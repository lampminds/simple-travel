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
        Schema::create('service_entertainment', function (Blueprint $table) {

            $table->id();
            $table->foreignId('service_id')->unique()->constrained();
            $table->foreignId('service_entertainment_type_id')->nullable()->constrained('cat_service_entertainment_types');
            $table->unsignedSmallInteger('difficulty_level')->nullable()
                ->comment('1 easy, 2 moderate, 3 difficult');
            $table->unsignedSmallInteger('min_age')->nullable();
            $table->unsignedSmallInteger('max_age')->nullable();
            $table->boolean('guide_included')->default(false);
            $table->boolean('transport_included')->default(false);
            $table->boolean('outdoor_activity')->default(true);
            $table->unsignedSmallInteger('max_altitude_m')->nullable();
            $table->unsignedSmallInteger('distance_km')->nullable();
            $table->boolean('requires_good_weather')->default(false);
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_entertainment');
    }
};

