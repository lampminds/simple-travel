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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();

            $table->morphs('owner');

            $table->string('name');
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();

            $table->boolean('is_active')->default(true);

            lmpStamps($table);

            $table->unique(['owner_id', 'owner_type']);
        });

        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('price_list_id')->constrained();
            $table->foreignId('service_variant_id')->constrained();

            $table->decimal('price', 10, 2);
            $table->enum('pricing_mode', ['fixed'])->default('fixed')->comment('fixed, percentage');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_lists');
    }
};
