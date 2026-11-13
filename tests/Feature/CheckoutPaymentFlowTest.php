<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\MidtransService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMidtransService;
use Tests\TestCase;

class CheckoutPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private FakeMidtransService $midtrans;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.client_key' => 'test-client-key',
            'services.midtrans.is_production' => false,
        ]);

        $this->midtrans = new FakeMidtransService;
        $this->app->instance(MidtransService::class, $this->midtrans);
    }

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    */

    public function test_midtrans_checkout_creates_an_invoice_without_calling_midtrans(): void
    {
        $user = User::factory()->create();
        $item = $this->seedCart($user);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload('midtrans', $item))
            ->assertRedirect(route('payment.invoice', Order::firstOrFail()->payment->invoice_number));

        $payment = Payment::firstOrFail();

        $this->assertSame('midtrans', $payment->payment_method);
        $this->assertSame('pending', $payment->payment_status);
        $this->assertSame('midtrans', $payment->gateway);
        $this->assertNotNull($payment->expired_at);

        // Transaksi hanya dibuat saat customer menekan tombol bayar.
        $this->assertSame([], $this->midtrans->createdPayloads);
        $this->assertNull($payment->snap_token);
    }

    public function test_cod_checkout_creates_an_invoice_with_instructions_and_no_expiry(): void
    {
        $user = User::factory()->create();
        $item = $this->seedCart($user);

        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload('cod', $item));

        $payment = Payment::firstOrFail();

        $this->assertSame('cod', $payment->payment_method);
        $this->assertNull($payment->gateway);
        $this->assertNull($payment->expired_at);
        $this->assertNotEmpty($payment->instructions);

        // COD tidak pernah menyentuh Midtrans.
        $this->assertSame([], $this->midtrans->createdPayloads);
    }

    public function test_checkout_rejects_an_unknown_payment_method(): void
    {
        $user = User::factory()->create();
        $this->seedCart($user);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload('virtual_account'))
            ->assertSessionHasErrors('payment_method');

        $this->assertSame(0, Order::count());
        $this->assertSame(0, Payment::count());
    }

    public function test_checkout_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);
        CartItem::factory()->for($user)->for($product)->create(['quantity' => 5]);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload('midtrans'))
            ->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(0, Payment::count());
    }

    public function test_checkout_clears_the_cart_and_reduces_stock(): void
    {
        $user = User::factory()->create();
        $cartItem = $this->seedCart($user, quantity: 2);
        $stockBefore = $cartItem->product->stock;

        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload('midtrans', $cartItem));

        $product = Order::firstOrFail()->items->first()->product;

        $this->assertSame(0, $user->cartItems()->count());
        $this->assertSame($stockBefore - 2, $product->fresh()->stock);
        $this->assertSame(2, $product->fresh()->sold);
    }

    /*
    |--------------------------------------------------------------------------
    | Pembayaran
    |--------------------------------------------------------------------------
    */

    public function test_pay_creates_a_snap_transaction_and_redirects_to_midtrans(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->actingAs($payment->order->user)
            ->post(route('payment.pay', $payment->invoice_number))
            ->assertRedirect($this->midtrans->snapResponse['redirect_url']);

        $payment->refresh();

        $this->assertSame('fake-snap-token', $payment->snap_token);
        $this->assertSame($this->midtrans->snapResponse['redirect_url'], $payment->payment_url);
        $this->assertNotEmpty($payment->instructions);
        $this->assertCount(1, $this->midtrans->createdPayloads);
    }

    public function test_snap_payload_uses_database_values_and_order_number(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->actingAs($payment->order->user)
            ->post(route('payment.pay', $payment->invoice_number));

        $payload = $this->midtrans->createdPayloads[0];

        $this->assertSame($payment->order->order_number, $payload['transaction_details']['order_id']);
        $this->assertSame($payment->amount, $payload['transaction_details']['gross_amount']);
        $this->assertNotEmpty($payload['item_details']);
        $this->assertSame(
            $payload['transaction_details']['gross_amount'],
            collect($payload['item_details'])->sum(fn (array $item) => $item['price'] * $item['quantity'])
        );
        $this->assertArrayHasKey('expiry', $payload);
        $this->assertNotEmpty($payload['callbacks']['finish']);
        $this->assertNotEmpty($payload['callbacks']['unfinish']);
        $this->assertNotEmpty($payload['callbacks']['error']);
    }

    public function test_pay_is_idempotent_and_never_creates_two_snap_transactions(): void
    {
        $payment = $this->placeOrder('midtrans');
        $user = $payment->order->user;

        $this->actingAs($user)->post(route('payment.pay', $payment->invoice_number));
        $this->actingAs($user)->post(route('payment.pay', $payment->invoice_number));
        $this->actingAs($user)->post(route('payment.pay', $payment->invoice_number));

        $this->assertCount(1, $this->midtrans->createdPayloads);
        $this->assertSame('fake-snap-token', $payment->fresh()->snap_token);
    }

    public function test_customer_cannot_pay_someone_elses_invoice(): void
    {
        $payment = $this->placeOrder('midtrans');

        // 404 (bukan 403) agar keberadaan invoice milik orang lain tidak bocor.
        $this->actingAs(User::factory()->create())
            ->post(route('payment.pay', $payment->invoice_number))
            ->assertNotFound();

        $this->assertSame([], $this->midtrans->createdPayloads);
    }

    public function test_pay_is_rejected_when_midtrans_is_not_configured(): void
    {
        $payment = $this->placeOrder('midtrans');

        config(['services.midtrans.server_key' => null]);

        $this->actingAs($payment->order->user)
            ->post(route('payment.pay', $payment->invoice_number))
            ->assertSessionHas('error');

        $this->assertNull($payment->fresh()->snap_token);
    }

    public function test_pay_is_rejected_once_the_invoice_has_expired(): void
    {
        $payment = $this->placeOrder('midtrans');
        $payment->update(['expired_at' => now()->subMinute()]);

        $this->actingAs($payment->order->user)
            ->post(route('payment.pay', $payment->invoice_number))
            ->assertSessionHas('error');

        $this->assertSame([], $this->midtrans->createdPayloads);
    }

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    */

    public function test_settlement_notification_marks_payment_and_order_as_paid(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, [
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ])->assertOk()->assertJson(['payment_status' => 'paid']);

        $payment->refresh();

        $this->assertSame('paid', $payment->payment_status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame('txn-test-1', $payment->gateway_reference);
        $this->assertSame('paid', $payment->order->fresh()->payment_status);
    }

    public function test_notification_saves_the_method_chosen_by_the_customer_at_midtrans(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, [
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'bank' => 'bca',
            'va_number' => '8808123456789',
        ])->assertOk();

        $payment->refresh();

        $this->assertSame('bank_transfer', $payment->payment_type);
        $this->assertSame('bca', $payment->bank_name);
        $this->assertSame('8808123456789', $payment->account_number);
    }

    public function test_pending_notification_keeps_the_invoice_unpaid(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, [
            'transaction_status' => 'pending',
            'payment_type' => 'bank_transfer',
        ])->assertOk()->assertJson(['payment_status' => 'processing']);

        $this->assertSame('processing', $payment->fresh()->payment_status);
        $this->assertNull($payment->fresh()->paid_at);
    }

    public function test_capture_with_fraud_challenge_is_not_marked_as_paid(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, [
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
        ])->assertOk()->assertJson(['payment_status' => 'processing']);

        $this->assertSame('processing', $payment->fresh()->payment_status);
    }

    public function test_capture_with_fraud_deny_is_marked_as_denied(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, [
            'transaction_status' => 'capture',
            'fraud_status' => 'deny',
        ])->assertOk()->assertJson(['payment_status' => 'denied']);

        $this->assertSame('denied', $payment->fresh()->payment_status);
        $this->assertNull($payment->fresh()->paid_at);
    }

    public function test_expired_notification_does_not_release_stock(): void
    {
        $payment = $this->placeOrder('midtrans');
        $stockBefore = $payment->order->items->first()->product->fresh()->stock;

        $this->sendNotification($payment, ['transaction_status' => 'expire'])->assertOk();

        $this->assertSame('expired', $payment->fresh()->payment_status);
        $this->assertSame($stockBefore, $payment->order->items->first()->product->fresh()->stock);
    }

    public function test_notification_with_a_mismatched_amount_is_never_marked_as_paid(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, [
            'transaction_status' => 'settlement',
            'gross_amount' => (string) ($payment->amount - 1000),
        ])->assertOk();

        $this->assertSame('pending', $payment->fresh()->payment_status);
        $this->assertNull($payment->fresh()->paid_at);
    }

    public function test_repeated_notification_is_idempotent(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->sendNotification($payment, ['transaction_status' => 'settlement'])->assertOk();
        $firstPaidAt = $payment->fresh()->paid_at;

        $this->sendNotification($payment, ['transaction_status' => 'pending'])->assertOk();
        $this->sendNotification($payment, ['transaction_status' => 'settlement'])->assertOk();

        $this->assertSame('paid', $payment->fresh()->payment_status);
        $this->assertEquals($firstPaidAt, $payment->fresh()->paid_at);
    }

    public function test_notification_for_an_unknown_order_is_not_found(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->midtrans->notificationPayload = $this->notificationPayload($payment, [
            'order_id' => 'ORDER-NOT-EXISTS',
        ]);

        $this->postJson(route('midtrans.notification'))->assertNotFound();
    }

    public function test_notification_is_rejected_when_midtrans_cannot_verify_it(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->app->instance(
            MidtransService::class,
            new class extends MidtransService
            {
                public function notificationPayload(): array
                {
                    throw new \RuntimeException('Invalid notification signature.');
                }
            }
        );

        $this->postJson(route('midtrans.notification'))->assertForbidden();

        $this->assertSame('pending', $payment->fresh()->payment_status);
    }

    /*
    |--------------------------------------------------------------------------
    | Halaman
    |--------------------------------------------------------------------------
    */

    public function test_finish_page_does_not_mark_the_invoice_as_paid(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->actingAs($payment->order->user)
            ->get(route('midtrans.finish', ['order_id' => $payment->order->order_number]))
            ->assertOk();

        $this->assertSame('pending', $payment->fresh()->payment_status);
    }

    public function test_invoice_page_is_only_visible_to_the_order_owner(): void
    {
        $payment = $this->placeOrder('midtrans');

        $this->actingAs($payment->order->user)
            ->get(route('payment.invoice', $payment->invoice_number))
            ->assertOk();

        $this->actingAs(User::factory()->create())
            ->get(route('payment.invoice', $payment->invoice_number))
            ->assertNotFound();
    }

    public function test_invoice_shows_the_midtrans_method_chosen_by_the_customer(): void
    {
        $payment = $this->placeOrder('midtrans');
        $payment->update([
            'payment_type' => 'qris',
            'transaction_status' => 'pending',
        ]);

        $this->actingAs($payment->order->user)
            ->get(route('payment.invoice', $payment->invoice_number))
            ->assertOk()
            ->assertSee('QRIS');
    }

    public function test_invoice_does_not_offer_a_pay_button_for_an_expired_invoice(): void
    {
        $payment = $this->placeOrder('midtrans');
        $payment->update(['payment_status' => 'expired']);

        $this->actingAs($payment->order->user)
            ->get(route('payment.invoice', $payment->invoice_number))
            ->assertOk()
            ->assertDontSee(route('payment.pay', $payment->invoice_number));
    }

    /*
    |--------------------------------------------------------------------------
    | Invoice
    |--------------------------------------------------------------------------
    */

    public function test_creating_invoice_twice_returns_the_same_invoice(): void
    {
        $order = Order::factory()->create(['payment_method' => 'midtrans']);
        $service = app(PaymentService::class);

        $first = $service->createForOrder($order);
        $second = $service->createForOrder($order->fresh());

        $this->assertSame($first->invoice_number, $second->invoice_number);
        $this->assertSame(1, Payment::count());
    }

    public function test_invoice_numbers_are_unique_across_many_orders(): void
    {
        $service = app(PaymentService::class);

        $numbers = collect(range(1, 25))
            ->map(fn () => $service->createForOrder(Order::factory()->create([
                'payment_method' => 'midtrans',
            ]))->invoice_number);

        $this->assertCount(25, $numbers->unique());
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private function placeOrder(string $paymentMethod): Payment
    {
        $user = User::factory()->create();
        $item = $this->seedCart($user);

        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload($paymentMethod, $item));

        return Payment::firstOrFail();
    }

    private function seedCart(User $user, int $quantity = 1): CartItem
    {
        return CartItem::factory()->for($user)->create(['quantity' => $quantity]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function notificationPayload(Payment $payment, array $overrides = []): array
    {
        return array_merge([
            'order_id' => $payment->order->order_number,
            'transaction_id' => 'txn-test-1',
            'transaction_status' => 'settlement',
            'gross_amount' => (string) $payment->amount,
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function sendNotification(Payment $payment, array $overrides = [])
    {
        $this->midtrans->notificationPayload = $this->notificationPayload($payment, $overrides);

        return $this->postJson(route('midtrans.notification'));
    }

    /**
     * @return array<string, mixed>
     */
    private function checkoutPayload(string $paymentMethod, ?CartItem $item = null): array
    {
        return [
            'items' => $item ? [$item->id] : [],
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Menteng',
            'postal_code' => '10110',
            'full_address' => 'Jl. Merdeka No. 1',
            'courier' => 'jnt',
            'service' => 'EZ',
            'payment_method' => $paymentMethod,
        ];
    }
}
