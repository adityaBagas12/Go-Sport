<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(100, 1000) * 1000;
        $shippingFee = 12000;
        $discount = 0;
        $total = $subtotal - $discount + $shippingFee;

        return [
            'user_id' => User::factory(),
            'order_number' => 'GS-'.now()->format('ymdHis').Str::upper(Str::random(4)),
            'status' => 'menunggu_pembayaran',
            'courier' => 'J&T Express',
            'shipping_service' => 'EZ',
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => 'midtrans',
            'payment_status' => 'unpaid',
            'recipient_name' => fake()->name(),
            'recipient_phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Menteng',
            'postal_code' => '10110',
            'full_address' => fake()->address(),
            'note' => null,
        ];
    }
}
