<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Provider navbar: passenger policies inside the Catalog submenu.
     *
     * Hidden for operators and agencies, same as the other catalog items.
     */
    public function up(): void
    {
        $catalogId = DB::table('cat_menus')->where('slug', 'catalog')->value('id');
        if ($catalogId === null) {
            return;
        }

        $menuId = DB::table('cat_menus')->where('slug', 'provider_passenger_policies')->value('id');
        if ($menuId === null) {
            $menuId = DB::table('cat_menus')->insertGetId([
                'slug' => 'provider_passenger_policies',
                'icon' => null,
                'route' => 'account.passenger-policies.index',
                'parent_id' => $catalogId,
                'sort_order' => 4,
                'active' => true,
            ]);
        } else {
            DB::table('cat_menus')->where('id', $menuId)->update([
                'route' => 'account.passenger-policies.index',
                'parent_id' => $catalogId,
                'sort_order' => 4,
                'active' => true,
            ]);
        }

        $hiddenFrom = DB::table('cat_account_types')
            ->whereIn('code', ['operator', 'agency'])
            ->pluck('id');

        foreach ($hiddenFrom as $accountTypeId) {
            $exists = DB::table('cat_menu_account_type_exclusions')
                ->where('menu_id', $menuId)
                ->where('account_type_id', $accountTypeId)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('cat_menu_account_type_exclusions')->insert([
                'menu_id' => $menuId,
                'account_type_id' => $accountTypeId,
            ]);
        }

        $translations = [
            1 => [
                'name' => 'Passenger policies',
                'tip' => 'Define age policies and assign them to your services.',
            ],
            2 => [
                'name' => 'Políticas de pasajeros',
                'tip' => 'Define las edades de cada política y asígnalas a tus servicios.',
            ],
            3 => [
                'name' => 'Políticas de passageiros',
                'tip' => 'Defina as idades de cada política e atribua-as aos seus serviços.',
            ],
        ];

        $languageIds = DB::table('cat_languages')->pluck('id')->map(static fn (mixed $id): int => (int) $id)->all();

        foreach ($translations as $languageId => $text) {
            if (! in_array((int) $languageId, $languageIds, true)) {
                continue;
            }

            DB::table('cat_menu_translations')->updateOrInsert(
                ['menu_id' => $menuId, 'language_id' => $languageId],
                $text,
            );
        }
    }

    public function down(): void
    {
        DB::table('cat_menus')->where('slug', 'provider_passenger_policies')->delete();
    }
};
