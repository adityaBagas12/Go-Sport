<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMidtransService;
use Tests\TestCase;

class CartQuantityUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => 'test-server-key',
            'services.midtrans.client_key' => 'test-client-key',
            'services.midtrans.is_production' => false,
        ]);

        $this->app->instance(MidtransService::class, new FakeMidtransService);

        $this->user = User::factory()->create();
    }

    public function test_cart_page_renders_an_update_form_that_the_route_actually_accepts(): void
    {
        $item = $this->seedCart(quantity: 1);

        $content = $this->actingAs($this->user)
            ->get(route('cart.index'))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, preg_match('/<form[^>]*action="[^"]*\/keranjang\/update"[^>]*method="POST"/i', $content));
        $this->assertStringContainsString('name="_method" value="PUT"', $content);
        $this->assertStringContainsString('name="quantity['.$item->id.']"', $content);
    }

    public function test_the_method_the_form_spoofs_is_accepted_by_the_route(): void
    {
        $item = $this->seedCart(quantity: 1);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->post(route('cart.update'), ['_method' => 'PUT', 'quantity' => [$item->id => 5]])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('success');

        $this->assertSame(5, $item->fresh()->quantity);
    }

    public function test_updating_quantity_from_one_to_five_persists_to_the_database(): void
    {
        $item = $this->seedCart(quantity: 1);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('success');

        $this->assertSame(5, $item->fresh()->quantity);
    }

    public function test_quantity_five_updates_subtotal_and_total_on_the_cart_page(): void
    {
        $item = $this->seedCart(quantity: 1, price: 189_000);

        $this->actingAs($this->user)
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]]);

        $item->refresh();

        $this->assertSame(945_000, $item->subtotal);

        $content = $this->actingAs($this->user)
            ->get(route('cart.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Rp 945.000', $content);
        $this->assertStringContainsString('value="5"', $content);
        $this->assertStringContainsString('data-cart-count>5<', $content);
    }

    public function test_checkout_reads_the_latest_quantity_from_the_cart(): void
    {
        $item = $this->seedCart(quantity: 1, price: 189_000);

        $this->actingAs($this->user)
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]]);

        $this->actingAs($this->user)
            ->get(route('checkout.index', ['items' => [$item->id]]))
            ->assertOk()
            ->assertSee('5x')
            ->assertSee('Rp 945.000');
    }

    public function test_order_items_store_quantity_five_and_the_matching_total(): void
    {
        $item = $this->seedCart(quantity: 1, price: 189_000);

        $this->actingAs($this->user)
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]]);

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->checkoutPayload($item));

        $order = Order::firstOrFail();
        $orderItem = $order->items->first();

        $this->assertSame(5, $orderItem->quantity);
        $this->assertSame(189_000, $orderItem->price);
        $this->assertSame(945_000, $orderItem->subtotal);
        $this->assertSame(945_000, $order->subtotal);
        $this->assertSame(945_000 + $order->shipping_fee, $order->total);
        $this->assertSame(0, CartItem::where('user_id', $this->user->id)->count());
    }

    public function test_stock_is_decremented_by_the_updated_quantity(): void
    {
        $item = $this->seedCart(quantity: 1);
        $product = $item->product;
        $initialStock = $product->stock;

        $this->actingAs($this->user)
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]]);

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->checkoutPayload($item));

        $product->refresh();

        $this->assertSame($initialStock - 5, $product->stock);
        $this->assertSame(5, $product->sold);
    }

    public function test_quantity_can_be_lowered_back_to_one(): void
    {
        $item = $this->seedCart(quantity: 5);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 1]])
            ->assertRedirect(route('cart.index'));

        $this->assertSame(1, $item->fresh()->quantity);

        $this->actingAs($this->user)
            ->post(route('checkout.store'), $this->checkoutPayload($item));

        $this->assertSame(1, Order::firstOrFail()->items->first()->quantity);
    }

    public function test_quantities_of_two_and_five_are_both_persisted(): void
    {
        foreach ([2, 5] as $quantity) {
            $item = $this->seedCart(quantity: 1);

            $this->actingAs($this->user)
                ->put(route('cart.update'), ['quantity' => [$item->id => $quantity]]);

            $this->assertSame($quantity, $item->fresh()->quantity);
        }
    }

    public function test_quantity_below_one_is_rejected(): void
    {
        $item = $this->seedCart(quantity: 3);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 0]])
            ->assertSessionHasErrors('quantity.'.$item->id);

        $this->assertSame(3, $item->fresh()->quantity);
    }

    public function test_quantity_above_stock_is_rejected_and_nothing_is_saved(): void
    {
        $item = $this->seedCart(quantity: 1, stock: 3);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertSame(1, $item->fresh()->quantity);
    }

    public function test_non_numeric_quantity_is_rejected(): void
    {
        $item = $this->seedCart(quantity: 2);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 'abc']])
            ->assertSessionHasErrors('quantity.'.$item->id);

        $this->assertSame(2, $item->fresh()->quantity);
    }

    public function test_another_users_cart_item_cannot_be_updated(): void
    {
        $item = CartItem::factory()->create(['quantity' => 1]);

        $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]])
            ->assertNotFound();

        $this->assertSame(1, $item->fresh()->quantity);
    }

    public function test_guests_cannot_update_the_cart(): void
    {
        $this->put(route('cart.update'), ['quantity' => [1 => 5]])
            ->assertRedirect(route('login'));
    }

    public function test_the_update_route_never_returns_method_not_allowed(): void
    {
        $item = $this->seedCart(quantity: 1);

        $response = $this->actingAs($this->user)
            ->from(route('cart.index'))
            ->put(route('cart.update'), ['quantity' => [$item->id => 5]]);

        $this->assertNotSame(405, $response->getStatusCode());
        $this->assertSame(5, $item->fresh()->quantity);
    }

    private function seedCart(int $quantity = 1, int $price = 100_000, int $stock = 50): CartItem
    {
        $product = Product::factory()->create(['price' => $price, 'stock' => $stock]);

        return CartItem::factory()->for($this->user)->for($product)->create(['quantity' => $quantity]);
    }

    /**
     * @return array<string, mixed>
     */
    private function checkoutPayload(?CartItem $item = null): array
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
            'payment_method' => 'cod',
        ];
    }
}
