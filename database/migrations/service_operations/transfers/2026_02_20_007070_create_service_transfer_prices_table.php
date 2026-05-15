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
        Schema::create('service_transfer_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_transfer_id')->constrained();
            $table->unsignedBigInteger('route_id')->nullable();

            $table->foreignId('service_transfer_vehicle_type_id')->nullable()
                ->constrained('service_transfer_vehicle_types', 'id', 'stvt_fk');

            $table->enum('pricing_type', ['per_vehicle', 'per_person']);
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->decimal('price_per_extra_passenger', 10, 2)->nullable();

            $table->unsignedInteger('min_passengers')->nullable();
            $table->unsignedInteger('max_passengers')->nullable();

            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

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
        Schema::dropIfExists('service_transfer_prices');
    }
};
