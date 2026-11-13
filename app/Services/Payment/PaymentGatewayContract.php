<?php

namespace App\Services\Payment;

use App\Models\Payment;

/**
 * Satu channel pembayaran (Midtrans Snap, COD, dan channel lain di masa depan).
 *
 * Setiap implementasi hanya bertanggung jawab pada satu `payment_method`,
 * sehingga controller dan view tidak perlu tahu detail spesifik penyedia.
 */
interface PaymentGatewayContract
{
    /**
     * Nilai `payment_method` yang ditangani channel ini.
     */
    public function method(): string;

    /**
     * Apakah customer harus membayar sebelum pesanan dikirim.
     *
     * Channel yang tidak butuh bayar di muka (mis. COD) tidak memiliki
     * batas waktu pembayaran, sehingga `expired_at` invoice-nya kosong.
     */
    public function requiresPrepayment(): bool;

    /**
     * Apakah instruksi pembayaran untuk invoice ini sudah siap.
     *
     * Dipakai agar permintaan pembuatan transaksi bersifat idempoten:
     * customer yang klik "Bayar" berkali-kali tidak akan membuat transaksi
     * ganda di sisi payment gateway.
     */
    public function isChargeReady(Payment $payment): bool;

    /**
     * Membuat instruksi pembayaran yang akan ditampilkan ke customer.
     *
     * Implementasi yang menghubungi API gateway (seperti Midtrans) wajib
     * menyimpan token atau URL hasil pembuatan transaksi ke dalam
     * `$payment` sebelum mengembalikannya.
     */
    public function createCharge(Payment $payment): PaymentInstruction;

    /**
     * Memverifikasi pembayaran melalui API gateway.
     *
     * Hanya dipakai untuk channel yang mendukung pengecekan status aktif.
     * Midtrans tidak memakai ini karena status finalnya hanya boleh berasal
     * dari notification.
     */
    public function verify(Payment $payment): bool;
}
