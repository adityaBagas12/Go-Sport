<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_review(): void
    {
        $product = Product::factory()->create(['rating' => 0]);
        $order = Order::factory()->create();

        $response = $this->post(route('reviews.store'), [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Bagus banget!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_cannot_review_product_they_did_not_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['rating' => 0]);
        $otherProduct = Product::factory()->create(['rating' => 0]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'status' => 'selesai',
        ]);
        $order->items()->create([
            'product_id' => $otherProduct->id,
            'product_name' => $otherProduct->name,
            'price' => $otherProduct->price,
            'quantity' => 1,
            'subtotal' => $otherProduct->price,
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Barang keren',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_cannot_review_unpaid_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['rating' => 0]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_status' => 'unpaid',
            'status' => 'menunggu_pembayaran',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Belum bayar kok bisa review',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_verified_buyer_can_submit_review_and_product_rating_updates(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['rating' => 0]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_status' => 'paid',
            'status' => 'selesai',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Kualitas sangat memuaskan dan nyaman dipakai!',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Kualitas sangat memuaskan dan nyaman dipakai!',
        ]);

        $product->refresh();
        $this->assertEquals(5.0, $product->rating);
    }
}
