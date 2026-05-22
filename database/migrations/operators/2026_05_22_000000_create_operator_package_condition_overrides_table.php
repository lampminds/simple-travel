<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Operator packages items
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_package_condition_overrides', function (Blueprint $table) {

            $table->id();
            $table->foreignId('operator_package_id')->constrained('operator_service_catalog')->cascadeOnDelete();
            $table->foreignId('service_detail_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['append_top', 'append_bottom', 'replace', 'supress'])
                ->comment('append: append to the top/bottom, replace: replace the entire list, supress: supress the entire list');

            lmpStamps($table);
        });

        Schema::create('operator_package_condition_overrides_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_package_condition_override_id')
                ->constrained('operator_package_condition_overrides', 'id', 'opco_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'opco_translations_lang_fk')
                ->references('id')->on('cat_languages');
            $table->string('custom_text')->nullable();

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
        Schema::dropIfExists('operator_package_condition_overrides_translations');
        Schema::dropIfExists('operator_package_condition_overrides');
    }
};
