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
        Schema::create('commercial_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commercial_plan_id')->constrained('commercial_plans');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');

            lmpStamps($table);
        });

        Schema::create('commercial_subscription_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commercial_subscription_id')
                ->constrained('commercial_subscriptions', 'id', 'cs_modules_fk')
                ->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->smallInteger('sort_order')->default(9999);

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
        Schema::dropIfExists('commercial_subscription_modules');
        Schema::dropIfExists('commercial_subscriptions');
    }
};
