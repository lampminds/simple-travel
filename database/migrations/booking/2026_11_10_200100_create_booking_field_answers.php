<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Answers captured when an agency books a package.
     *
     * Label, type and scope are copied so a later edit of the provider field
     * does not rewrite this booking. passenger_index is null for a booking-level
     * answer and otherwise matches bookings.passengers_snapshot ages (0, 1, 2…).
     */
    public function up(): void
    {
        Schema::create('booking_field_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('bookings', 'id', 'bfa_booking_fk')
                ->cascadeOnDelete();
            $table->foreignId('provider_booking_field_id')
                ->nullable()
                ->constrained('provider_booking_fields', 'id', 'bfa_field_fk')
                ->nullOnDelete();
            $table->unsignedSmallInteger('passenger_index')->nullable();
            $table->string('label_snapshot', 120);
            $table->string('input_type_snapshot', 20);
            $table->string('scope_snapshot', 20);
            $table->text('value');

            lmpStamps($table);

            $table->index(['booking_id', 'passenger_index'], 'bfa_booking_passenger_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_field_answers');
    }
};
