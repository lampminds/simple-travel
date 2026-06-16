<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Operator packages (catalog))
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_service_catalog', function (Blueprint $table) {

            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('operator_id')->constrained('accounts', 'id', 'fkosc_account_id');
            $table->enum('status', ['active', 'hidden', 'paused', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false);
            $table->enum('inventory_type', ['unlimited', 'per_day', 'per_timeslot', 'per_departure'])
                ->default('unlimited');
            $table->unsignedInteger('inventory_total')
                ->nullable()
                ->comment('Default package sales capacity when not unlimited');

            lmpStamps($table);
        });

        Schema::create('operator_service_catalog_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_service_catalog_id')
                ->constrained('operator_service_catalog', 'id', 'osct_translations_fk');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();

            lmpStamps($table);

            $table->unique(['operator_service_catalog_id', 'language_id'], 'osc_trans_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operator_service_catalog_translations');
        Schema::dropIfExists('operator_service_catalog');
    }
};
