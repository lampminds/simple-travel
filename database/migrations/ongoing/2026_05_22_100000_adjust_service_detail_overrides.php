<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cat_service_detail_topic_categories', function (Blueprint $table) {
            $table->enum('operator_override_mode', ['none', 'append_only', 'replace', 'suppress'])->default('none')
                ->after('active')
                ->comment('none: operator cannot override nor hide; append_only: operator can only append, replace: operator can replace, suppress: operator can hide it');
        });

        Schema::table('service_details', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(false)->after('active');
            $table->foreignId('condition_key_id')
                ->nullable()
                ->constrained('cat_service_detail_condition_keys')
                ->after('is_mandatory');
        }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_service_detail_topic_categories', function (Blueprint $table) {
            $table->dropColumn('operator_override_mode');
        });
        Schema::table('service_details', function (Blueprint $table) {
            $table->dropColumn('is_mandatory');
            $table->dropColumn('condition_key_id');
        });
    }
};
