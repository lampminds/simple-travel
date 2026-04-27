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
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name');
            $table->foreignId('role_id')->constrained('user_roles');
            $table->string('token')->unique();
            $table->unsignedInteger('send_attempts')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->foreignId('invited_by')->nullable()->constrained('users');
            $table->foreignId('account_inviting')->nullable()->constrained('accounts');
            $table->enum('type', ['internal', 'external'])->default('internal');
            $table->enum('status', ['pending', 'accepted', 'declined', 'expired', 'revoked'])->default('pending');

            lmpStamps($table);
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
