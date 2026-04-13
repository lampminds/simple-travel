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
        Schema::create('service_transfer_vehicle_types', function (Blueprint $table) {

            $table->id();
            $table->string('name'); // sedan, van, minibus, etc.

            $table->unsignedInteger('max_passengers');
            $table->unsignedInteger('max_luggage')->nullable();

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
        Schema::dropIfExists('service_transfer_vehicle_types');
    }
};
