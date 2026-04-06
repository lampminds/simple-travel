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
        Schema::create('cat_languages', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_locales');

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
        Schema::dropIfExists('cat_languages');
    }
};

