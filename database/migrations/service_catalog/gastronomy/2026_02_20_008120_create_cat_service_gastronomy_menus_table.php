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
        Schema::create('cat_service_gastronomy_menus', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->boolean('is_default')->default(false)->comment('Is this the default menu?');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        // Since the constraint name would be too long, we use a shortened name
        Schema::create('cat_service_gastronomy_menu_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_gastronomy_menu_id')
                ->constrained('cat_service_gastronomy_menus', 'id', 'sgm_translations_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_service_gastronomy_menu_translations');
        Schema::dropIfExists('cat_service_gastronomy_menus');
    }
};

