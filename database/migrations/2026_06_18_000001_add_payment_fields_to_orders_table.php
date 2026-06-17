<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom untuk integrasi pembayaran Midtrans (Snap):
 *  - snap_token      : token transaksi Snap (untuk membuka popup pembayaran).
 *  - payment_status  : status pembayaran terpisah dari status fulfillment (`status`).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('total');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'challenge'])
                  ->default('pending')->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'payment_status']);
        });
    }
};
