<?php

namespace App\Services\Payment;

/**
 * Instruksi pembayaran yang ditampilkan ke customer pada halaman invoice.
 *
 * Instruksi bersifat generik. Detail yang spesifik per metode - seperti
 * nomor virtual account, bank tujuan, atau QR - TIDAK disimpan di sini:
 * untuk Midtrans detail tersebut hanya berlaku setelah customer memilih
 * metode di halaman Midtrans dan diteruskan lewat notification, sehingga
 * disimpan di tabel `payments` dan dibaca dari sana.
 */
final readonly class PaymentInstruction
{
    /**
     * @param  list<string>  $steps  Langkah pembayaran yang ditampilkan ke customer.
     */
    public function __construct(
        public string $channel,
        public string $channelLabel,
        public array $steps = [],
    ) {}
}
