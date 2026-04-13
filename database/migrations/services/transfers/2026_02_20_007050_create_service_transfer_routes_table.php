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
        Schema::create('service_transfer_routes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_transfer_id')->constrained();

            $table->foreignId('origin_location_id')->constrained('service_transfer_locations');
            $table->foreignId('destination_location_id')->constrained('service_transfer_locations');

            $table->boolean('is_active')->default(true);

            $table->decimal('distance_km', 8, 2)->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();

            lmpStamps($table);

            $table->unique([
                'service_transfer_id',
                'origin_location_id',
                'destination_location_id'
            ], 'transfer_route_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transfer_routes');
    }
};
