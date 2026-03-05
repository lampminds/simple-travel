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
        Schema::create('service_plan_item_translations', function (BluePrint $table) {
            $table->id();
            $table->foreignId('service_plan_item_id')->constrained();
            $table->foreignId('language_id')->constrained();
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
        Schema::dropIfExists('service_plan_item_translations');
    }
};
