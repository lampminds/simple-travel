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
        Schema::create('cat_contact_positions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');

            lmpStamps($table);
        });

        Schema::create('cat_contact_position_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_position_id')->constrained('cat_contact_positions');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();

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
        Schema::dropIfExists('cat_contact_position_translations');
        Schema::dropIfExists('cat_contact_positions');
    }
};
