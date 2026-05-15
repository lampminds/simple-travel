<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Which menu formats from the catalogue apply to a gastronomy service profile.
     */
    public function up(): void
    {
        Schema::create('service_gastronomy_menu_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_id')
                ->constrained('service_gastronomies', 'id', 'sgma_sg_fk');
            $table->foreignId('service_gastronomy_menu_id')
                ->constrained('cat_service_gastronomy_menus', 'id', 'sgma_menu_fk');

            $table->unique(['service_gastronomy_id', 'service_gastronomy_menu_id'], 'sgma_unique_pair');

            lmpStamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_gastronomy_menu_assignments');
    }
};
