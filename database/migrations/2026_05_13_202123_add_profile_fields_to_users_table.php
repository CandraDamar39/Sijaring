<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company'))       $table->string('company')->nullable()->after('phone');
            if (!Schema::hasColumn('users', 'bio'))           $table->text('bio')->nullable()->after('company');
            if (!Schema::hasColumn('users', 'address'))       $table->text('address')->nullable()->after('bio');
            if (!Schema::hasColumn('users', 'city'))          $table->string('city', 100)->nullable()->after('address');
            if (!Schema::hasColumn('users', 'province'))      $table->string('province', 100)->nullable()->after('city');
            if (!Schema::hasColumn('users', 'zip'))           $table->string('zip', 10)->nullable()->after('province');
            if (!Schema::hasColumn('users', 'address_label')) $table->string('address_label', 50)->nullable()->after('zip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company', 'bio', 'address', 'city', 'province', 'zip', 'address_label']);
        });
    }
};
