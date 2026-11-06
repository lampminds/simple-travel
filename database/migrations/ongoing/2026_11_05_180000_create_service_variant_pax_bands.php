<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-person prices by party size on a service variant.
     *
     * Each band is an inclusive passenger count. max_pax null means no upper limit.
     * The band price is the regular per-person amount. A row in
     * service_variant_pax_band_prices replaces that amount for one age band of the
     * service policy. An empty cell is not stored and uses the regular price.
     * Party size counts every passenger. Price-list percentage adjustments apply
     * afterwards; a fixed list price does not replace these amounts.
     */
    public function up(): void
    {
        Schema::create('service_variant_pax_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_variant_id')
                ->constrained('service_variants', 'id', 'svpb_variant_fk')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('min_pax');
            $table->unsignedSmallInteger('max_pax')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('sort_order')->default(9999);

            lmpStamps($table);

            $table->unique(['service_variant_id', 'min_pax'], 'svpb_variant_min_unique');
        });

        Schema::create('service_variant_pax_band_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_variant_pax_band_id')
                ->constrained('service_variant_pax_bands', 'id', 'svpbp_band_fk')
                ->cascadeOnDelete();
            $table->foreignId('provider_passenger_policy_rule_id')
                ->constrained('provider_passenger_policy_rules', 'id', 'svpbp_rule_fk')
                ->cascadeOnDelete();
            $table->decimal('price', 12, 2);

            lmpStamps($table);

            $table->unique(
                ['service_variant_pax_band_id', 'provider_passenger_policy_rule_id'],
                'svpbp_band_rule_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_variant_pax_band_prices');
        Schema::dropIfExists('service_variant_pax_bands');
    }
};
