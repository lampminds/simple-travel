<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Global passenger categories, reusable provider age policies, and special fares.
     *
     * A provider keeps a few named policies. A service optionally points at one of them.
     * A special fare hangs from a service variant or from a provider price-list item,
     * and from one age band of that policy. Age bounds are inclusive. age_max null
     * means no upper limit. The currency of a fixed amount is the currency of the
     * regular price.
     *
     * Replaces the account-scoped passenger_categories / passenger_category_price_rules
     * created on 2026-10-29, and drops services.has_differentiated_prices from that
     * same migration. Absence of a policy on the service means passenger pricing is unused.
     */
    public function up(): void
    {
        Schema::dropIfExists('service_price_rules');
        Schema::dropIfExists('service_passenger_policy_rules');
        Schema::dropIfExists('service_passenger_policies');
        Schema::dropIfExists('provider_passenger_policy_rules');
        Schema::dropIfExists('provider_passenger_policies');
        Schema::dropIfExists('passenger_category_price_rules');
        Schema::dropIfExists('passenger_category_translations');
        Schema::dropIfExists('passenger_categories');

        Schema::create('passenger_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->smallInteger('sort_order')->default(9999);

            lmpStamps($table);
        });

        Schema::create('passenger_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_category_id')
                ->constrained('passenger_categories', 'id', 'pct_category_fk')
                ->restrictOnDelete();
            $table->unsignedTinyInteger('language_id');
            $table->foreign('language_id', 'pct_language_fk')->references('id')->on('cat_languages');
            $table->string('name', 50);

            lmpStamps($table);

            $table->unique(['passenger_category_id', 'language_id'], 'pct_category_lang_unique');
        });

        Schema::create('provider_passenger_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->constrained('accounts', 'id', 'ppp_provider_fk')
                ->restrictOnDelete();
            $table->string('name', 120);

            lmpStamps($table);

            $table->unique(['provider_id', 'name'], 'ppp_provider_name_unique');
        });

        Schema::create('provider_passenger_policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_passenger_policy_id')
                ->constrained('provider_passenger_policies', 'id', 'pppr_policy_fk')
                ->restrictOnDelete();
            $table->foreignId('passenger_category_id')
                ->constrained('passenger_categories', 'id', 'pppr_category_fk')
                ->restrictOnDelete();
            $table->unsignedTinyInteger('age_min')->default(0);
            $table->unsignedTinyInteger('age_max')->nullable();

            lmpStamps($table);

            $table->unique(
                ['provider_passenger_policy_id', 'passenger_category_id'],
                'provider_policy_category_unique'
            );
        });

        Schema::create('service_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_variant_id')
                ->nullable()
                ->constrained('service_variants', 'id', 'spr_variant_fk')
                ->restrictOnDelete();
            $table->foreignId('provider_price_list_item_id')
                ->nullable()
                ->constrained('provider_price_list_items', 'id', 'spr_list_item_fk')
                ->restrictOnDelete();
            $table->foreignId('provider_passenger_policy_rule_id')
                ->constrained('provider_passenger_policy_rules', 'id', 'spr_policy_rule_fk')
                ->restrictOnDelete();
            $table->string('adjustment_type', 30);
            $table->decimal('adjustment_value', 12, 2);

            lmpStamps($table);

            $table->unique(
                ['service_variant_id', 'provider_passenger_policy_rule_id'],
                'spr_variant_rule_unique'
            );
            $table->unique(
                ['provider_price_list_item_id', 'provider_passenger_policy_rule_id'],
                'spr_list_item_rule_unique'
            );
        });

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'has_differentiated_prices')) {
                $table->dropColumn('has_differentiated_prices');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('provider_passenger_policy_id')
                ->nullable()
                ->after('account_id')
                ->constrained('provider_passenger_policies', 'id', 'services_passenger_policy_fk')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'provider_passenger_policy_id')) {
                $table->dropConstrainedForeignId('provider_passenger_policy_id');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'has_differentiated_prices')) {
                $table->boolean('has_differentiated_prices')->default(false);
            }
        });

        Schema::dropIfExists('service_price_rules');
        Schema::dropIfExists('service_passenger_policy_rules');
        Schema::dropIfExists('service_passenger_policies');
        Schema::dropIfExists('provider_passenger_policy_rules');
        Schema::dropIfExists('provider_passenger_policies');
        Schema::dropIfExists('passenger_category_translations');
        Schema::dropIfExists('passenger_categories');
    }
};
