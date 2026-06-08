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
        Schema::create('cat_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('Short name, in English');
            $table->foreignId('account_type_id')
                ->comment('Account type this FAQ is intended for; null = all types')
                ->nullable()
                ->constrained('cat_account_types');
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();

            lmpStamps($table);

            $table->unique(['code', 'account_type_id'], 'cat_faqs_code_account_type_unique');
        });

        Schema::create('cat_faq_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained('cat_faqs')->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('question')->nullable();
            $table->text('answer')->nullable();

            lmpStamps($table);

            $table->unique(['faq_id', 'language_id'], 'cat_faq_trans_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_faq_translations');
        Schema::dropIfExists('cat_faqs');
    }
};
