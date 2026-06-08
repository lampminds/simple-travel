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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('service_type_id')->constrained('cat_service_types');
            $table->unsignedBigInteger('city_id')->nullable(); //References lmp_cities.id on addons connection (no FK across DBs)
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false)->comment('Visible in website');
            $table->enum('booking_mode', ['instant', 'request', 'external', 'quote'])->default('instant');
            $table->integer('confirmation_time_hours')->nullable()->comment('Confirmation time in hours');
            $table->enum('status', ['active', 'suspended', 'discontinued', 'onhold', 'terminated'])->default('active');
            $table->enum('ownership_type', ['provider', 'operator'])->default('provider')
                ->comment('Defines if this service requires offers flow or not');

            lmpStamps($table);
        });

        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();

            lmpStamps($table);

            $table->unique(['service_id', 'language_id'], 'service_trans_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_translations');
    }
};
