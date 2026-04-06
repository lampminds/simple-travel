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
        Schema::create('service_variant_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_variant_id')->constrained();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('weekday_mask')->nullable()
                ->comment('Bitmask for weekdays (1 = Monday, ... 7= Sunday)');
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
        Schema::dropIfExists('service_variant_availability_rules');
    }
};
