<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix operator_reservations menu: exclusions must hide provider + agency, not operator.
     * Account type ids are resolved by code (ids differ across environments).
     */
    public function up(): void
    {
        $menuId = DB::table('cat_menus')->where('slug', 'operator_reservations')->value('id');

        if ($menuId === null) {
            DB::table('cat_menus')->insert([
                'id' => 29,
                'slug' => 'operator_reservations',
                'icon' => null,
                'route' => 'account.operator.reservations.index',
                'parent_id' => null,
                'sort_order' => 6,
                'active' => 1,
            ]);
            $menuId = 29;

            if (! DB::table('cat_menu_translations')->where('menu_id', $menuId)->exists()) {
                DB::table('cat_menu_translations')->insert([
                    [
                        'menu_id' => $menuId,
                        'language_id' => 1,
                        'name' => 'Reservations',
                        'tip' => 'Review and confirm bookings submitted by linked agencies.',
                    ],
                    [
                        'menu_id' => $menuId,
                        'language_id' => 2,
                        'name' => 'Reservas',
                        'tip' => 'Revisá y confirmá reservas enviadas por agencias vinculadas.',
                    ],
                ]);
            }
        }

        DB::table('cat_menu_account_type_exclusions')->where('menu_id', $menuId)->delete();

        $excludeTypeIds = DB::table('cat_account_types')
            ->whereIn('code', ['provider', 'agency'])
            ->pluck('id');

        foreach ($excludeTypeIds as $typeId) {
            DB::table('cat_menu_account_type_exclusions')->insert([
                'menu_id' => (int) $menuId,
                'account_type_id' => (int) $typeId,
            ]);
        }
    }

    public function down(): void
    {
        // No rollback: prior exclusions were incorrect.
    }
};
