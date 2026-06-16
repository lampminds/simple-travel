<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_assistant_messages', function (Blueprint $table) {
            $table->id();
            $table->string('usage_type', 32)->default('assistant')->index();
            $table->string('source', 100)->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_type_id')->nullable()->constrained('cat_account_types')->nullOnDelete();
            $table->unsignedTinyInteger('language_id')->nullable();
            $table->foreign('language_id')->references('id')->on('cat_languages')->nullOnDelete();

            $table->text('question')->nullable();
            $table->text('answer')->nullable();

            $table->string('status', 32)->default('success')->index();
            $table->string('error_message', 500)->nullable();

            $table->string('chat_model', 100)->nullable();
            $table->string('embedding_model', 100)->nullable();

            $table->unsignedInteger('embedding_prompt_tokens')->nullable();
            $table->unsignedInteger('chat_prompt_tokens')->nullable();
            $table->unsignedInteger('chat_completion_tokens')->nullable();
            $table->unsignedInteger('chat_total_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable()
                ->comment('Sum of embedding + chat total tokens when available');
            $table->decimal('estimated_usd', 12, 6)->nullable()
                ->comment('Estimated OpenAI cost in USD at log time; 0 for free translation APIs');

            $table->json('source_keys')->nullable()
                ->comment('ai_knowledge_items.key values used as retrieval context');

            lmpStamps($table);

            $table->index(['account_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_messages');
    }
};
