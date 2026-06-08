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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('agency_id')
                ->constrained('accounts');
            $table->string('legal_name');
            $table->string('trade_name')->nullable();
            $table->string('website')->nullable();

            lmpStamps($table);

            $table->unique(['agency_id', 'legal_name']);
        });

        Schema::create('organization_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained();
            $table->enum('type', ['billing'])->default('billing');

            lmpStamps($table);

            $table->unique(['organization_id', 'address_id']);
        });

        Schema::create('organization_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('cat_documents');
            $table->string('value');

            lmpStamps($table);

            $table->unique(['organization_id', 'document_id']);
        }
        );

        Schema::create('organization_person', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();

            $table->foreignId('contact_department_id')->constrained('cat_contact_departments');
            $table->foreignId('contact_position_id')->constrained('cat_contact_positions');

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public_contact')->default(false);
            $table->boolean('is_preferred_contact_mode')->default(false);

            lmpStamps($table);

            $table->unique(['organization_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_person');
        Schema::dropIfExists('organization_documents');
        Schema::dropIfExists('organization_addresses');
        Schema::dropIfExists('organizations');
    }
};
