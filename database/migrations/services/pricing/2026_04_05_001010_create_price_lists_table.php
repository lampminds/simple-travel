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

        Schema::create('price_list_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('price_list_id')->constrained()->cascadeOnDelete();

            $table->morphs('assigned_to'); // operator, agency

            $table->enum('adjustment_type', ['none', 'percentage', 'fixed'])
                ->default('none')
                ->comment('Global adjustment over list');

            // Ej:
            // percentage: -10 = 10% descuento, +15 = markup
            // fixed: monto absoluto
            $table->decimal('adjustment_value', 12, 2)
                ->nullable()
                ->comment('-10 = discount, +15 = markup');

            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['assigned_to_type', 'assigned_to_id'], 'assigned_to_index');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_list_assignments');
        Schema::dropIfExists('price_list_items');
        Schema::dropIfExists('price_lists');
    }
};
