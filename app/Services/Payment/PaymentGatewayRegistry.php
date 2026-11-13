<?php

namespace App\Services\Payment;

/**
 * Memetakan `payment_method` ke channel pembayaran yang menanganinya.
 *
 * Untuk menambahkan metode baru, tambahkan implementasinya ke
 * {@see self::CHANNELS} lalu daftarkan metodenya di
 * `config('gosport.payment_methods')`. Tidak ada perubahan lain yang
 * diperlukan pada controller atau view.
 */
class PaymentGatewayRegistry
{
    /**
     * @var array<string, class-string<PaymentGatewayContract>>
     */
    public const CHANNELS = [
        'midtrans' => MidtransSnapGateway::class,
        'cod' => CodGateway::class,
    ];

    /**
     * @var array<string, PaymentGatewayContract>
     */
    private array $resolved = [];

    public function supports(string $method): bool
    {
        return isset(self::CHANNELS[$method]);
    }

    /**
     * @return list<string>
     */
    public function supportedMethods(): array
    {
        return array_keys(self::CHANNELS);
    }

    public function for(string $method): PaymentGatewayContract
    {
        if (! $this->supports($method)) {
            throw new PaymentException("Metode pembayaran \"{$method}\" belum didukung aplikasi.");
        }

        return $this->resolved[$method] ??= app(self::CHANNELS[$method]);
    }
}
