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
        Schema::create('plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained();
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        // Add a parent
        Schema::table('plan_items', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('plan_items');
        });

        Schema::create('plan_item_translations', function (BluePrint $table) {
            $table->id();
            $table->foreignId('plan_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('text')->nullable();

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
        Schema::table('plan_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('plan_item_translations');
        Schema::dropIfExists('plan_items');
    }
};
