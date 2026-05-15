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
        Schema::create('service_cuisine_gastronomy_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')
                ->constrained('service_gastronomies', 'id', 'sg_fk');
            $table->foreignId('service_gastronomy_cuisine_id')
                ->constrained('cat_service_gastronomy_cuisines', 'id', 'sgc_fk');

            $table->unique(['service_gastronomy_id', 'service_gastronomy_cuisine_id'], 'sgc_sg_unique');

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
        Schema::dropIfExists('service_cuisine_gastronomy_assignments');
    }
};
