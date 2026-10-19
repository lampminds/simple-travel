<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hostname of an operator B2B portal (for example b2b-test.ebgrouptravel.com).
     * Stored without scheme or path. Null when the account has no portal.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('portal_host')
                ->nullable()
                ->unique()
                ->after('url')
                ->comment('Operator B2B portal hostname, without scheme or path');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('portal_host');
        });
    }
};
