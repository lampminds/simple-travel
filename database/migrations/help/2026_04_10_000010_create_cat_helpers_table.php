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
        Schema::create('cat_helpers', function (Blueprint $table) {
            $table->id();
            $table->string('screen_code')->comment('Screen code, in English');
            $table->string('code')->comment('Short name, in English');
            $table->foreignId('account_type_id')
                ->comment('optional, account type for which this helper is intended')
                ->nullable()
                ->constrained('cat_account_types');
            $table->foreignId('service_type_id')
                ->comment('optional, service type for which this helper is intended')
                ->nullable()
                ->constrained('cat_service_types');
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();

            lmpStamps($table);

            $table->unique(['screen_code', 'code', 'account_type_id', 'service_type_id'], 'unique_helper');
        });

        Schema::create('cat_helper_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('helper_id')->constrained('cat_helpers')->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->text('text')->nullable();

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
        Schema::dropIfExists('cat_helper_translations');
        Schema::dropIfExists('cat_helpers');
    }
};
