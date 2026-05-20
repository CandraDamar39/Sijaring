<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama');
            $table->text('deskripsi');
            $table->enum('kategori', ['Router', 'Switch', 'Access Point', 'Kabel', 'Server']);
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
