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
        Schema::create('service_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_variant_id')->constrained();

            $table->enum('transfer_type', ['one_way', 'round_trip']);
            $table->enum('modality', ['private', 'shared']);

            $table->boolean('allows_multiple_stops')->default(false);

            $table->enum('operation_mode', ['on_demand', 'scheduled'])->default('on_demand');

            $table->unsignedInteger('duration_minutes')->nullable()->comment('Duration in minutes');

            $table->unsignedInteger('default_duration_minutes')->nullable();

            $table->boolean('requires_flight_info')->default(false);
            $table->boolean('requires_pickup_time')->default(false);
            $table->boolean('requires_dropoff_time')->default(false);

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
        Schema::dropIfExists('service_transfers');
    }
};
