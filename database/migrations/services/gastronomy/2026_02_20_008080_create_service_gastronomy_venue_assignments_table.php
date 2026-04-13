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
        Schema::create('service_gastronomy_venue_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')
                ->constrained('service_gastronomies', 'id', 'serv_gastr_fk');
            $table->foreignId('service_gastronomy_venue_id')
                ->constrained('cat_service_gastronomy_venues', 'id', 'sgv_fk');

            $table->unique(['service_gastronomy_id', 'service_gastronomy_venue_id'], 'sgv_sg_unique');

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
        Schema::dropIfExists('service_gastronomy_venue_assignments');
    }
};
