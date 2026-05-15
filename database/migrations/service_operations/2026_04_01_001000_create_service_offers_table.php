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
        Schema::create('service_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->constrained('accounts', 'id', 'fkp_account_id');
            $table->foreignId('operator_id')
                ->constrained('accounts', 'id', 'fko_account_id');
            $table->foreignId('service_id')
                ->nullable()
                ->comment('For the whole service -then variant id is ignored');
            $table->foreignId('service_variant_id')
                ->nullable()
                ->comment('For a specific variant -then service id is ignored');
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                ->comment('Status of the offer')
                ->default('pending');
            $table->enum('availability', ['active', 'suspended', 'discontinued'])
                ->comment('How the offer is shown to this operator account')
                ->default('active');
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();

            $table->unique(['provider_id', 'operator_id', 'service_id', 'service_variant_id'], 'unique_service_offer');

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
        Schema::dropIfExists('service_offers');
    }
};
