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
        Schema::create('service_variant_availability_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_variant_availability_rule_id')
                ->constrained('service_variant_availability_rules', 'id', 'svar_id_fk');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('cutoff_minutes')->nullable()
                ->comment('Minutes before start time when booking closes');
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
        Schema::dropIfExists('service_variant_availability_time_slots');
    }
};
