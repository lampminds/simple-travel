<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lmp_ips', function (Blueprint $table): void {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('status', 16)->default('pending');
            $table->string('country_code', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->string('region_name')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('looked_up_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lmp_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lmp_ip_id')->nullable()->constrained('lmp_ips')->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamps();

            $table->index(['user_id', 'logged_in_at'], 'lmp_sessions_user_logged_in_index');
            $table->index('session_id', 'lmp_sessions_session_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lmp_sessions');
        Schema::dropIfExists('lmp_ips');
    }
};
