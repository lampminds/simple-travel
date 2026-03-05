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
        Schema::create('provider_categories', function (BluePrint $table) {
            $table->id();
            $table->string('group')->comment('Category group');
            $table->string('code')->comment('Short name');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing - inside the group');

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
        Schema::dropIfExists('provider_categories');
    }
};
