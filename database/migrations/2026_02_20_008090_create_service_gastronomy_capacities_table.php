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
        Schema::create('service_gastronomy_capacities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')->constrained();
            $table->unsignedInteger('min_people')->nullable();
            $table->unsignedInteger('max_people')->nullable();
            $table->unsignedInteger('total_capacity')->nullable();

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
        Schema::dropIfExists('service_gastronomy_capacities');
    }
};
