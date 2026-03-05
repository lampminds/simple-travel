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
        Schema::create('contact_type', function (BluePrint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('contact_type_id')->constrained();
            $table->string('value');

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
        Schema::dropIfExists('contact_type');
    }
};
