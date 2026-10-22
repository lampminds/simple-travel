<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Components of a combo service.
     *
     * Composition is defined per parent variant: each row links one sellable
     * variant of a "combo" service to one concrete child variant. The child
     * service is derived through its variant (child_variant.service).
     *
     * - Price is the parent variant's price; child prices are not summed.
     * - Capacity is intended to count both the parent variant and every
     *   child variant (enforced at booking/availability stage, not here).
     * - Components never create service_offers for the children: a child is
     *   only offered on its own when the provider lists it separately.
     */
    public function up(): void
    {
        Schema::create('service_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_variant_id')
                ->constrained('service_variants', 'id', 'sc_parent_variant_fk')
                ->cascadeOnDelete();

            $table->foreignId('child_variant_id')
                ->constrained('service_variants', 'id', 'sc_child_variant_fk')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedSmallInteger('day_number')->nullable();
            $table->enum('inclusion_mode', ['included', 'optional', 'upgrade'])
                ->default('included')
                ->comment('included: always included, optional: included if selected, upgrade: only if selected');
            $table->unsignedSmallInteger('sort_order')->default(9999);
            $table->text('notes')->nullable();

            lmpStamps($table);

            $table->unique(['parent_variant_id', 'child_variant_id'], 'service_components_unique');
            $table->index('child_variant_id', 'sc_child_variant_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_components');
    }
};
