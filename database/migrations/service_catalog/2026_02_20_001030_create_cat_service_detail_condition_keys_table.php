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
        Schema::create('cat_service_detail_condition_keys', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->string('category');
            $table->string('description');
            $table->enum('consolidation_mode', ['deduplicate', 'conflict_check', 'most_restrictive', 'show_all'])->default('deduplicate')->comment('How to handle duplicate condition keys');

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
        Schema::dropIfExists('cat_service_detail_condition_keys');
    }

};
