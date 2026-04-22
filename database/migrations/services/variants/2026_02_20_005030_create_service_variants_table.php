<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Service variants: generic "complete reservable unit" per service.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->string('sku')->comment('Stock keeping unit / variant code');

            $table->enum('status', ['active', 'suspended', 'discontinued'])->default('active');

            $table->enum('pricing_type', ['per_person', 'per_unit', 'per_room', 'per_vehicle', 'per_group']);
            $table->decimal('base_price', 12, 2);
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->enum('inventory_type', ['unlimited', 'per_day', 'per_timeslot', 'per_departure']);
            $table->unsignedInteger('inventory_total')->nullable()->comment('When inventory_type is fixed');
            $table->unsignedSmallInteger('capacity_min')->nullable()->comment('Min capacity (e.g. persons)');
            $table->unsignedSmallInteger('capacity_max')->nullable()->comment('Max capacity (e.g. persons)');

            $table->unsignedInteger('min_advance_booking_hours')->nullable();
            $table->unsignedSmallInteger('max_advance_booking_days')->nullable();
            $table->time('start_time')->nullable()->comment('When applicable (e.g. daily slot)');
            $table->time('end_time')->nullable();
            $table->unsignedInteger('cutoff_minutes')->nullable()
                ->comment('Minutes before start time when booking closes');

            $table->smallInteger('sort_order')->default(9999);

            $table->unique(['service_id', 'sku']);

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
        Schema::dropIfExists('service_variants');
    }
};
