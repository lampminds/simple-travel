<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('has_differentiated_prices')
                ->default(false);
        });

        Schema::create('passenger_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name', 100);
            $table->string('code', 50);

            $table->unsignedSmallInteger('age_min')->nullable();
            $table->unsignedSmallInteger('age_max')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(9999);
            $table->boolean('is_active')->default(true);

            lmpStamps($table);

            $table->unique(['account_id', 'code']);
            $table->index(['account_id', 'is_active']);
        });

        Schema::create('passenger_category_price_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('passenger_category_id')
                ->unique()
                ->constrained('passenger_categories')
                ->restrictOnDelete();

            $table->enum('adjustment_type', [
                'regular',
                'percentage_discount',
                'percentage_surcharge',
                'fixed_discount',
                'fixed_surcharge',
                'fixed_price',
            ])->default('regular');

            $table->decimal('adjustment_value', 12, 2)->default(0);

            $table->boolean('is_active')->default(true);

            lmpStamps($table);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('has_differentiated_prices');
        });

        Schema::dropIfExists('passenger_category_price_rules');
        Schema::dropIfExists('passenger_categories');
    }
};
