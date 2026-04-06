<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catalog of locale tags (BCP 47 / RFC 5646), seeded from CSV.
     */
    public function up(): void
    {
        Schema::create('cat_locales', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('tag', 64)->unique()->comment('BCP 47 tag, hyphen-separated (e.g. en-GB)');
            $table->string('name_en')->comment('English label for admin / reference');
            $table->boolean('active')->default(true);
            $table->smallInteger('sort_order')->default(9999);

            lmpStamps($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_locales');
    }
};
