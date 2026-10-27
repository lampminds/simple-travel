<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Short opening-hours line shown in the public site header.
     */
    public function up(): void
    {
        Schema::table('account_public_profiles', function (Blueprint $table) {
            $table->string('business_hours', 120)
                ->nullable()
                ->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('account_public_profiles', function (Blueprint $table) {
            $table->dropColumn('business_hours');
        });
    }
};
