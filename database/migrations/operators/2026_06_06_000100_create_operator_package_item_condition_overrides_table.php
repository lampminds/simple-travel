<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-item condition overrides when composing an operator package.
     * Each row customizes one inherited topic for one package item (variant + provider offer).
     */
    public function up(): void
    {
        Schema::create('operator_package_item_condition_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_package_item_id')
                ->constrained('operator_package_items', 'id', 'opico_items_op_fk')
                ->cascadeOnDelete();
            $table->foreignId('service_detail_topic_id')
                ->constrained('cat_service_detail_topics', 'id', 'opico_topics_fk')
                ->cascadeOnDelete();
            $table->enum('action', ['append_top', 'append_bottom', 'replace', 'suppress'])
                ->comment('append_top/bottom: add operator text; replace: replace inherited text; suppress: hide inherited text');

            lmpStamps($table);

            $table->unique(
                ['operator_package_item_id', 'service_detail_topic_id'],
                'operator_package_item_condition_overrides_unique'
            );
        });

        Schema::create('operator_package_item_condition_override_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_package_item_condition_override_id')
                ->constrained('operator_package_item_condition_overrides', 'id', 'opico_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'opico_translations_lang_fk')
                ->references('id')
                ->on('cat_languages');
            $table->text('custom_text')->nullable()
                ->comment('Operator text for append/replace; null when action is suppress');

            lmpStamps($table);

            $table->unique(
                ['operator_package_item_condition_override_id', 'language_id'],
                'opico_trans_lang_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_package_item_condition_override_translations');
        Schema::dropIfExists('operator_package_item_condition_overrides');
    }
};
