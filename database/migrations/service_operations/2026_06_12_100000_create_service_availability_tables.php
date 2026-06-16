<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Service-level availability (mass closures and master schedule for all variants).
     */
    public function up(): void
    {
        Schema::create('service_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('weekday_mask')->nullable()
                ->comment('Bitmask for weekdays (1=Mon, 2=Tue, 4=Wed, ... 64=Sun)');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('service_availability_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->date('date');
            $table->date('end_date')->nullable()
                ->comment('Inclusive end date; null means single-day closure');
            $table->boolean('closed')->default(true);
            $table->string('reason')->nullable();

            lmpStamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_availability_overrides');
        Schema::dropIfExists('service_availability_rules');
    }
};
