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
        Schema::create('ai_knowledge_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();  // ie: "edit_image"
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });

        Schema::create('ai_knowledge_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ai_knowledge_item_id')
                ->constrained('ai_knowledge_items')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');

            $table->string('title');
            $table->text('content');
            $table->text('content_short')->nullable();

            $table->string('url', 500)->nullable();
            $table->json('tags')->nullable();

            // embedding per language (nullable: drafts, API errors, or rows not yet embedded)
            $table->json('embedding')->nullable();
            $table->string('embedding_model', 100)->nullable();
            $table->string('embedding_version', 50)->nullable();

            $table->timestamps();

            $table->unique(['ai_knowledge_item_id', 'language_id'], 'ai_knowledge_item_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_knowledge_translations');
        Schema::dropIfExists('ai_knowledge_items');
    }
};
