<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Operator → agency commercial package offers (mirror of service_offers).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_offers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('operator_id')
                ->constrained('accounts', 'id', 'pkgo_operator_fk');

            $table->foreignId('agency_id')
                ->constrained('accounts', 'id', 'pkgo_agency_fk');

            $table->foreignId('operator_service_catalog_id')
                ->constrained('operator_service_catalog', 'id', 'pkgo_catalog_fk');

            $table->foreignId('operator_price_list_id')
                ->constrained('operator_price_lists', 'id', 'pkgo_price_list_fk');

            $table->enum('status', ['pending', 'accepted', 'rejected'])
                ->default('pending');

            $table->enum('availability', ['active', 'suspended', 'discontinued'])
                ->default('active');

            $table->timestamp('offered_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();

            lmpStamps($table);

            $table->unique(
                ['operator_id', 'agency_id', 'operator_service_catalog_id'],
                'unique_package_offer'
            );

            $table->index(['agency_id', 'status'], 'idx_package_offer_agency_status');
            $table->index(['operator_id', 'status'], 'idx_package_offer_operator_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_offers');
    }
};
