<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->foreignId('invited_account_id')
                ->nullable()
                ->after('invited_person_id')
                ->constrained('accounts')
                ->nullOnDelete()
                ->comment('Target company when invitee is an existing user (e.g. provider account)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_invitations');
    }
};
