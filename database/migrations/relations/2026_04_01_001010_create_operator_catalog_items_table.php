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
        Schema::create('operator_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->constrained('accounts', 'id', 'fkp2_account_id');
            $table->foreignId('operator_id')
                ->constrained('accounts', 'id', 'fko2_account_id');

            $table->foreignId('service_id')
                ->nullable()
                ->comment('For the whole service -then variant id is ignored');
            $table->foreignId('service_variant_id')
                ->nullable()
                ->comment('For a specific variant -then service id is ignored');

            $table->foreignId('service_offer_id')
                ->nullable()
                ->constrained()
                ->comment('The offer that this item is based on - optional');

            $table->enum('status', ['pending', 'active', 'hidden', 'suspended', 'discontinued'])
                ->comment('Status according to the operator account')
                ->default('pending');

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('removed_at')->nullable();

            $table->unique(['operator_id', 'service_id', 'service_variant_id'], 'unique_operator_catalog_item');

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
        Schema::dropIfExists('operator_catalog_items');
    }
};
