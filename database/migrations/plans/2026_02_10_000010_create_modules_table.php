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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('module_account_types', function (Blueprint $table) {
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_type_id')->constrained('cat_account_types')->cascadeOnDelete();

            $table->unique(['module_id', 'account_type_id']);

            lmpStamps($table);
        });

        Schema::create('module_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('description')->nullable();

            $table->unique(['module_id', 'language_id']);

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
        Schema::dropIfExists('module_translations');
        Schema::dropIfExists('modules');
    }
};
