<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A template is an ordered set of the provider's booking fields.
     *
     * Each item points at a catalog field and only adds whether that use is
     * required. Prompt, type and scope stay on the field, so the same field
     * can be used in many templates.
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
            $table->foreignId('provider_booking_field_id')
                ->constrained('provider_booking_fields', 'id', 'pbti_field_fk')
                ->restrictOnDelete();
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(9999);

            lmpStamps($table);

            $table->unique(
                ['provider_booking_template_id', 'provider_booking_field_id'],
                'pbti_template_field_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_booking_template_items');
        Schema::dropIfExists('provider_booking_templates');
    }
};
