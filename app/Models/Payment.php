<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'order_id', 'payment_method', 'channel',
        'bank_name', 'account_number', 'account_holder', 'payment_status',
        'amount', 'qr_code', 'gateway', 'gateway_reference', 'instructions',
        'snap_token', 'payment_url', 'payment_type', 'transaction_status',
        'fraud_status', 'midtrans_payload', 'expired_at', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'instructions' => 'array',
        'midtrans_payload' => 'array',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Status internal aplikasi.
     *
     * Nilai `paid` hanya boleh diisi dari notification Midtrans atau
     * verifikasi admin untuk COD — bukan dari halaman `finish`.
     */
    public const STATUSES = [
        'pending' => 'Menunggu Pembayaran',
        'processing' => 'Menunggu Verifikasi',
        'paid' => 'Pembayaran Berhasil',
        'completed' => 'Selesai',
        'denied' => 'Pembayaran Ditolak',
        'cancelled' => 'Pembayaran Dibatalkan',
        'expired' => 'Pembayaran Kedaluwarsa',
        'failed' => 'Pembayaran Gagal',
        'refunded' => 'Dana Dikembalikan',
    ];

    /**
     * Status yang sudah final, sehingga tidak boleh diturunkan lagi menjadi
     * status belum lunas meski notifikasi berulang atau tidak berurutan tiba.
     */
    public const FINAL_STATUSES = ['paid', 'completed', 'refunded'];

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }

    public function scopeForInvoice(Builder $query, string $invoiceNumber): Builder
    {
        return $query->where('invoice_number', $invoiceNumber);
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'completed'], true);
    }

    /**
     * Status sudah final sehingga tidak boleh diturunkan lagi.
     */
    public function isFinal(): bool
    {
        return in_array($this->payment_status, self::FINAL_STATUSES, true);
    }

    public function isExpired(): bool
    {
        return $this->expired_at !== null
            && $this->expired_at->isPast()
            && ! $this->isPaid();
    }

    /**
     * Nomor virtual account yang dikirim Midtrans lewat notification.
     */
    public function hasAccountNumber(): bool
    {
        return filled($this->account_number);
    }

    public function hasQrCode(): bool
    {
        return filled($this->qr_code);
    }

    /**
     * Status yang tidak bisa lagi dibayar customer, walau `expired_at` belum
     * tercapai. `expired` tidak masuk di sini karena tetap ditentukan waktu.
     */
    public const UNPAYABLE_STATUSES = [
        'denied', 'cancelled', 'expired', 'failed', 'refunded',
    ];

    /**
     * Customer masih bisa menekan "Bayar Sekarang".
     */
    public function canBePaid(): bool
    {
        return $this->payment_method === 'midtrans'
            && ! $this->isPaid()
            && ! in_array($this->payment_status, self::UNPAYABLE_STATUSES, true)
            && ! $this->isExpired();
    }

    /**
     * Halaman pembayaran Midtrans sudah siap dibuka.
     */
    public function hasPaymentUrl(): bool
    {
        return filled($this->payment_url);
    }

    public function methodLabel(): string
    {
        return config('gosport.payment_methods')[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Nama metode yang benar-benar dipakai customer, mis. "BCA Virtual
     * Account" atau "QRIS". Midtrans yang menentukannya, bukan aplikasi.
     */
    public function paymentTypeLabel(): ?string
    {
        if (blank($this->payment_type)) {
            return null;
        }

        return strtoupper(str_replace('_', ' ', $this->payment_type));
    }

    public function amountLabel(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
