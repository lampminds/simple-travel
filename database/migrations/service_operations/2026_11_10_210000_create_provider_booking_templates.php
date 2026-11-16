<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A template owns its booking questions.
     *
     * Replicating a template onto a service snapshots each item into the
     * provider catalog once (source_template_item_id). Later template edits
     * do not rewrite that snapshot. Another service that replicates the same
     * item reuses the snapshot, so the agency is asked once.
     */
    public function up(): void
    {
        Schema::create('provider_booking_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('accounts', 'id', 'pbt_account_fk')
                ->restrictOnDelete();
            $table->string('name', 120);
            $table->unsignedSmallInteger('sort_order')->default(9999);

            lmpStamps($table);

            $table->unique(['account_id', 'name'], 'pbt_account_name_unique');
        });

        Schema::create('provider_booking_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_booking_template_id')
                ->constrained('provider_booking_templates', 'id', 'pbti_template_fk')
                ->cascadeOnDelete();
            $table->enum('scope', ['passenger', 'booking']);
            $table->enum('input_type', ['text', 'textarea', 'date', 'time', 'phone', 'country']);
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(9999);

            lmpStamps($table);
        });

        Schema::create('provider_booking_template_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_booking_template_item_id')
                ->constrained('provider_booking_template_items', 'id', 'pbtit_item_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'pbtit_language_fk')->references('id')->on('cat_languages');
            $table->string('label', 120);
            $table->string('help_text', 255)->nullable();

            lmpStamps($table);

            $table->unique(
                ['provider_booking_template_item_id', 'language_id'],
                'pbtit_item_lang_unique'
            );
        });

        Schema::table('provider_booking_fields', function (Blueprint $table) {
            $table->foreignId('source_template_item_id')
                ->nullable()
                ->unique('pbf_source_item_unique')
                ->constrained('provider_booking_template_items', 'id', 'pbf_source_item_fk')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('provider_booking_fields', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_template_item_id');
        });

        Schema::dropIfExists('provider_booking_template_item_translations');
        Schema::dropIfExists('provider_booking_template_items');
        Schema::dropIfExists('provider_booking_templates');
    }
};
