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
        // Global groupings for transfer location types (e.g. public transport, hospitality, urban, ...).
        Schema::create('cat_service_transfer_location_type_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        // Constraint names shortened to stay below the MySQL identifier limit.
        Schema::create('cat_service_transfer_location_type_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_transfer_location_type_category_id')
                ->constrained('cat_service_transfer_location_type_categories', 'id', 'stltcat_translations_cat_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'stltcat_lang_fk')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
        });

        Schema::create('service_transfer_location_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->unsignedBigInteger('service_transfer_location_type_category_id')->nullable();
            $table->foreign('service_transfer_location_type_category_id', 'stlt_category_fk')
                ->references('id')
                ->on('cat_service_transfer_location_type_categories')
                ->nullOnDelete();
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        // Since the constraint name would be too long, we use a shortened name
        Schema::create('service_transfer_location_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_transfer_location_type_id')
                ->constrained('service_transfer_location_types', 'id', 'stlt_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transfer_location_type_translations');
        Schema::dropIfExists('service_transfer_location_types');
        Schema::dropIfExists('cat_service_transfer_location_type_category_translations');
        Schema::dropIfExists('cat_service_transfer_location_type_categories');
    }
};
