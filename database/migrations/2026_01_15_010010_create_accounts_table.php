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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('nick')->unique()->comment('The short name chosen by the tenant');
            $table->string('code')->unique()->comment('Combination of nick + 3 random numbers');
            $table->string('name')->nullable();
            $table->string('commercial_name')->nullable();
            $table->string('url')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->unsignedBigInteger('city_id')->nullable(); //References lmp_cities.id on addons connection (no FK across DBs)
            $table->string('postal_code')->nullable();
            $table->enum('status', ['active', 'onhold', 'inactive', 'terminated'])->default('active');

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
        Schema::dropIfExists('accounts');
    }
};
