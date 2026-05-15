<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links accounts to business types ({@see \App\Models\AccountType}).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_type_id')
                ->constrained('cat_account_types', 'id', 'aat_type_fk')
                ->cascadeOnDelete();

            $table->unique(['account_id', 'account_type_id']);

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
        Schema::dropIfExists('account_type_assignments');
    }
};
