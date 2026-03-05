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
        Schema::create('contact_type_translations', function (BluePrint $table) {
            $table->id();
            $table->foreignId('contact_type_id')->constrained();
            $table->foreignId('language_id')->constrained();
            $table->string('name');
            $table->string('mask')->nullable()->comment('Mask for formatting the value');
            $table->string('validation')->nullable()->comment('Validation for the value');
            $table->boolean('active')->default(true)->comment('Is this contact type active?');

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
        Schema::dropIfExists('contact_type_translations');
    }
};
