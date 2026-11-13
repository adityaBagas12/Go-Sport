<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan data instruksi pembayaran (rekening tujuan / nomor virtual account)
     * ke tabel `payments` sekaligus mengunci relasi satu invoice per pesanan.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('channel')->nullable()->after('payment_method');
            $table->string('bank_name')->nullable()->after('channel');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('account_holder')->nullable()->after('account_number');
            $table->json('instructions')->nullable()->after('account_holder');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('order_id', 'payments_order_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_order_id_unique');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['channel', 'bank_name', 'account_number', 'account_holder', 'instructions']);
        });
    }
};
