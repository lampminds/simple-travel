<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Operator-facing reservations inbox (agency keeps menu id 9).
     */
    public function up(): void
    {
        if (DB::table('cat_menus')->where('slug', 'operator_reservations')->exists()) {
            return;
        }

        DB::table('cat_menus')->insert([
            'id' => 29,
            'slug' => 'operator_reservations',
            'icon' => null,
            'route' => 'account.operator.reservations.index',
            'parent_id' => null,
            'sort_order' => 6,
            'active' => 1,
        ]);

        DB::table('cat_menu_translations')->insert([
            [
                'menu_id' => 29,
                'language_id' => 1,
                'name' => 'Reservations',
                'tip' => 'Review and confirm bookings submitted by linked agencies.',
            ],
            [
                'menu_id' => 29,
                'language_id' => 2,
                'name' => 'Reservas',
                'tip' => 'Revisá y confirmá reservas enviadas por agencias vinculadas.',
            ],
        ]);

        // Exclude provider and agency by code (ids vary per environment); operator must see this item.
        $providerTypeId = DB::table('cat_account_types')->where('code', 'provider')->value('id');
        $agencyTypeId = DB::table('cat_account_types')->where('code', 'agency')->value('id');

        $exclusions = [];
        if ($providerTypeId !== null) {
            $exclusions[] = ['menu_id' => 29, 'account_type_id' => (int) $providerTypeId];
        }
        if ($agencyTypeId !== null) {
            $exclusions[] = ['menu_id' => 29, 'account_type_id' => (int) $agencyTypeId];
        }
        if ($exclusions !== []) {
            DB::table('cat_menu_account_type_exclusions')->insert($exclusions);
        }
    }

    public function down(): void
    {
        DB::table('cat_menu_account_type_exclusions')->where('menu_id', 29)->delete();
        DB::table('cat_menu_translations')->where('menu_id', 29)->delete();
        DB::table('cat_menus')->where('id', 29)->delete();
    }
};
