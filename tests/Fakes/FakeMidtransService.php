<?php

namespace Tests\Fakes;

use App\Models\Payment;
use App\Services\MidtransService;
use RuntimeException;

/**
 * Pengganti {@see MidtransService} untuk test.
 *
 * SDK Midtrans bekerja dengan static class dan selalu menghubungi API,
 * sehingga test tidak boleh memanggil jaringan. Fake ini mencatat payload
 * transaksi yang dibuat dan mengembalikan data yang diminta test.
 */
class FakeMidtransService extends MidtransService
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $createdPayloads = [];

    /**
     * @var array<string, mixed>
     */
    public array $snapResponse = [
        'token' => 'fake-snap-token',
        'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/transactions/fake-snap-token/pay',
    ];

    /**
     * Payload notification yang dikembalikan sebagai hasil verifikasi.
     *
     * @var array<string, mixed>|null
     */
    public ?array $notificationPayload = null;

    /**
     * @return array<string, mixed>
     */
    public function createSnapTransaction(Payment $payment): array
    {
        $order = $payment->order()->with(['items', 'user'])->firstOrFail();

        $this->createdPayloads[] = $this->payload($payment, $order);

        return $this->snapResponse;
    }

    /**
     * @return array<string, mixed>
     */
    public function notificationPayload(): array
    {
        if ($this->notificationPayload === null) {
            throw new RuntimeException('Notification belum disiapkan untuk test ini.');
        }

        return $this->notificationPayload;
    }

    public function resetCreatedPayloads(): void
    {
        $this->createdPayloads = [];
    }
}
