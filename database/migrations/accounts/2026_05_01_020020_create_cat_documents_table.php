<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cat_documents replaces cat_account_categories
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_documents', function (Blueprint $table) {
            $table->id();
            $table->string('group')->comment('Category group - generic, in English');
            $table->string('code')->comment('Short name, in English');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');

            lmpStamps($table);

            $table->unique(['group', 'code'], 'cat_documents_group_code_unique');
        });

        Schema::create('cat_document_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('cat_documents');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('description')->nullable();

            lmpStamps($table);

            $table->unique(['document_id', 'language_id'], 'cat_document_trans_lang_unique');
        });

        if (Schema::hasTable('account_category_assignments')) {
            Schema::table('account_category_assignments', function (Blueprint $table) {
                $table->foreign('document_id')->references('id')->on('cat_documents');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('account_category_assignments')) {
            Schema::table('account_category_assignments', function (Blueprint $table) {
                $table->dropForeign(['document_id']);
            });
        }

        Schema::dropIfExists('cat_document_translations');
        Schema::dropIfExists('cat_documents');
    }
};
