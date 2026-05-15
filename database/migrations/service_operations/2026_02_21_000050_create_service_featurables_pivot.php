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
        Schema::create('service_featurables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_feature_id')
                ->constrained('cat_service_features', 'id', 'sf_fk');

            $table->morphs('service_featurable', 'sf_morph');  // service_featurable_id + service_featurable_type

            lmpStamps($table);

            $table->unique([
                'service_feature_id',
                'service_featurable_id',
                'service_featurable_type'
            ], 'sf_sf_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_featurables');
    }
};
