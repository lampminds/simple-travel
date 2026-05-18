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
        Schema::create('service_gastronomies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')->unique()->constrained();

            $table->unsignedBigInteger('city_id')->nullable(); //References lmp_cities.id on addons connection (no FK across DBs)

            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_indoor')->default(false);
            $table->boolean('is_outdoor')->default(false);
            $table->boolean('has_takeaway')->default(false);
            $table->boolean('has_delivery')->default(false);

            lmpStamps($table);
        });

        Schema::create('service_gastronomy_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')
                ->constrained('service_gastronomies', 'id', 'sgta_sg_fk')
                ->cascadeOnDelete();
            $table->foreignId('service_gastronomy_type_id')
                ->constrained('cat_service_gastronomy_types', 'id', 'sgta_type_fk')
                ->cascadeOnDelete();

            $table->unique(['service_gastronomy_id', 'service_gastronomy_type_id'], 'sgta_sg_type_unique');

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
        Schema::dropIfExists('service_gastronomy_type_assignments');
        Schema::dropIfExists('service_gastronomies');
    }
};
