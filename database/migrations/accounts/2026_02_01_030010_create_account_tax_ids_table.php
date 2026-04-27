<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Pivot: tax IDs per account, keyed by account category (group tax_id in cat_account_categories).
     */
    public function up(): void
    {
        Schema::create('account_tax_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_category_id')->constrained('cat_account_categories');
            $table->string('value');

            lmpStamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_tax_ids');
    }
};
