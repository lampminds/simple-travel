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
        Schema::create('cat_menus', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('Short name, in English');
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('cat_menus')->cascadeOnDelete();
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('cat_menu_translations', function (BluePrint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('cat_menus')->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('tip')->nullable();

            lmpStamps($table);

            $table->unique(['menu_id', 'language_id'], 'cat_menu_trans_lang_unique');
        });

        Schema::create('cat_menu_account_type_exclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('cat_menus')->cascadeOnDelete();
            $table->foreignId('account_type_id')->constrained('cat_account_types')->cascadeOnDelete();

            $table->unique(['menu_id', 'account_type_id']);

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
        Schema::dropIfExists('cat_menu_account_type_exclusions');
        Schema::dropIfExists('cat_menu_translations');
        Schema::dropIfExists('cat_menus');
    }
};
