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
            // NULL = global system rate
            $table->foreignId('account_id')->nullable()->constrained();
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->string('source', 20)->nullable();

            // 1 USD = X currency units
            $table->decimal('units_per_usd_buy', 18, 8);
            $table->decimal('units_per_usd_sell', 18, 8);

            $table->timestamp('starting_at');
            $table->boolean('is_active')->default(true);

            lmpStamps($table);

            $table->unique(['account_id', 'currency_id', 'source', 'starting_at']);
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
