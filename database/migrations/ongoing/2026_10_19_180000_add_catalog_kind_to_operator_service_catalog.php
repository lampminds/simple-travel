<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distinguishes a real commercial package from a one-line catalog
     * generated so agencies can book a single accepted provider service.
     */
    public function up(): void
    {
        Schema::table('operator_service_catalog', function (Blueprint $table) {
            $table->enum('catalog_kind', ['package', 'service'])
                ->default('package')
                ->after('operator_id');

            $table->foreignId('source_service_offer_id')
                ->nullable()
                ->after('catalog_kind')
                ->constrained('service_offers', 'id', 'osc_source_offer_fk')
                ->nullOnDelete();

            $table->unique('source_service_offer_id', 'osc_source_offer_unique');
        });
    }

    public function down(): void
    {
        Schema::table('operator_service_catalog', function (Blueprint $table) {
            $table->dropForeign('osc_source_offer_fk');
            $table->dropUnique('osc_source_offer_unique');
            $table->dropColumn(['source_service_offer_id', 'catalog_kind']);
        });
    }
};
