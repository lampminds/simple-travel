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
        Schema::create('service_plan_translations', function (BluePrint $table) {
            $table->id();
            $table->foreignId('service_plan_id')->constrained();
            $table->foreignId('language_id')->constrained();
            $table->decimal('price', 10, 2)->nullable();
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
        Schema::dropIfExists('service_plan_translations');
    }
};
