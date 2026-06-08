<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * cat_account_documents replaces cat_account_tax_ids.
     *
     * Pivot: tax IDs per account, keyed by account documents (group tax_id in cat_account_categories).
     */
    public function up(): void
    {
        Schema::create('account_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('cat_documents');
            $table->string('value');

            lmpStamps($table);

            $table->unique(['account_id', 'document_id'], 'account_documents_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_documents');
    }
};
