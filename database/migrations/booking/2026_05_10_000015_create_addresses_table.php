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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->unsignedBigInteger('city_id')->nullable(); // refers to lmp_cities table
            $table->string('city')->nullable();
            $table->unsignedBigInteger('state_id')->nullable(); // refers to lmp_states table
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->unsignedBigInteger('country_id')->nullable(); // refers to lmp_countries table


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
        Schema::dropIfExists('addresses');
    }
};
