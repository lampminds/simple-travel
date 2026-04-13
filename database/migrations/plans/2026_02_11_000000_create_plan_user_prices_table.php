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
        Schema::create('plan_user_prices', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('up_to')->unique()->comment('Up to amount of users');

            lmpStamps($table);
        });

        Schema::create('plan_user_price_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_user_price_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('description')->nullable();

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
        Schema::dropIfExists('plan_user_price_translations');
        Schema::dropIfExists('plan_user_prices');
    }
};
