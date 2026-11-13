<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Services\MidtransService;

/**
 * Midtrans Snap: customer diarahkan ke halaman pembayaran Midtrans, lalu
 * memilih sendiri metode pembayarannya (bank transfer, virtual account,
 * QRIS, GoPay, e-wallet, atau metode lain yang tersedia di akun Midtrans).
 *
 * Aplikasi tidak pernah membuat nomor rekening maupun nomor virtual account
 * sendiri. Nomor VA (bila customer memilih VA) dikirim Midtrans lewat
 * notification dan baru disimpan di {@see Payment}.
 */
class MidtransSnapGateway implements PaymentGatewayContract
{
    public function __construct(private MidtransService $midtrans) {}

    public function method(): string
    {
        return 'midtrans';
    }

    public function requiresPrepayment(): bool
    {
        return true;
    }

    /**
     * Transaksi Snap sudah dibuat bila Snap Token tersimpan.
     *
     * Ini yang membuat tombol "Bayar" aman diklik berkali-kali: permintaan
     * kedua tidak akan membuat transaksi baru di Midtrans.
     */
    public function isChargeReady(Payment $payment): bool
    {
        return filled($payment->snap_token);
    }

    public function createCharge(Payment $payment): PaymentInstruction
    {
        $response = $this->midtrans->createSnapTransaction($payment);

        $payment->update([
            'snap_token' => $response['token'] ?? null,
            'payment_url' => $response['redirect_url'] ?? null,
            'gateway' => 'midtrans',
        ]);

        return new PaymentInstruction(
            channel: $this->method(),
            channelLabel: (string) config('gosport.payment_methods.midtrans'),
            steps: [
                'Klik "Bayar Sekarang" untuk membuka halaman pembayaran Midtrans.',
                'Pilih metode pembayaran yang tersedia, seperti transfer bank, virtual account, QRIS, GoPay, atau e-wallet.',
                'Selesaikan pembayaran sesuai petunjuk dari Midtrans.',
                'Status pembayaran diperbarui otomatis dari notification Midtrans.',
            ],
        );
    }

    /**
     * Status tidak pernah diverifikasi lewat request balik dari user.
     *
     * Satu-satunya sumber kebenaran adalah notification Midtrans, sehingga
     * method ini selalu mengembalikan `false`.
     */
    public function verify(Payment $payment): bool
    {
        return false;
    }
}
