<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each attached feature is either included or explicitly excluded.
     * Existing rows were "selected", which means included. A missing row stays unspecified.
     */
    public function up(): void
    {
        Schema::table('service_featurables', function (Blueprint $table) {
            $table->enum('inclusion', ['included', 'excluded'])
                ->default('included')
                ->after('service_feature_id')
                ->comment('included: part of the offer; excluded: explicitly not included. No row means unspecified.');
        });

        DB::table('service_featurables')
            ->whereNull('inclusion')
            ->update(['inclusion' => 'included']);
    }

    public function down(): void
    {
        Schema::table('service_featurables', function (Blueprint $table) {
            $table->dropColumn('inclusion');
        });
    }
};
