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
        Schema::create('service_gastronomy_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')->unique()->constrained();

            $table->unsignedInteger('duration_minutes')->nullable();

            $table->boolean('includes_food')->default(true);
            $table->boolean('includes_drinks')->default(false);
            $table->boolean('is_guided')->default(false);

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
        Schema::dropIfExists('service_gastronomy_experiences');
    }
};
