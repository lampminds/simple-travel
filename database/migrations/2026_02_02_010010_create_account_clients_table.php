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
        Schema::create('account_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('name')->comment('The short name chosen by the account');
            $table->string('commercial_name')->nullable()->comment('Full name of the account\'s client');

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->unsignedBigInteger('city_id')->nullable(); //References lmp_cities.id on addons connection (no FK across DBs)
            $table->string('postal_code')->nullable();

            $table->enum('status', ['active', 'onhold', 'inactive', 'terminated'])->default('active');

            $table->foreignId('linked_account_id')->nullable()->constrained('accounts')->onDelete('set null')->comment('If this client is also an account in our system');

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
        Schema::dropIfExists('account_clients');
    }
};

