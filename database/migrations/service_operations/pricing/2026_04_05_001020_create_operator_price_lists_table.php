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
        Schema::create('operator_price_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('operator_id')->constrained('accounts', 'id', 'pl_account_fk');

            $table->string('name');
            $table->unsignedTinyInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');

            $table->boolean('is_active')->default(true);

            lmpStamps($table);
        });

        Schema::create('operator_price_list_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operator_price_list_id')->constrained();
            $table->foreignId('operator_package_item_id')
                ->constrained('operator_package_items', 'id', 'pl_items_op_fk');

            $table->decimal('price', 10, 2)->nullable();
            $table->enum('pricing_mode', ['fixed_delta', 'percentage', 'fixed_price'])->nullable();

            $table->unique(
                ['operator_price_list_id', 'operator_package_item_id'],
                'opl_items_list_package_unique'
            );
        });

        Schema::create('operator_price_list_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operator_price_list_id')
                ->constrained('operator_price_lists', 'id', 'opl_assignments_list_fk');

            $table->foreignId('agency_id')
                ->constrained('accounts', 'id', 'opl_assignments_account_fk');

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

            $table->index('agency_id', 'assigned_to_index');

            $table->unique(
                ['operator_price_list_id', 'agency_id'],
                'operator_price_list_assignments_unique'
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
        Schema::dropIfExists('operator_price_list_assignments');
        Schema::dropIfExists('operator_price_list_items');
        Schema::dropIfExists('operator_price_lists');
    }
};
