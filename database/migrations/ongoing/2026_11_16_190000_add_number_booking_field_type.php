<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds a general numeric type to provider booking fields.
     *
     * Integers and decimals are accepted. The value is stored as text.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE provider_booking_fields MODIFY input_type ENUM('text', 'textarea', 'number', 'date', 'time', 'phone', 'country') NOT NULL");
    }

    public function down(): void
    {
        DB::table('provider_booking_fields')
            ->where('input_type', 'number')
            ->update(['input_type' => 'text']);

        DB::statement("ALTER TABLE provider_booking_fields MODIFY input_type ENUM('text', 'textarea', 'date', 'time', 'phone', 'country') NOT NULL");
    }
};
