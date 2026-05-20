<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            if (!Schema::hasColumn('produks', 'spec'))  $table->string('spec')->default('—')->after('nama');
            if (!Schema::hasColumn('produks', 'foto'))  $table->string('foto')->nullable()->after('deskripsi');
            if (!Schema::hasColumn('produks', 'tone'))  $table->string('tone', 30)->default('tone-cream')->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['spec', 'foto', 'tone']);
        });
    }
};
