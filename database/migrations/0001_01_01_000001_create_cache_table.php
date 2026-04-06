<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cacheTable = (string) env('DB_CACHE_TABLE', 'sys_cache');
        $cacheLocksTable = (string) env('DB_CACHE_LOCK_TABLE', 'sys_cache_locks');

        Schema::create($cacheTable, function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create($cacheLocksTable, function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cacheTable = (string) env('DB_CACHE_TABLE', 'sys_cache');
        $cacheLocksTable = (string) env('DB_CACHE_LOCK_TABLE', 'sys_cache_locks');

        Schema::dropIfExists($cacheTable);
        Schema::dropIfExists($cacheLocksTable);
    }
};
