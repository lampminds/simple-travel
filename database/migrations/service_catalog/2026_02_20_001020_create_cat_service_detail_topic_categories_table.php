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
        Schema::create('cat_service_detail_topic_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);
            $table->enum('operator_override_mode', ['none', 'append_only', 'replace', 'suppress'])->default('none')
                ->comment('none: operator cannot override nor hide; append_only: operator can only append, replace: operator can replace, suppress: operator can hide it');

            lmpStamps($table);
        });

        Schema::create('cat_service_detail_topic_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_detail_topic_category_id')
                ->constrained('cat_service_detail_topic_categories', 'id', 'sdtc_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'sdtc_lang_fk')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            lmpStamps($table);

            $table->unique(['service_detail_topic_category_id', 'language_id'], 'cat_sdtc_trans_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_service_detail_topic_category_translations');
        Schema::dropIfExists('cat_service_detail_topic_categories');
    }
};
