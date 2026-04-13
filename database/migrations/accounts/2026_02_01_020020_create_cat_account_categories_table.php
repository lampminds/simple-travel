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
        Schema::create('cat_account_categories', function (Blueprint $table) {
            $table->id();
            $table->string('group')->comment('Category group - generic, in English');
            $table->string('code')->comment('Short name, in English');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');

            lmpStamps($table);
        });

        Schema::create('cat_account_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_category_id')->constrained('cat_account_categories');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
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
        Schema::dropIfExists('cat_account_category_translations');
        Schema::dropIfExists('cat_account_categories');
    }
};
