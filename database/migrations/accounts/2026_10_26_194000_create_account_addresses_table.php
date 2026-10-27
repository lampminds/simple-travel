<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links an account to one or more rows in addresses.
     * The edit forms currently maintain only the primary address.
     */
    public function up(): void
    {
        Schema::create('account_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('primary');

            lmpStamps($table);

            $table->unique(['account_id', 'address_id'], 'account_addresses_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_addresses');
    }
};
