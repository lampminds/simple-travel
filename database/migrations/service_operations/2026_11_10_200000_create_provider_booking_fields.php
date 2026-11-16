<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Provider-owned booking questions.
     *
     * A field is defined once: prompt, input type, and whether it is asked per
     * passenger or once per booking. Templates and services reference that
     * field. The agency form asks each field a single time.
     */
    public function up(): void
    {
        Schema::create('provider_booking_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('accounts', 'id', 'pbf_account_fk')
                ->restrictOnDelete();
            $table->enum('scope', ['passenger', 'booking']);
            $table->enum('input_type', ['text', 'textarea', 'number', 'date', 'time', 'phone', 'country']);
            $table->unsignedSmallInteger('sort_order')->default(9999);
            $table->boolean('is_active')->default(true);

            lmpStamps($table);

            $table->index(['account_id', 'is_active'], 'pbf_account_active_idx');
        });

        Schema::create('provider_booking_field_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_booking_field_id')
                ->constrained('provider_booking_fields', 'id', 'pbft_field_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'pbft_language_fk')->references('id')->on('cat_languages');
            $table->string('label', 120);
            $table->string('help_text', 255)->nullable();

            lmpStamps($table);

            $table->unique(
                ['provider_booking_field_id', 'language_id'],
                'pbft_field_lang_unique'
            );
        });

        Schema::create('service_booking_field_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained('services', 'id', 'sbfa_service_fk')
                ->cascadeOnDelete();
            $table->foreignId('provider_booking_field_id')
                ->constrained('provider_booking_fields', 'id', 'sbfa_field_fk')
                ->restrictOnDelete();
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(9999);

            lmpStamps($table);

            $table->unique(
                ['service_id', 'provider_booking_field_id'],
                'sbfa_service_field_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_booking_field_assignments');
        Schema::dropIfExists('provider_booking_field_translations');
        Schema::dropIfExists('provider_booking_fields');
    }
};
