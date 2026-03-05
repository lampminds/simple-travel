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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('kicked_out')
                ->default(false)
                ->after('password')
                ->comment('If true, the user is kicked out and needs to login again.');
            $table->timestamp('last_login_at')
                ->nullable()
                ->after('kicked_out')
                ->comment('The last time the user logged in.');
            $table->timestamp('last_seen_at')
                ->nullable()
                ->after('last_login_at')
                ->comment('The last time the user requested a page');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kicked_out', 'last_login_at', 'last_seen_at']);
        });
    }
};
