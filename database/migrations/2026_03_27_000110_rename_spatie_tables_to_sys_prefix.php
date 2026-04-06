<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('activity_log') && ! Schema::hasTable('sys_activity_log')) {
            Schema::rename('activity_log', 'sys_activity_log');
        }
        if (Schema::hasTable('media') && ! Schema::hasTable('sys_media')) {
            Schema::rename('media', 'sys_media');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sys_activity_log') && ! Schema::hasTable('activity_log')) {
            Schema::rename('sys_activity_log', 'activity_log');
        }
        if (Schema::hasTable('sys_media') && ! Schema::hasTable('media')) {
            Schema::rename('sys_media', 'media');
        }
    }
};
