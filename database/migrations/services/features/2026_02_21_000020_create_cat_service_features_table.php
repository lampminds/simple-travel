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
        Schema::create('cat_service_features', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->foreignId('service_feature_category_id')
                ->constrained('cat_service_feature_categories', 'id', 'sf_category_fk');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);
            $table->boolean('is_selectable')->default(true)->comment('If true, this feature can be selected by the user');

            lmpStamps($table);
        });

        Schema::create('cat_service_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_feature_id')
                ->constrained('cat_service_features', 'id', 'sf_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
        });

        // add a parent_id column to cat_service_features foreign to itself
        Schema::table('cat_service_features', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('cat_service_features', 'id', 'sf_parent_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_service_feature_translations');
        Schema::dropIfExists('cat_service_features');
    }
};
