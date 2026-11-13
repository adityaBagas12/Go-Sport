<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Batas percobaan pembuatan nomor invoice sebelum menyerah.
     */
    private const INVOICE_NUMBER_ATTEMPTS = 5;

    /**
     * Lebar nomor urut pada nomor invoice, mis. 0001.
     */
    private const INVOICE_SEQUENCE_WIDTH = 4;

    /**
     * Nama unique index yang menjamin satu invoice per pesanan.
     */
    private const ORDER_UNIQUE_KEY = 'payments_order_id_unique';

    /**
     * Pemetaan `transaction_status` Midtrans ke status internal aplikasi.
     *
     * `capture` tidak selalu berarti berhasil: untuk kartu kredit, status
     * `fraud_status` menentukan apakah dana benar-benar diterima.
     *
     * @var array<string, string>
     */
    private const STATUS_MAP = [
        'settlement' => 'paid',
        'capture' => 'paid',
        'pending' => 'processing',
        'deny' => 'denied',
        'cancel' => 'cancelled',
        'expire' => 'expired',
        'failure' => 'failed',
        'refund' => 'refunded',
        'partial_refund' => 'refunded',
    ];

    public function __construct(private PaymentGatewayRegistry $gateways) {}

    /**
     * Membuat invoice (transaksi pembayaran) untuk sebuah pesanan.
     *
     * Operasional ini idempoten: satu pesanan hanya boleh memiliki satu
     * invoice. Memanggilnya berulang kali - misalnya karena customer
     * me-refresh halaman - akan mengembalikan invoice yang sama, bukan
     * membuat nomor invoice baru.
     *
     * Transaksi di payment gateway BELUM dibuat di sini. Pembuatan transaksi
     * Midtrans dilakukan saat customer menekan "Bayar Sekarang".
     */
    public function createForOrder(Order $order): Payment
    {
        $order->loadMissing('items');

        if ($existing = $order->payment()->first()) {
            return $existing;
        }

        $gateway = $this->gateways->for($order->payment_method);

        return $this->storeInvoice($order, $gateway);
    }

    /**
     * Memastikan instruksi pembayaran sudah siap untuk invoice.
     *
     * Hanya channel yang membutuhkan transaksi di payment gateway yang
     * melakukan panggilan API. Panggilan berulang aman karena
     * {@see PaymentGatewayContract::isChargeReady()} mencegah pembuatan
     * transaksi ganda.
     */
    public function ensureCharge(Payment $payment): Payment
    {
        $gateway = $this->gateways->for($payment->payment_method);

        if ($gateway->isChargeReady($payment)) {
            return $payment;
        }

        $instruction = $gateway->createCharge($payment);

        $payment->update([
            'channel' => $instruction->channel,
            'instructions' => $instruction->steps,
        ]);

        return $payment->fresh();
    }

    /**
     * Status `paid` setelah ada konfirmasi resmi.
     *
     * Dipakai oleh notification handler Midtrans, dan oleh verifikasi admin
     * untuk pesanan COD yang pembayarannya diterima di tempat.
     */
    public function markAsPaid(Payment $payment): Payment
    {
        if ($payment->isPaid()) {
            return $payment;
        }

        $payment->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $payment->order()->update([
            'payment_status' => 'paid',
            'status' => 'diproses',
        ]);

        return $payment->fresh();
    }

    /**
     * Memproses notification/webhook dari Midtrans.
     *
     * Parameter `$payload` harus sudah diverifikasi oleh Midtrans sebelum
     * masuk ke sini: `MidtransService::notification()` membungkus
     * `Midtrans\Notification` yang melakukan request balik ke API Midtrans
     * berdasarkan `transaction_id`, sehingga isinya tidak bisa dipalsukan.
     * Verifikasi sengaja tidak dilakukan di dalam kelas ini agar aturan
     * bisnis tidak terikat pada kelas SDK dan tetap bisa diuji.
     *
     * Pemrosesan bersifat idempoten: notifikasi yang dikirim berulang kali
     * tidak akan membuat invoice kedua maupun menurunkan status yang sudah
     * final.
     *
     * @param  array<string, mixed>  $payload
     * @return array{payment: ?Payment, changed: bool}
     */
    public function handleMidtransNotification(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $transactionId = (string) ($payload['transaction_id'] ?? '');

        if ($orderId === '' || $transactionId === '') {
            Log::warning('Midtrans notification tanpa order_id atau transaction_id.', $payload);

            return ['payment' => null, 'changed' => false];
        }

        $order = Order::where('order_number', $orderId)->first();

        if (! $order) {
            Log::warning('Midtrans notification untuk order yang tidak dikenal.', [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
            ]);

            return ['payment' => null, 'changed' => false];
        }

        $payment = $order->payment()->first();

        if (! $payment) {
            Log::warning('Order Midtrans tidak memiliki invoice pembayaran.', ['order_id' => $orderId]);

            return ['payment' => null, 'changed' => false];
        }

        if (! $this->amountMatches($payment, $payload)) {
            Log::error('Nominal Midtrans tidak cocok dengan invoice, pembayaran tidak ditandai lunas.', [
                'order_id' => $orderId,
                'midtrans_gross_amount' => $payload['gross_amount'] ?? null,
                'invoice_amount' => $payment->amount,
            ]);

            $this->storeGatewayData($payment, $payload);

            return ['payment' => $payment->fresh(), 'changed' => false];
        }

        $this->storeGatewayData($payment, $payload);

        $status = $this->resolveStatus($payload);

        // Notifikasi ganda atau notifikasi yang datang tidak berurutan tidak
        // boleh menurunkan status yang sudah final.
        if ($payment->isFinal() && $status !== 'refunded') {
            return ['payment' => $payment->fresh(), 'changed' => false];
        }

        if ($status === 'paid') {
            $this->markAsPaid($payment);

            return ['payment' => $payment->fresh(), 'changed' => true];
        }

        $payment->update(['payment_status' => $status]);

        // Stok sengaja TIDAK dilepas otomatis di sini. Stok hanya dilepas oleh
        // pembatalan pesanan (customer atau admin), sehingga tidak mungkin
        // terjadi pelepasan stok dua kali ketika notifikasi `expire` datang
        // bersamaan dengan aksi pembatalan.

        return ['payment' => $payment->fresh(), 'changed' => true];
    }

    /**
     * Menyimpan invoice dengan nomor yang dijamin unik oleh unique index.
     *
     * Penomoran berurutan dibaca dari database sehingga bisa saja meleset
     * bila dua checkout berjalan bersamaan. Karena itu pelanggaran unique
     * index dianggap normal: nomor dihitung ulang lalu dicoba lagi.
     *
     * @throws PaymentException
     */
    private function storeInvoice(Order $order, PaymentGatewayContract $gateway): Payment
    {
        for ($attempt = 1; $attempt <= self::INVOICE_NUMBER_ATTEMPTS; $attempt++) {
            try {
                $payment = $order->payment()->create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'payment_method' => $order->payment_method,
                    'payment_status' => 'pending',
                    'amount' => $order->total,
                    'gateway' => $order->payment_method === 'midtrans' ? 'midtrans' : null,
                    'expired_at' => $this->expiryFor($gateway),
                ]);

                // Channel yang tidak butuh panggilan API (COD) sudah punya
                // instruksi sejak invoice dibuat, sehingga invoice langsung
                // menampilkan langkah pembayaran tanpa customer harus menekan
                // tombol bayar terlebih dahulu.
                if ($gateway->isChargeReady($payment)) {
                    $instruction = $gateway->createCharge($payment);

                    $payment->update([
                        'channel' => $instruction->channel,
                        'instructions' => $instruction->steps,
                    ]);
                }

                return $payment->fresh();
            } catch (QueryException $exception) {
                if (! $this->isUniqueViolation($exception)) {
                    throw $exception;
                }

                // Benturan pada order_id berarti proses lain untuk pesanan yang
                // sama sudah lebih dulu menyimpan invoice. Gunakan invoice itu
                // agar idempotensi tetap berlaku, bukan membuat nomor baru.
                if ($this->violatesUniqueKey($exception, self::ORDER_UNIQUE_KEY)) {
                    return $order->payment()->firstOrFail();
                }

                if ($attempt === self::INVOICE_NUMBER_ATTEMPTS) {
                    throw $exception;
                }
            }
        }

        throw new PaymentException('Gagal membuat nomor invoice, silakan coba lagi.');
    }

    /**
     * Menyimpan detail transaksi Midtrans ke invoice.
     *
     * Nomor virtual account (bila customer memilih VA) diambil dari
     * notification Midtrans, tidak pernah dibuat oleh aplikasi.
     *
     * @param  array<string, mixed>  $payload
     */
    private function storeGatewayData(Payment $payment, array $payload): void
    {
        $payment->update(array_filter([
            'gateway' => 'midtrans',
            'gateway_reference' => $payload['transaction_id'] ?? null,
            'channel' => $payload['payment_channel'] ?? $payment->channel,
            'payment_type' => $payload['payment_type'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'fraud_status' => $payload['fraud_status'] ?? null,
            'bank_name' => $payload['bank'] ?? null,
            'account_number' => $payload['va_number'] ?? $payload['account_number'] ?? null,
            'midtrans_payload' => $payload ?: null,
        ], fn ($value) => $value !== null));
    }

    /**
     * Menentukan status internal dari notification Midtrans.
     *
     * @param  array<string, mixed>  $payload
     */
    private function resolveStatus(array $payload): string
    {
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');

        // `capture` pada kartu kredit masih bergantung pada fraud detection:
        // `challenge` berarti ditahan untuk verifikasi, `deny` berarti ditolak.
        if ($transactionStatus === 'capture') {
            return match ($payload['fraud_status'] ?? null) {
                'challenge' => 'processing',
                'deny' => 'denied',
                default => 'paid',
            };
        }

        return self::STATUS_MAP[$transactionStatus] ?? 'pending';
    }

    /**
     * Menolak pembayaran yang nominalnya tidak sama dengan invoice.
     *
     * Midtrans mengirim `gross_amount`; ketidakcocokan berarti ada yang
     * berubah di antara pembuatan transaksi dan pembayaran, sehingga
     * pembayaran tidak boleh otomatis dianggap lunas.
     *
     * @param  array<string, mixed>  $payload
     */
    private function amountMatches(Payment $payment, array $payload): bool
    {
        if (! array_key_exists('gross_amount', $payload)) {
            return true;
        }

        return (int) round((float) $payload['gross_amount']) === (int) $payment->amount;
    }

    /**
     * Metode tanpa pembayaran dimuka (COD) tidak memiliki batas bayar.
     */
    private function expiryFor(PaymentGatewayContract $gateway): ?string
    {
        $hours = (int) config('gosport.payment.expiry_hours', 24);

        if ($hours < 1 || ! $gateway->requiresPrepayment()) {
            return null;
        }

        return now()->addHours($hours)->toDateTimeString();
    }

    /**
     * Nomor invoice harian berurutan, mis. `GS202609260001`.
     *
     * Urutan diambil dari baris terakhir berawalan prefix hari ini (urutan id,
     * bukan pengurutan string) sehingga penomoran tetap benar setelah
     * melewati angka 9999. Prefix dipotong sebelum di-cast ke integer agar
     * digit tanggal tidak ikut dihitung.
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = config('gosport.invoice_prefix', 'GS').now()->format('Ymd');

        $last = Payment::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('invoice_number');

        $sequence = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;

        return $prefix.str_pad((string) $sequence, self::INVOICE_SEQUENCE_WIDTH, '0', STR_PAD_LEFT);
    }

    private function isUniqueViolation(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505'], true);
    }

    /**
     * Menguji apakah pelanggaran unique terjadi pada index tertentu.
     */
    private function violatesUniqueKey(QueryException $exception, string $key): bool
    {
        return str_contains($exception->getMessage(), $key);
    }
}
