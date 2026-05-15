<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Activity/event service profile (1:1 with service) and catalogue type assignments.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_service_catalog', function (Blueprint $table) {

            $table->id();
            $table->foreignId('operator_id')->constrained('accounts', 'id', 'fkosc_account_id');
            $table->foreignId('provider_id')->constrained('accounts', 'id', 'fkpsc_account_id');
            $table->foreignId('service_id')->constrained();
            $table->foreignId('service_variant_id')->nullable();
            $table->foreignId('service_offer_id')->constrained();
            $table->enum('status', ['active', 'hidden', 'paused', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false);

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

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operator_service_catalog');
    }
};
