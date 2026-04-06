<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename configurable Laravel internal tables to sys_* names.
     */
    public function up(): void
    {
        $this->renameIfExists('jobs', 'sys_jobs');
        $this->renameIfExists('job_batches', 'sys_job_batches');
        $this->renameIfExists('failed_jobs', 'sys_failed_jobs');
        $this->renameIfExists('cache', 'sys_cache');
        $this->renameIfExists('cache_locks', 'sys_cache_locks');
        $this->renameIfExists('sessions', 'sys_sessions');
        $this->renameIfExists('password_reset_tokens', 'sys_password_reset_tokens');
    }

    public function down(): void
    {
        $this->renameIfExists('sys_jobs', 'jobs');
        $this->renameIfExists('sys_job_batches', 'job_batches');
        $this->renameIfExists('sys_failed_jobs', 'failed_jobs');
        $this->renameIfExists('sys_cache', 'cache');
        $this->renameIfExists('sys_cache_locks', 'cache_locks');
        $this->renameIfExists('sys_sessions', 'sessions');
        $this->renameIfExists('sys_password_reset_tokens', 'password_reset_tokens');
    }

    private function renameIfExists(string $from, string $to): void
    {
        if (Schema::hasTable($from) && ! Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }
    }
};
