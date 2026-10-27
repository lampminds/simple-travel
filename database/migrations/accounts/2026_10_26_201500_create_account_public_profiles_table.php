<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public website copy for an account (one profile, translated title, subtitle and about).
     */
    public function up(): void
    {
        Schema::create('account_public_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('video_url', 500)->nullable();

            lmpStamps($table);

            $table->unique('account_id', 'account_public_profiles_account_unique');
        });

        Schema::create('account_public_profile_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_public_profile_id')
                ->constrained('account_public_profiles', 'id', 'app_trans_profile_fk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'app_trans_lang_fk')->references('id')->on('cat_languages');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('about')->nullable();

            lmpStamps($table);

            $table->unique(['account_public_profile_id', 'language_id'], 'app_trans_lang_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_public_profile_translations');
        Schema::dropIfExists('account_public_profiles');
    }
};
