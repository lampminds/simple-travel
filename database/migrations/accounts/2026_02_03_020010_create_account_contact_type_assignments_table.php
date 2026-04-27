<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Values per contact for each catalog contact type (email, phone, etc.).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_contact_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('accounts', 'id', 'account_fk')
                ->cascadeOnDelete();
            $table->foreignId('contact_id')
                ->constrained('account_contacts', 'id', 'contact_fk');
            $table->foreignId('contact_type_id')
                ->constrained('cat_contact_types', 'id', 'cat_contact_type_fk');
            $table->string('value');

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
        Schema::dropIfExists('account_contact_type_assignments');
    }
};

