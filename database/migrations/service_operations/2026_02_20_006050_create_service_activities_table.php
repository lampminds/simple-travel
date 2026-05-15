<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Activity/event service profile (1:1 with service) and catalogue type assignments.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_activities', function (Blueprint $table) {

            $table->id();
            $table->foreignId('service_id')->unique()->constrained();
            $table->unsignedSmallInteger('difficulty_level')->nullable()
                ->comment('1 easy, 2 moderate, 3 difficult');
            $table->unsignedSmallInteger('min_age')->nullable();
            $table->unsignedSmallInteger('max_age')->nullable();
            $table->boolean('guide_included')->default(false);
            $table->boolean('transport_included')->default(false);
            $table->boolean('outdoor_activity')->default(false);
            $table->unsignedSmallInteger('max_altitude_m')->nullable();
            $table->unsignedSmallInteger('distance_km')->nullable();
            $table->boolean('requires_good_weather')->default(false);
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('service_activity_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_activity_id')
                ->constrained('service_activities', 'id', 'sa_type_assignments_fk')
                ->cascadeOnDelete();
            $table->foreignId('service_activity_type_id')
                ->constrained('cat_service_activity_types', 'id', 'sa_type_assignments_type_fk')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('service_activity_type_assignments');
        Schema::dropIfExists('service_activities');
    }
};
