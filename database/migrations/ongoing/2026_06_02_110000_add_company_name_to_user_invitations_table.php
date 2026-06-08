<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->string('company_name')
                ->nullable()
                ->after('name')
                ->comment('Proposed company name for external invitations (new company signup)');
        });
    }

    public function down(): void
    {
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
    }
};
