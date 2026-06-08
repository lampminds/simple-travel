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
        Schema::create('service_excursions', function (Blueprint $table) {
            $table->id();
            // Note: each excursion has its main info in "services", and exactly ONE row here.
            $table->foreignId('service_id')->unique()->constrained();
            $table->unsignedInteger('duration_minutes')->nullable()->comment('Duration in minutes');

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
        Schema::dropIfExists('service_excursions');
    }
};
