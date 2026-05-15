<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_variant_id')->constrained();

            $table->foreignId('provider_id')
                ->constrained('accounts', 'id', 'provider_fk');

            $table->foreignId('operator_id')
                ->constrained('accounts', 'id', 'operator_fk');

            $table->enum('allocation_type', [
                'hard',     // cupo garantizado
                'soft',     // sin garantía (on request)
                'free_sale' // sin límite (hasta capacidad global)
            ])->default('hard');

            $table->unsignedInteger('capacity');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->boolean('active')->default(true);

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
        Schema::dropIfExists('allocations');
    }
};
