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
        Schema::create('service_transfer_vehicle_types', function (Blueprint $table) {

            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('service_transfer_vehicle_type_category_id')->nullable();
            $table->foreign('service_transfer_vehicle_type_category_id', 'stvt_category_fk')
                ->references('id')
                ->on('cat_service_transfer_vehicle_type_categories');

            $table->string('name'); // sedan, van, minibus, etc.
            $table->smallInteger('sort_order')->default(9999);
            $table->unsignedSmallInteger('max_passengers')->nullable();
            $table->unsignedSmallInteger('max_luggage')->nullable();
            $table->boolean('active')->default(true);

            lmpStamps($table);

            $table->unique(['account_id', 'code'], 'stvt_account_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_transfer_vehicle_types');
    }
};
