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
        Schema::create('cat_genders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');

            lmpStamps($table);
        });

        Schema::create('cat_gender_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gender_id')->constrained('cat_genders');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();

            lmpStamps($table);

            $table->unique(['gender_id', 'language_id'], 'cat_gender_trans_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_gender_translations');
        Schema::dropIfExists('cat_genders');
    }
};

