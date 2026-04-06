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
        Schema::create('cat_service_detail_topics', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Short name, in English');
            $table->foreignId('service_detail_topic_category_id')->nullable()->constrained('cat_service_detail_topic_categories', 'id', 'sdtc_sdc_fk');
            $table->enum('visibility', ['public', 'operator', 'internal'])->nullable()->default('public');
            $table->smallinteger('sort_order')->default(9999)->comment('Order for listing');
            $table->boolean('active')->default(true);

            lmpStamps($table);
        });

        Schema::create('cat_service_detail_topic_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_detail_topic_id')
                ->constrained('cat_service_detail_topics', 'id', 'sdt_sd_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id')->references('id')->on('cat_languages');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

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
        Schema::dropIfExists('cat_service_detail_topic_translations');
        Schema::dropIfExists('cat_service_detail_topics');
    }
};
