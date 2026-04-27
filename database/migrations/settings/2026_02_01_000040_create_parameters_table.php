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
        Schema::create('cat_parameter_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50);
            $table->string('subcategory', 50)->nullable();
            $table->string('code', 200)->unique();
            $table->enum('type', ['string', 'integer', 'decimal', 'boolean', 'json', 'array', 'object', 'date', 'datetime', 'time']);
            $table->enum('scope', ['system', 'tenant']);
            $table->boolean('has_default')->default(false);
            $table->enum('ui_component', ['input', 'select', 'checkbox', 'radio', 'switch', 'textarea', 'editor', 'date', 'datetime', 'time'])->default('input');
            $table->json('ui_options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('default_value')->nullable();
            $table->json('validation_rules')->nullable();
            $table->text('comments')->nullable()->comment('Internal comments');
            lmpStamps($table);
        });

        Schema::create('cat_parameter_definition_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_definition_id')
                ->constrained('cat_parameter_definitions', 'id', 'pd_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'pd_translations_lang_fk')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->text('help')->nullable()->comment('Help text for the user');
            lmpStamps($table);

            $table->unique(['parameter_definition_id', 'language_id'], 'pd_trans_lang_unique');
        });

        Schema::create('parameter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_definition_id')
                ->constrained('cat_parameter_definitions')
                ->cascadeOnDelete();
            $table->foreignId('account_id')
                ->nullable()
                ->constrained('accounts')
                ->cascadeOnDelete();
            $table->text('value')->nullable();
            lmpStamps($table);

            $table->index(['parameter_definition_id', 'account_id']);
        });

        Schema::create('cat_parameter_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_definition_id')
                ->constrained('cat_parameter_definitions')
                ->cascadeOnDelete();
            $table->string('value');
            $table->integer('sort_order')->default(0);
            lmpStamps($table);
        });

        Schema::create('cat_parameter_option_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_option_id')
                ->constrained('cat_parameter_options', 'id', 'po_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'po_translations_lang_fk')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            lmpStamps($table);

            $table->unique(['parameter_option_id', 'language_id'], 'po_trans_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_parameter_option_translations');
        Schema::dropIfExists('cat_parameter_options');
        Schema::dropIfExists('cat_parameter_definition_translations');
        Schema::dropIfExists('parameter_values');
        Schema::dropIfExists('cat_parameter_definitions');
    }
};
