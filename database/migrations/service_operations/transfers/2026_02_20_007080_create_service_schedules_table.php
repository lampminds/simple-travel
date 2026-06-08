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
        Schema::create('service_transfer_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_transfer_id')->constrained();
            $table->integer('day_of_week')->nullable();
            $table->time('departure_time')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('service_transfer_schedules');
    }
};
