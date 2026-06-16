<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_package_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_service_catalog_id')
                ->constrained('operator_service_catalog', 'id', 'opar_catalog_fk');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('weekday_mask')->nullable()
                ->comment('Bitmask for weekdays (1=Mon, 2=Tue, 4=Wed, ... 64=Sun)');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('operator_package_availability_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_package_availability_rule_id')
                ->constrained('operator_package_availability_rules', 'id', 'opats_rule_fk');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('cutoff_minutes')->nullable();
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999);

            lmpStamps($table);
        });

        Schema::create('operator_package_availability_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_service_catalog_id')
                ->constrained('operator_service_catalog', 'id', 'opao_catalog_fk');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('closed')->default(false);
            $table->string('reason')->nullable();

            lmpStamps($table);

            $table->unique(
                ['operator_service_catalog_id', 'date', 'start_time'],
                'opkg_date_time_unique',
            );
        });

        Schema::create('package_allocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('operator_service_catalog_id')
                ->constrained('operator_service_catalog', 'id', 'palloc_catalog_fk');

            $table->foreignId('operator_id')
                ->constrained('accounts', 'id', 'palloc_operator_fk');

            $table->foreignId('agency_id')
                ->constrained('accounts', 'id', 'palloc_agency_fk');

            $table->enum('allocation_type', [
                'hard',
                'soft',
                'free_sale',
            ])->default('hard');

            $table->unsignedInteger('capacity');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('active')->default(true);

            lmpStamps($table);

            $table->unique(
                ['operator_id', 'agency_id', 'operator_service_catalog_id', 'start_date'],
                'package_allocations_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_allocations');
        Schema::dropIfExists('operator_package_availability_overrides');
        Schema::dropIfExists('operator_package_availability_time_slots');
        Schema::dropIfExists('operator_package_availability_rules');
    }
};
