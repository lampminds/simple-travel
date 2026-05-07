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
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->decimal('units_per_usd', 18, 8);
            $table->timestamp('starting_at');

            lmpStamps($table);

            $table->unique(['currency_id', 'starting_at']);
            $table->index(['currency_id', 'starting_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_rates');
    }
};
