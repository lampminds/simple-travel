<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A supplier provider offers one of its own variants to a buyer provider.
     *
     * Only an accepted, active offer may be used as a component of the buyer's combo.
     * The link does not open the supplier catalog.
     */
    public function up(): void
    {
        Schema::create('provider_service_offers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('supplier_id')
                ->constrained('accounts', 'id', 'pso_supplier_fk')
                ->cascadeOnDelete();

            $table->foreignId('buyer_id')
                ->constrained('accounts', 'id', 'pso_buyer_fk')
                ->cascadeOnDelete();

            $table->foreignId('service_variant_id')
                ->constrained('service_variants', 'id', 'pso_variant_fk')
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->enum('availability', ['active', 'suspended', 'discontinued'])->default('active');
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();

            lmpStamps($table);

            $table->unique(['buyer_id', 'service_variant_id'], 'pso_buyer_variant_unique');
            $table->index(['supplier_id', 'buyer_id', 'status'], 'pso_supplier_buyer_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_service_offers');
    }
};
