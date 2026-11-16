<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Commercial link between two providers.
     *
     * The buyer (assembler) invites the supplier and may include the supplier's
     * accepted variants inside the buyer's own combos. This is not an
     * operator–provider relationship.
     */
    public function up(): void
    {
        Schema::create('provider_supply_relationships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buyer_account_id')
                ->constrained('accounts', 'id', 'psr_buyer_fk')
                ->cascadeOnDelete();

            $table->foreignId('supplier_account_id')
                ->constrained('accounts', 'id', 'psr_supplier_fk')
                ->cascadeOnDelete();

            $table->enum('status', ['approved', 'suspended', 'terminated'])
                ->default('approved');

            $table->enum('created_via', ['invitation', 'manual', 'system'])
                ->default('invitation');

            $table->foreignId('source_invitation_id')
                ->nullable()
                ->constrained('user_invitations', 'id', 'psr_invitation_fk')
                ->nullOnDelete();

            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users', 'id', 'psr_approved_by_fk')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('terminated_at')->nullable();

            lmpStamps($table);

            $table->unique(
                ['buyer_account_id', 'supplier_account_id'],
                'psr_buyer_supplier_unique'
            );
            $table->index(['buyer_account_id', 'status'], 'psr_buyer_status_idx');
            $table->index(['supplier_account_id', 'status'], 'psr_supplier_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_supply_relationships');
    }
};
