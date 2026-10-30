<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fixed passenger-category amounts need a currency. Percentages do not.
     */
    public function up(): void
    {
        Schema::table('passenger_category_price_rules', function (Blueprint $table) {
            $table->unsignedTinyInteger('currency_id')->nullable()->after('adjustment_value');
            $table->foreign('currency_id')->references('id')->on('cat_currencies');
        });
    }

    public function down(): void
    {
        Schema::table('passenger_category_price_rules', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
