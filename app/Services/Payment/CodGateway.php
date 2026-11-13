<?php

namespace App\Services\Payment;

use App\Models\Payment;

/**
 * Bayar di tempat (COD): customer tidak melakukan pembayaran apa pun
 * sebelum pesanan diterima. Invoice tetap dibuat sebagai bukti pesanan,
 * namun tanpa instruksi pembayaran.
 */
class CodGateway implements PaymentGatewayContract
{
    public function method(): string
    {
        return 'cod';
    }

    public function requiresPrepayment(): bool
    {
        return false;
    }

    /**
     * COD tidak punya transaksi di payment gateway sama sekali,
     * jadi instruksinya selalu siap sejak awal.
     */
    public function isChargeReady(Payment $payment): bool
    {
        return true;
    }

    public function createCharge(Payment $payment): PaymentInstruction
    {
        return new PaymentInstruction(
            channel: $this->method(),
            channelLabel: config('gosport.payment_methods.cod'),
            steps: [
                'Siapkan uang tunai sejumlah total pembayaran.',
                'Bayar langsung kepada kurir saat pesanan diterima.',
                'Simpan invoice ini sebagai bukti pesanan.',
            ],
        );
    }

    public function verify(Payment $payment): bool
    {
        return false;
    }
}
