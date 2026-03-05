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
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50);
            $table->string('code', 200);
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->text('value')->nullable();
            $table->unsignedTinyInteger('mode_id')->default(0);
            $table->text('help')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('parameters');
    }
};
