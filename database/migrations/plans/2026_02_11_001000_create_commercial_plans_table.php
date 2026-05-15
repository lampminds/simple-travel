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
        Schema::create('commercial_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('commercial_plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_plan_id')->constrained('commercial_plans', 'id', 'cp_translations_fk')->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('description')->nullable();

            lmpStamps($table);
        });

        Schema::create('commercial_plan_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_plan_id')->constrained('commercial_plans', 'id', 'cp_modules_fk')->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules', 'id', 'pt_modules_fk')->cascadeOnDelete();
            $table->smallInteger('sort_order')->default(9999);

            lmpStamps($table);
        });

        Schema::create('commercial_module_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained('modules', 'id', 'cm_prices_fk')->cascadeOnDelete();
            $table->enum('billing_type', ['fixed', 'per_user', 'hybrid', 'usage', 'included'])->default('fixed')
                ->comment('website:fixed, crm:per_user, core:hybrid, api:usage');
            $table->decimal('base_price', 10, 2)->nullable();
            $table->integer('included_users')->nullable();
            $table->decimal('price_per_user', 10, 2)->nullable();
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('commercial_module_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_price_id')->constrained('commercial_module_prices', 'id', 'module_prices_fk')->cascadeOnDelete();
            $table->smallInteger('from_users')->nullable();
            $table->smallInteger('to_users')->nullable();
            $table->decimal('price_per_user', 10, 2)->nullable();

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
        Schema::dropIfExists('commercial_module_price_tiers');
        Schema::dropIfExists('commercial_module_prices');
        Schema::dropIfExists('commercial_plan_modules');
        Schema::dropIfExists('commercial_plan_translations');
        Schema::dropIfExists('commercial_plans');
    }
};
