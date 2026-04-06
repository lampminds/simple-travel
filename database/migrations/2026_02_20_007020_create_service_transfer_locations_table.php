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
        Schema::create('service_transfer_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_transfer_location_type_id')
                ->constrained('service_transfer_location_types', 'id', 'stlt_fk');

            $table->string('address')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('airport_code', 10)->nullable();

            $table->boolean('is_active')->default(true);

            lmpStamps($table);
        });

        Schema::create('service_transfer_location_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_transfer_location_id')
                ->constrained('service_transfer_locations', 'id', 'stl_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transfer_locations');
        Schema::dropIfExists('service_transfer_location_translations');
    }
};
