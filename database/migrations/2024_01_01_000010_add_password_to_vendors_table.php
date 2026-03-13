<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('password')->after('email')->nullable();
            $table->boolean('is_active')->default(true)->after('address');
            $table->rememberToken()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['password', 'is_active', 'remember_token']);
        });
    }
};
