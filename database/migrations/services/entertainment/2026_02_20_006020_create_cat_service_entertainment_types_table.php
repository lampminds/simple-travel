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
        Schema::create('cat_service_entertainment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->foreignId('service_entertainment_type_category_id')
                ->constrained('cat_service_entertainment_type_categories', 'id', 'service_entertainment_type_fk');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('cat_service_entertainment_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_entertainment_type_id')
                ->constrained('cat_service_entertainment_types', 'id', 'service_entertainment_type_translations_fk')
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
        Schema::dropIfExists('cat_service_entertainment_type_translations');
        Schema::dropIfExists('cat_service_entertainment_types');
    }
};

