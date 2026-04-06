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
        Schema::create('service_variant_availability_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_variant_id')
                ->constrained('service_variants', 'id', 'sv_id_fk');
            $table->date('date');
            $table->date('start_time')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('closed')->default(false);
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('service_variant_availability_overrides');
    }
};
