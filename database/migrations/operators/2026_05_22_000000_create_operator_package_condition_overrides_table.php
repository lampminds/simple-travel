<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Package-level condition overrides (not tied to a single catalog item).
     * Inherits topic semantics from cat_service_detail_topics; custom text is per language.
     */
    public function up(): void
    {
        Schema::create('operator_package_condition_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_package_id')
                ->constrained('operator_service_catalog', 'id', 'osct_opco_fk')
                ->cascadeOnDelete();
            $table->foreignId('service_detail_topic_id')
                ->constrained('cat_service_detail_topics', 'id', 'sdt_opco_fk')
                ->cascadeOnDelete();
            $table->enum('action', ['append_top', 'append_bottom', 'replace', 'suppress'])
                ->comment('append_top/bottom: add operator text; replace: replace inherited text; suppress: hide inherited text');

            lmpStamps($table);

            $table->unique(
                ['operator_package_id', 'service_detail_topic_id'],
                'operator_package_condition_overrides_unique'
            );
        });

        Schema::create('operator_package_condition_override_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_package_condition_override_id')
                ->constrained('operator_package_condition_overrides', 'id', 'opco_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'opco_translations_lang_fk')
                ->references('id')
                ->on('cat_languages');
            $table->text('custom_text')->nullable()
                ->comment('Operator text for append/replace; null when action is suppress');

            lmpStamps($table);

            $table->unique(
                ['operator_package_condition_override_id', 'language_id'],
                'opco_trans_lang_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_package_condition_override_translations');
        Schema::dropIfExists('operator_package_condition_overrides');
    }
};
