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
        Schema::create('service_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_plan_id')->constrained();
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        // Add a parent
        Schema::table('service_plan_items', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('service_plan_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_plan_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        Schema::dropIfExists('service_plan_items');
    }
};
