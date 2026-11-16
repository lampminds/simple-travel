<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distinguishes a provider inviting another provider from the existing
     * operator invitation. Null keeps the historical commercial invitation.
     */
    public function up(): void
    {
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->string('purpose', 32)
                ->nullable()
                ->after('type')
                ->comment('provider_supply: a provider invites another provider. Null: operator commercial invitation.');
        });
    }

    public function down(): void
    {
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->dropColumn('purpose');
        });
    }
};
