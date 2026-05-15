<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links each hotel profile row to one or more catalogue hotel types.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_hotel_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_hotel_id')
                ->constrained('service_hotels', 'id', 'shta_hotel_fk')
                ->cascadeOnDelete();
            $table->foreignId('service_hotel_type_id')
                ->constrained('cat_service_hotel_types', 'id', 'shta_type_fk')
                ->cascadeOnDelete();

            $table->unique(['service_hotel_id', 'service_hotel_type_id'], 'service_hotel_type_assignments_unique');

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
        Schema::dropIfExists('service_hotel_type_assignments');
    }
};
