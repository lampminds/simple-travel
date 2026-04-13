<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('service_feature_scopes') && ! Schema::hasTable('cat_service_feature_scopes')) {
            Schema::rename('service_feature_scopes', 'cat_service_feature_scopes');
            $this->syncMigrationRecordToNewFilename();

            return;
        }

        if (! Schema::hasTable('service_feature_scopes') && Schema::hasTable('cat_service_feature_scopes')) {
            $this->syncMigrationRecordToNewFilename();

            return;
        }

        if (Schema::hasTable('cat_service_feature_scopes')) {
            return;
        }

        Schema::create('cat_service_feature_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')->constrained('cat_service_types');

            $table->foreignId('service_feature_id')
                ->constrained('cat_service_features', 'id', 'cat_service_feature_scopes_feature_fk');

            lmpStamps($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_service_feature_scopes');
    }

    private function syncMigrationRecordToNewFilename(): void
    {
        DB::table('migrations')
            ->where('migration', '2026_02_21_000060_create_service_feature_scopes_table')
            ->update(['migration' => '2026_02_21_000060_create_cat_service_feature_scopes_table']);
    }
};
