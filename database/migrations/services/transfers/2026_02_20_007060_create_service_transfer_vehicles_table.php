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
        Schema::create('service_transfer_vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_transfer_id')->constrained();
            $table->foreignId('service_transfer_vehicle_type_id')
                ->constrained('service_transfer_vehicle_types', 'id', 'stvtype_fk');

            $table->boolean('is_default')->default(false);

            lmpStamps($table);

            $table->unique([
                'service_transfer_id',
                'service_transfer_vehicle_type_id'
            ], 'service_transfer_vehicle_unique');
        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transfer_vehicles');
    }
};
