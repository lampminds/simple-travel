<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Canonical account-level commercial relationship between one operator and one provider.
     * This is independent from invitation rows (which are operational workflow records).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_relationships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operator_account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();

            $table->foreignId('provider_account_id')
                ->constrained('accounts')
                ->cascadeOnDelete();

            $table->enum('status', ['approved', 'suspended', 'terminated'])
                ->default('approved');

            $table->enum('created_via', ['invitation', 'manual', 'system'])
                ->default('invitation');

            $table->foreignId('source_invitation_id')
                ->nullable()
                ->constrained('user_invitations')
                ->nullOnDelete()
                ->comment('Invitation used to establish this relation when applicable');

            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable()
                ->comment('When relation became active for business operations');
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('terminated_at')->nullable();

            lmpStamps($table);

            $table->unique(
                ['operator_account_id', 'provider_account_id'],
                'u_operator_provider_relationship'
            );

            $table->index(['operator_account_id', 'status'], 'idx_relationship_operator_status');
            $table->index(['provider_account_id', 'status'], 'idx_relationship_provider_status');
            $table->index(['created_via', 'status'], 'idx_relationship_origin_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_relationships');
    }
};

