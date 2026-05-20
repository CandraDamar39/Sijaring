<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('company')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('zip', 10);
            $table->enum('payment_method', ['bca','mandiri','bni','qris','cod'])->default('bca');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('shipping', 10, 2)->default(25000);
            $table->decimal('total', 14, 2);
            $table->enum('status', ['Menunggu Konfirmasi','Diproses','Dikirim','Selesai','Dibatalkan'])
                  ->default('Menunggu Konfirmasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
