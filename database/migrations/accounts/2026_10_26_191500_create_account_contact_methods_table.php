<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contact channels (email, phone, WhatsApp, …) stored on an account.
     */
    public function up(): void
    {
        Schema::create('account_contact_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_type_id')->constrained('cat_contact_types')->cascadeOnDelete();
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);

            lmpStamps($table);

            $table->unique(['account_id', 'contact_type_id', 'value'], 'account_contact_methods_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_contact_methods');
    }
};
