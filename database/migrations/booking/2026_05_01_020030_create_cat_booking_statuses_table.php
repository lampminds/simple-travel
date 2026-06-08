<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cat_booking_statuses: status codes for both the main booking status and the booking items
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_booking_statuses', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['main', 'item'])->default('main')->comment('Main status for the booking, or an item status for a booking item');
            $table->string('code')->comment('Short name, in English');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999)->comment('Order for listing');

            lmpStamps($table);

            $table->unique(['type', 'code']);
        });

        Schema::create('cat_booking_status_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->constrained('cat_booking_statuses', 'id', 'bst_translations_fk');
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->string('help_tip')->nullable();
            $table->text('description')->nullable();

            lmpStamps($table);

            $table->unique(['status_id', 'language_id'], 'bst_translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_booking_status_translations');
        Schema::dropIfExists('cat_booking_statuses');
    }
};
