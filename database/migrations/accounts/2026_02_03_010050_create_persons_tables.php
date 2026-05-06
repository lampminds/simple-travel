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
            $table->string('name');

            lmpStamps($table);
        });

        Schema::create('account_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();

            $table->foreignId('contact_department_id')->constrained('cat_contact_departments');
            $table->foreignId('contact_position_id')->constrained('cat_contact_positions');

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public_contact')->default(false);
            $table->boolean('is_preferred_contact_mode')->default(false);

            lmpStamps($table);
        });

        Schema::create('account_contact_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete(); // The account that owns the contact link
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignId('source_account_id')->constrained('accounts')->cascadeOnDelete(); // The account the contact comes from
            $table->boolean('is_favorite')->default(false);
            $table->enum('visibility', ['private', 'shared'])->default('private');

            lmpStamps($table);

        });

        Schema::create('user_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();

            lmpStamps($table);
        });

        Schema::create('person_contact_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignId('contact_type_id')->constrained('cat_contact_types')->cascadeOnDelete();
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);

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
        Schema::dropIfExists('person_contact_methods');
        Schema::dropIfExists('user_person');
        Schema::dropIfExists('account_contact_links');
        Schema::dropIfExists('account_person');
        Schema::dropIfExists('persons');
    }
};

