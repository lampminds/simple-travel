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
        Schema::create('service_gastronomy_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')->constrained();
            $table->unsignedTinyInteger('day_of_week'); // 0 (domingo) - 6 (sábado)

            $table->time('open_time');
            $table->time('close_time');

            lmpStamps($table);

            $table->index(['service_gastronomy_id', 'day_of_week'], 'sg_day_of_week_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_gastronomy_schedules');
    }
};
