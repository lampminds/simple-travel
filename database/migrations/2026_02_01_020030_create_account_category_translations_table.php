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
        Schema::create('account_category_translations', function (BluePrint $table) {
            $table->id();
            $table->foreignId('account_category_id')->constrained();
            $table->foreignId('language_id')->constrained();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);

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
        Schema::dropIfExists('account_category_translations');
    }
};
