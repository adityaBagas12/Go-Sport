<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $order = Order::factory();

        return [
            'invoice_number' => 'GS'.now()->format('Ymd').fake()->unique()->numerify('####'),
            'order_id' => $order,
            'payment_method' => 'midtrans',
            'channel' => null,
            'payment_status' => 'pending',
            'amount' => 262000,
            'bank_name' => null,
            'account_number' => null,
            'account_holder' => null,
            'instructions' => null,
            'qr_code' => null,
            'gateway' => 'midtrans',
            'gateway_reference' => null,
            'snap_token' => null,
            'payment_url' => null,
            'payment_type' => null,
            'transaction_status' => null,
            'fraud_status' => null,
            'midtrans_payload' => null,
            'expired_at' => now()->addHours(24),
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'processing',
        ]);
    }

    /**
     * Bayar di tempat: tanpa transaksi gateway dan tanpa batas bayar.
     */
    public function cod(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cod',
            'channel' => 'cod',
            'gateway' => null,
            'expired_at' => null,
            'instructions' => ['Bayar tunai kepada kurir saat pesanan diterima.'],
        ]);
    }

    /**
     * Transaksi Midtrans sudah dibuat dan customer sudah diarahkan ke Snap.
     */
    public function withSnapToken(): static
    {
        return $this->state(fn (array $attributes) => [
            'snap_token' => 'test-snap-token-'.fake()->uuid(),
            'payment_url' => 'https://app.sandbox.midtrans.com/snap/v2/transactions/test-snap-token/pay',
        ]);
    }

    /**
     * Nomor virtual account yang dikirim Midtrans lewat notification.
     */
    public function virtualAccount(string $bank = 'bca', string $number = '8808123456789'): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'bank_transfer',
            'bank_name' => $bank,
            'account_number' => $number,
            'account_holder' => 'GoSport',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'expired',
            'expired_at' => now()->subHour(),
        ]);
    }
}
