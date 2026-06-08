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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('given_name')->nullable()->comment('Optional, for legacy systems');
            $table->string('family_name')->nullable()->comment('Optional, for legacy systems');
            $table->string('document_name')->nullable()->comment('As shown in documents');

            $table->unsignedBigInteger('nationality_id')->nullable(); // refers to lmp_countries
            $table->date('date_of_birth')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained('cat_genders');

            lmpStamps($table);
        });

        Schema::create('account_person', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();

            $table->enum('link_type', ['member', 'client'])
                ->default('member')
                ->comment('member = staff/contact; client = account-managed customer person');

            $table->foreignId('contact_department_id')->nullable()->constrained('cat_contact_departments');
            $table->foreignId('contact_position_id')->nullable()->constrained('cat_contact_positions');

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public_contact')->default(false);
            $table->boolean('is_preferred_contact_mode')->default(false);

            lmpStamps($table);

            $table->unique(['account_id', 'person_id', 'link_type'], 'account_person_unique');
        });

        Schema::create('account_contact_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete(); // The account that owns the contact link
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignId('source_account_id')->constrained('accounts')->cascadeOnDelete(); // The account the contact comes from
            $table->boolean('is_favorite')->default(false);
            $table->enum('visibility', ['private', 'shared'])->default('private');

            lmpStamps($table);

            $table->unique(['account_id', 'person_id', 'source_account_id'], 'account_contact_links_unique');
        });

        Schema::create('user_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();

            lmpStamps($table);

            $table->unique(['user_id', 'person_id']);
        });

        Schema::create('person_contact_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignId('contact_type_id')->constrained('cat_contact_types')->cascadeOnDelete();
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);

            lmpStamps($table);

            $table->unique(['person_id', 'contact_type_id', 'value'], 'person_contact_methods_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_contact_methods');
        Schema::dropIfExists('user_person');
        Schema::dropIfExists('account_contact_links');
        Schema::dropIfExists('account_person');
        Schema::dropIfExists('persons');
    }
};

