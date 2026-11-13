<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMidtransService;
use Tests\TestCase;

class CheckoutSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.client_key' => 'test-client-key',
            'services.midtrans.is_production' => false,
        ]);

        $this->app->instance(MidtransService::class, new FakeMidtransService);
    }

    /*
    |--------------------------------------------------------------------------
    | Tampilan keranjang
    |--------------------------------------------------------------------------
    */

    public function test_cart_preselects_every_item_for_backwards_compatible_checkout(): void
    {
        $user = User::factory()->create();
        $this->seedCart($user, 2);
        $this->seedCart($user);

        $response = $this->actingAs($user)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Pilih Semua');
        $response->assertSee('form="checkout-form"', false);
        $this->assertSame(2, substr_count($response->getContent(), 'data-cart-item'));
    }

    public function test_checkout_page_shows_only_the_selected_items(): void
    {
        [$user, $a, $b, $c] = $this->cartWithThreeProducts();

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$a->id, $c->id]]))
            ->assertOk()
            ->assertSee($a->product->name)
            ->assertSee($c->product->name)
            ->assertDontSee($b->product->name);
    }

    public function test_checkout_page_summarises_only_the_selected_subtotal(): void
    {
        [$user, $a, $b, $c] = $this->cartWithThreeProducts();
        $expected = $a->subtotal + $c->subtotal;

        $response = $this->actingAs($user)->get(route('checkout.index', ['items' => [$a->id, $c->id]]));

        $response->assertOk();
        $response->assertSee($this->rupiah($expected));
        $this->assertNotSame(
            $this->rupiah($a->subtotal + $b->subtotal + $c->subtotal),
            $this->rupiah($expected)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guardrails
    |--------------------------------------------------------------------------
    */

    public function test_checkout_redirects_to_cart_when_nothing_is_selected(): void
    {
        $user = User::factory()->create();
        $this->seedCart($user);

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');
    }

    public function test_checkout_ignores_cart_items_owned_by_someone_else(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mine = $this->seedCart($user);
        $theirs = $this->seedCart($other);

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$theirs->id]]))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('cart_items', ['id' => $mine->id]);
    }

    public function test_buying_now_only_checks_out_the_product_that_was_just_added(): void
    {
        $user = User::factory()->create();
        $existing = $this->seedCart($user);
        $new = Product::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('cart.add'), [
                'product_id' => $new->id,
                'quantity' => 1,
                'redirect' => 'checkout',
            ]);

        $added = CartItem::where('product_id', $new->id)->firstOrFail();

        $response->assertRedirect(route('checkout.index', ['items' => [$added->id]]));

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$added->id]]))
            ->assertOk()
            ->assertSee($new->name)
            ->assertDontSee($existing->product->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Penyimpanan pesanan
    |--------------------------------------------------------------------------
    */

    public function test_only_the_selected_items_are_ordered_and_removed_from_the_cart(): void
    {
        [$user, $a, $b, $c] = $this->cartWithThreeProducts();

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$a->id, $c->id]]))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->payload([$a->id, $c->id]))
            ->assertSessionHasNoErrors();

        $order = Order::firstOrFail();

        $this->assertSame(2, $order->items()->count());
        $this->assertEqualsCanonicalizing(
            [$a->product_id, $c->product_id],
            $order->items()->pluck('product_id')->all()
        );
        $this->assertDatabaseMissing('cart_items', ['id' => $a->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $c->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $b->id]);
    }

    public function test_checkout_uses_the_selected_quantity_from_the_database(): void
    {
        $user = User::factory()->create();
        $item = CartItem::factory()->for($user)->create(['quantity' => 5]);
        $expectedSubtotal = $item->product->price * 5;

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$item->id]]))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->payload([$item->id]))
            ->assertSessionHasNoErrors();

        $order = Order::firstOrFail();
        $orderItem = $order->items()->firstOrFail();

        $this->assertSame(5, $orderItem->quantity);
        $this->assertSame($expectedSubtotal, $orderItem->subtotal);
        $this->assertSame($expectedSubtotal, $order->subtotal);
        $this->assertSame($order->total, Payment::firstOrFail()->amount);
        $this->assertSame(
            $expectedSubtotal + $order->shipping_fee,
            $order->total
        );
    }

    public function test_checkout_uses_shipping_fee_for_the_delivery_region(): void
    {
        $javaUser = User::factory()->create();
        $javaItem = CartItem::factory()->for($javaUser)->create();

        $this->actingAs($javaUser)
            ->post(route('checkout.store'), $this->payload([$javaItem->id]));

        $papuaUser = User::factory()->create();
        $papuaItem = CartItem::factory()->for($papuaUser)->create();

        $this->actingAs($papuaUser)
            ->post(route('checkout.store'), array_merge($this->payload([$papuaItem->id]), [
                'province' => 'Papua',
            ]));

        $javaOrder = Order::where('user_id', $javaUser->id)->firstOrFail();
        $papuaOrder = Order::where('user_id', $papuaUser->id)->firstOrFail();

        $this->assertSame(12_000, $javaOrder->shipping_fee);
        $this->assertSame(27_000, $papuaOrder->shipping_fee);
    }

    public function test_checkout_ignores_a_quantity_smuggled_in_from_the_client(): void
    {
        $user = User::factory()->create();
        $item = CartItem::factory()->for($user)->create(['quantity' => 2]);

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$item->id]]))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->payload([$item->id]) + [
                'quantity' => [$item->id => 99],
                'subtotal' => 1,
                'total' => 1,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Order::firstOrFail()->items()->firstOrFail()->quantity);
    }

    public function test_checkout_rejects_a_cart_item_owned_by_someone_else(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mine = $this->seedCart($user);
        $theirs = $this->seedCart($other);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->payload([$theirs->id]))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertDatabaseHas('cart_items', ['id' => $mine->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $theirs->id]);
    }

    public function test_checkout_still_reduces_stock_only_for_the_selected_items(): void
    {
        [$user, $a, $b] = $this->cartWithThreeProducts();
        $aStock = $a->product->stock;
        $bStock = $b->product->stock;

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$a->id]]))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->payload([$a->id]))
            ->assertSessionHasNoErrors();

        $this->assertSame($aStock - $a->quantity, $a->product->fresh()->stock);
        $this->assertSame($bStock, $b->product->fresh()->stock);
    }

    public function test_clearing_the_selection_after_checkout_does_not_affect_the_next_visit(): void
    {
        [$user, $a, $b] = $this->cartWithThreeProducts();

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$a->id]]))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->payload([$a->id]))
            ->assertSessionHasNoErrors();

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$b->id]]))
            ->assertOk()
            ->assertSee($b->product->name)
            ->assertDontSee($a->product->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Voucher
    |--------------------------------------------------------------------------
    */

    public function test_voucher_discount_is_calculated_from_the_selected_subtotal(): void
    {
        $user = User::factory()->create();
        $big = CartItem::factory()->for($user)->create(['quantity' => 1]);
        Coupon::create([
            'code' => 'HEMAT50',
            'type' => 'percent',
            'value' => 50,
            'min_order' => 1_000_000,
            'is_active' => true,
        ]);

        $big->product->update(['price' => 3_000_000]);

        $this->actingAs($user)
            ->withSession(['cart_coupon' => 'HEMAT50'])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('1.500.000');

        $this->actingAs($user)
            ->get(route('checkout.index', ['items' => [$big->id]]))
            ->assertOk()
            ->assertSee('1.500.000');
    }

    public function test_voucher_is_ignored_when_the_selected_subtotal_is_below_the_minimum(): void
    {
        $user = User::factory()->create();
        $item = CartItem::factory()->for($user)->create(['quantity' => 1]);
        $item->product->update(['price' => 50_000]);
        Coupon::create([
            'code' => 'HEMAT50',
            'type' => 'percent',
            'value' => 50,
            'min_order' => 1_000_000,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['cart_coupon' => 'HEMAT50'])
            ->get(route('checkout.index', ['items' => [$item->id]]))
            ->assertOk()
            ->assertDontSee('HEMAT50');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @return array{0: User, 1: CartItem, 2: CartItem, 3: CartItem}
     */
    private function cartWithThreeProducts(): array
    {
        $user = User::factory()->create();

        return [
            $user,
            CartItem::factory()->for($user)->create(['quantity' => 2]),
            CartItem::factory()->for($user)->create(['quantity' => 1]),
            CartItem::factory()->for($user)->create(['quantity' => 3]),
        ];
    }

    private function seedCart(User $user, int $quantity = 1): CartItem
    {
        return CartItem::factory()->for($user)->create(['quantity' => $quantity]);
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<string, mixed>
     */
    private function payload(array $ids): array
    {
        return [
            'items' => $ids,
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Menteng',
            'postal_code' => '10110',
            'full_address' => 'Jl. Merdeka No. 1',
            'courier' => 'jnt',
            'service' => 'EZ',
            'payment_method' => 'midtrans',
        ];
    }

    private function rupiah(int $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
