<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;
use Throwable;

/**
 * Pembungkus (wrapper) resmi SDK Midtrans PHP.
 *
 * Tanggung jawab kelas ini hanya hal teknis: menyetel konfigurasi SDK,
 * menyusun payload Snap, membuat transaksi, dan membaca status. Seluruh
 * keputusan bisnis (status invoice, pesanan, stok) ada di
 * {@see PaymentService}.
 */
class MidtransService
{
    /**
     * Batas nama `order_id` yang diterima Midtrans.
     */
    private const MIDTRANS_ORDER_ID_MAX = 50;

    /**
     * Midtrans hanya menerima huruf dan spasi pada nama depan/belakang.
     */
    private const NAME_ALLOWED_PATTERN = '/[^A-Za-z ]+/';

    /**
     * Mengatur konfigurasi global SDK.
     *
     * `Config` pada SDK bersifat static, jadi harus diset ulang sebelum
     * setiap panggilan API. Selama `is_production` false, semua request
     * diarahkan ke host Sandbox.
     */
    public function configure(): void
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$clientKey = (string) config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
    }

    /**
     * Midtrans siap dipakai bila Server Key sudah diisi.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key'));
    }

    public function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    /**
     * Membuat transaksi Snap dan mengembalikan respons API.
     *
     * Respons berisi `token` (Snap Token) dan `redirect_url` (halaman
     * pembayaran Midtrans).
     *
     * @return array<string, mixed>
     */
    public function createSnapTransaction(Payment $payment): array
    {
        $this->configure();

        $order = $payment->order()->with(['items', 'user'])->firstOrFail();

        return (array) Snap::createTransaction($this->payload($payment, $order));
    }

    /**
     * Meminta status transaksi terbaru langsung ke API Midtrans.
     *
     * Dipakai sebagai pemantau, bukan sebagai sumber kebenaran — status
     * final tetap berasal dari notification handler.
     */
    public function status(string $transactionId): ?object
    {
        if (blank($transactionId)) {
            return null;
        }

        $this->configure();

        try {
            return Transaction::status($transactionId);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Membaca notification Midtrans lalu memverifikasinya.
     *
     * `Midtrans\Notification` tidak sekadar membaca body request: kelas ini
     * melakukan request balik ke API Midtrans berdasarkan `transaction_id`,
     * sehingga isinya sudah terverifikasi dan tidak bisa dipalsukan pihak lain.
     *
     * @return array<string, mixed>
     */
    public function notificationPayload(): array
    {
        $this->configure();

        return (array) (new Notification)->getResponse();
    }

    /**
     * Menyusun payload transaksi Snap.
     *
     * `gross_amount` selalu diambil dari database, tidak pernah dari input
     * browser. Rincian item disusun agar amounts-nya selalu konsisten dengan
     * `gross_amount` (Midtrans menolak payload yang tidak balance).
     *
     * @return array<string, mixed>
     */
    public function payload(Payment $payment, Order $order): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $this->orderId($order),
                'gross_amount' => (int) $payment->amount,
            ],
            'item_details' => $this->itemDetails($order),
            'customer_details' => $this->customerDetails($order),
            'callbacks' => $this->callbacks($payment),
        ];

        if ($payment->expired_at) {
            $payload['expiry'] = [
                // Transaksi dibuat saat customer menekan tombol bayar, bukan saat
                // checkout, sehingga `start_time` memakai waktu sekarang. Bila
                // customer baru membayar jauh setelah invoice dibuat, nilai ini
                // tidak lagi berada di masa lalu.
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hour',
                // Durasi dibulatkan ke bawah lalu dinaikkan minimal 1 jam supaya
                // batas bayar di Midtrans tidak melewati `expired_at` di database.
                'duration' => max(1, (int) ceil(now()->diffInMinutes($payment->expired_at, false) / 60)),
            ];
        }

        return $payload;
    }

    /**
     * `order_id` yang dikirim ke Midtrans.
     *
     * Memakai `order_number` milik aplikasi sehingga nilainya unik, dapat
     * karakter apa pun, dan langsung bisa dicari kembali di database.
     */
    public function orderId(Order $order): string
    {
        return Str::limit($order->order_number, self::MIDTRANS_ORDER_ID_MAX, '');
    }

    /**
     * Rincian item, ongkir, dan diskon dalam bentuk yang diterima Midtrans.
     *
     * @return list<array<string, mixed>>
     */
    private function itemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => (string) $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => Str::limit($item->product_name, 50, ''),
            ];
        }

        if ($order->shipping_fee > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_fee,
                'quantity' => 1,
                'name' => 'Ongkos Kirim '.$order->courier.' '.$order->shipping_service,
            ];
        }

        if ($order->discount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -((int) $order->discount),
                'quantity' => 1,
                'name' => 'Diskon',
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    private function customerDetails(Order $order): array
    {
        $name = $this->cleanName($order->recipient_name);
        $email = $order->user?->email;
        $phone = $this->cleanPhone($order->recipient_phone);

        $details = [];

        if ($name !== '') {
            $details['first_name'] = $name;
        }

        if (filled($email)) {
            $details['email'] = $email;
        }

        if ($phone !== '') {
            $details['phone'] = $phone;
        }

        $shippingAddress = array_filter([
            'first_name' => $name,
            'phone' => $phone,
            'address' => $order->full_address,
            'city' => $order->city,
            'postal_code' => $this->cleanPostalCode($order->postal_code),
            'country_code' => 'IDN',
        ], fn ($value) => filled($value));

        if ($shippingAddress !== []) {
            $details['shipping_address'] = $shippingAddress;
        }

        return $details;
    }

    /**
     * URL tujuan setelah customer selesai di halaman Midtrans.
     *
     * `finish` dan `error` hanya menampilkan informasi, tidak pernah mengubah
     * status menjadi lunas. `unfinish` dikembalikan ke invoice supaya
     * customer bisa membayar ulang selama masa berlaku transaksi.
     *
     * @return array<string, string>
     */
    private function callbacks(Payment $payment): array
    {
        return [
            'finish' => $this->publicUrl('midtrans.finish'),
            'unfinish' => $this->publicUrl('payment.invoice', [
                'invoiceNumber' => $payment->invoice_number,
            ]),
            'error' => $this->publicUrl('midtrans.error'),
        ];
    }

    private function publicUrl(string $routeName, array $parameters = []): string
    {
        $path = route($routeName, $parameters, absolute: false);

        $base = config('gosport.payment.public_url') ?: config('app.url');

        return rtrim((string) $base, '/').'/'.ltrim($path, '/');
    }

    private function cleanName(?string $name): string
    {
        return trim((string) preg_replace(self::NAME_ALLOWED_PATTERN, ' ', (string) $name));
    }

    private function cleanPhone(?string $phone): string
    {
        return trim((string) preg_replace('/[^0-9+]/', '', (string) $phone));
    }

    private function cleanPostalCode(?string $postalCode): string
    {
        return trim((string) preg_replace('/[^0-9]/', '', (string) $postalCode));
    }
}
