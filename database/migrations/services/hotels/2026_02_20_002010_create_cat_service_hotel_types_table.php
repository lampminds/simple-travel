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
        Schema::create('cat_service_hotel_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->foreignId('service_hotel_type_category_id')
                ->constrained('cat_service_hotel_type_categories', 'id', 'shtc_fk');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('cat_service_hotel_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_hotel_type_id')
                ->constrained('cat_service_hotel_types', 'id', 'sht_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'sht_lang_fk')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

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
        Schema::dropIfExists('cat_service_hotel_type_translations');
        Schema::dropIfExists('cat_service_hotel_types');
    }
};
