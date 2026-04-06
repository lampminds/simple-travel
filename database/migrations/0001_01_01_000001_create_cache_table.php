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
        // Use config (not env) so table names match config/cache.php when config is cached (production).
        $cacheTable = (string) config('cache.stores.database.table', 'sys_cache');
        $cacheLocksTable = (string) config('cache.stores.database.lock_table', 'sys_cache_locks');

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
        $cacheTable = (string) config('cache.stores.database.table', 'sys_cache');
        $cacheLocksTable = (string) config('cache.stores.database.lock_table', 'sys_cache_locks');

        Schema::dropIfExists($cacheTable);
        Schema::dropIfExists($cacheLocksTable);
    }
};
