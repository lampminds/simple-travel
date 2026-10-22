<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Where images for a service are uploaded: on the service, or on each variant.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->enum('images_scope', ['service', 'variant'])
                ->default('service')
                ->after('is_public')
                ->comment('service: images belong to the service; variant: each variant has its own images.');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('images_scope');
        });
    }
};
