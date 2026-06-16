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
        Schema::create('provider_price_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('provider_id')->constrained('accounts', 'id', 'pl_provider_fk');

            $table->string('name');
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->boolean('is_active')->default(true);

            lmpStamps($table);
        });

        Schema::create('provider_price_list_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provider_price_list_id')->constrained();
            $table->foreignId('service_variant_id')
                ->constrained('service_variants', 'id', 'pl_items_variant_fk');

            $table->decimal('price', 10, 2)->nullable();
            $table->enum('pricing_mode', ['fixed', 'percentage'])->nullable();

            $table->unique(
                ['provider_price_list_id', 'service_variant_id'],
                'provider_price_list_items_unique'
            );
        });

        Schema::create('provider_price_list_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provider_price_list_id')
                ->constrained('provider_price_lists', 'id', 'ppl_assignments_list_fk');

            $table->foreignId('operator_id')
                ->constrained('accounts', 'id', 'ppl_assignments_account_fk');

            $table->enum('adjustment_type', ['none', 'percentage', 'fixed'])
                ->default('none')
                ->comment('Global adjustment over list');

            $table->decimal('adjustment_value', 12, 2)
                ->nullable()
                ->comment('-10 = discount, +15 = markup');

            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('operator_id', 'assigned_to_index');

            $table->unique(
                ['provider_price_list_id', 'operator_id'],
                'provider_price_list_assignments_unique'
            );
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_price_list_assignments');
        Schema::dropIfExists('provider_price_list_items');
        Schema::dropIfExists('provider_price_lists');
    }
};
