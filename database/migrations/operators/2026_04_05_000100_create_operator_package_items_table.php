<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Activity/event service profile (1:1 with service) and catalogue type assignments.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_package_items', function (Blueprint $table) {

            $table->id();
            $table->foreignId('operator_service_catalog_id')->constrained('operator_service_catalog', 'id', 'fkp_service_id');
            $table->unsignedSmallInteger('day_number')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(9999);
            $table->integer('quantity')->default(1);
            $table->enum('inclusion_mode', ['included', 'optional', 'upgrade'])
                    ->comment('included: always included, optional: included if selected, upgrade: only if selected');
            $table->enum('cost_mode', ['fixed', 'percentage', 'inherit']);
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('operator_package_items');
    }
};
