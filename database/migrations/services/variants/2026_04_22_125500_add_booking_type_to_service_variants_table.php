<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add booking_type to service_variants for Filament variants form.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('service_variants', 'booking_type')) {
            Schema::table('service_variants', function (Blueprint $table): void {
                $table->enum('booking_type', ['instant', 'request'])
                    ->default('request')
                    ->after('inventory_type');
            });
        }
    }

    /**
     * Remove booking_type from service_variants.
     */
    public function down(): void
    {
        if (Schema::hasColumn('service_variants', 'booking_type')) {
            Schema::table('service_variants', function (Blueprint $table): void {
                $table->dropColumn('booking_type');
            });
        }
    }
};
