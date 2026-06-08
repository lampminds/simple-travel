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
        Schema::create('service_hotels', function (Blueprint $table) {
            $table->id();
            // Note: each Hotel has its main info in "services", and exactly ONE row here.
            $table->foreignId('service_id')->unique()->constrained();
            $table->smallInteger('stars')->nullable()->comment('Number of stars');
            $table->time('check_in_time')->nullable()->comment('Check-in time');
            $table->time('check_out_time')->nullable()->comment('Check-out time');

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
        Schema::dropIfExists('service_hotels');
    }
};
