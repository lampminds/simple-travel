<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todo_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->foreignId('todo_category_id')->constrained('todo_categories');
            $table->foreignId('original_task_id')->nullable()->constrained('todo_tasks');

            $table->enum('action_type', ['link', 'api_check', 'manual']);
            $table->string('action_url')->nullable();

            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('todo_task_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_task_id')->constrained('todo_tasks')->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            lmpStamps($table);

            $table->unique(['todo_task_id', 'language_id'], 'todo_task_trans_lang_unique');
        });

        Schema::create('todo_task_user_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_task_id')->constrained('todo_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'completed', 'ignored'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('ignored_at')->nullable();

            lmpStamps($table);

            $table->unique(['todo_task_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_task_user_assignments');
        Schema::dropIfExists('todo_task_translations');
        Schema::dropIfExists('todo_tasks');
    }
};
