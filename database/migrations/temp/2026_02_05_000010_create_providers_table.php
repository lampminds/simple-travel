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
        Schema::create('providers', function (BluePrint $table) {
            $table->id();
            $table->string('name');
            $table->string('commercial_name')->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->unsignedBigInteger('city_id')->nullable(); //References lmp_cities.id on addons connection (no FK across DBs)
            $table->string('postal_code')->nullable();

            $table->enum('status', ['active', 'onhold', 'inactive', 'terminated'])->default('active');
            $table->foreignId('inviting_id')->nullable()->constrained('accounts');

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
        Schema::dropIfExists('providers');
    }
};
