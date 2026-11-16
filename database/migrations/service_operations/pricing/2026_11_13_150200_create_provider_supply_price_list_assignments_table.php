<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Assigns a supplier price list to a buyer provider.
     *
     * The buyer uses it as an internal cost. It does not change the price
     * the buyer publishes to operators.
     */
    public function up(): void
    {
        Schema::create('provider_supply_price_list_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provider_price_list_id')
                ->constrained('provider_price_lists', 'id', 'pspla_list_fk')
                ->cascadeOnDelete();

            $table->foreignId('buyer_account_id')
                ->constrained('accounts', 'id', 'pspla_buyer_fk')
                ->cascadeOnDelete();

            $table->enum('adjustment_type', ['none', 'percentage', 'fixed'])
                ->default('none');

            $table->decimal('adjustment_value', 12, 2)->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['provider_price_list_id', 'buyer_account_id'],
                'pspla_list_buyer_unique'
            );
            $table->index('buyer_account_id', 'pspla_buyer_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_supply_price_list_assignments');
    }
};
