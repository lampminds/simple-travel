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
        Schema::create('account_categories', function (BluePrint $table) {
            $table->id();
            $table->string('group')->comment('Category group - generic, in English');
            $table->string('code')->comment('Short name, in English');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');

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
        Schema::dropIfExists('account_categories');
    }
};
