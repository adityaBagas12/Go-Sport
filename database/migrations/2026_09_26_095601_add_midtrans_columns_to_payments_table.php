<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom khusus Midtrans Snap ke tabel `payments` yang sudah ada.
 *
 * Migration ini hanya menambah kolom — tidak membuat tabel `payments` lagi.
 * Kolom rekening lama (`bank_name`, `account_number`, `account_holder`)
 * tetap dipakai, tetapi nilainya sekarang berasal dari notification Midtrans
 * (nomor virtual account), bukan dari konfigurasi manual aplikasi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('snap_token')->nullable()->after('qr_code');
            $table->text('payment_url')->nullable()->after('snap_token');
            $table->string('payment_type')->nullable()->after('payment_url');
            $table->string('transaction_status')->nullable()->after('payment_type');
            $table->string('fraud_status')->nullable()->after('transaction_status');
            $table->json('midtrans_payload')->nullable()->after('fraud_status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token', 'payment_url', 'payment_type',
                'transaction_status', 'fraud_status', 'midtrans_payload',
            ]);
        });
    }
};
