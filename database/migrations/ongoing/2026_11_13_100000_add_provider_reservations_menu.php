<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Provider navbar: Reservas, with Datos de reservas inside it.
     *
     * Hidden for operators and agencies. The parent has no route yet, because
     * the provider booking inbox is not built; the child opens the booking-field catalog.
     */
    public function up(): void
    {
        $parentId = DB::table('cat_menus')->where('slug', 'provider_reservations')->value('id');
        if ($parentId === null) {
            $parentId = DB::table('cat_menus')->insertGetId([
                'slug' => 'provider_reservations',
                'icon' => null,
                'route' => null,
                'parent_id' => null,
                'sort_order' => 6,
                'active' => true,
            ]);
        }

        $childId = DB::table('cat_menus')->where('slug', 'provider_booking_fields')->value('id');
        if ($childId === null) {
            $childId = DB::table('cat_menus')->insertGetId([
                'slug' => 'provider_booking_fields',
                'icon' => null,
                'route' => 'account.booking-fields.index',
                'parent_id' => $parentId,
                'sort_order' => 1,
                'active' => true,
            ]);
        }

        $hiddenFrom = DB::table('cat_account_types')
            ->whereIn('code', ['operator', 'agency'])
            ->pluck('id');

        foreach ([$parentId, $childId] as $menuId) {
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
        }

        $translations = [
            (int) $parentId => [
                1 => [
                    'name' => 'Reservations',
                    'tip' => 'Receive and manage bookings for your services.',
                ],
                2 => [
                    'name' => 'Reservas',
                    'tip' => 'Recibe y gestiona las reservas de tus servicios.',
                ],
                3 => [
                    'name' => 'Reservas',
                    'tip' => 'Receba e gerencie as reservas dos seus serviços.',
                ],
            ],
            (int) $childId => [
                1 => [
                    'name' => 'Booking details',
                    'tip' => 'Define the extra details each service asks for when it is booked.',
                ],
                2 => [
                    'name' => 'Datos de reservas',
                    'tip' => 'Define los datos extra que cada servicio pide al reservar.',
                ],
                3 => [
                    'name' => 'Dados da reserva',
                    'tip' => 'Defina os dados extras que cada serviço pede ao reservar.',
                ],
            ],
        ];

        $languageIds = DB::table('cat_languages')->pluck('id')->map(static fn (mixed $id): int => (int) $id)->all();

        foreach ($translations as $menuId => $byLanguage) {
            foreach ($byLanguage as $languageId => $text) {
                if (! in_array((int) $languageId, $languageIds, true)) {
                    continue;
                }

                DB::table('cat_menu_translations')->updateOrInsert(
                    ['menu_id' => $menuId, 'language_id' => $languageId],
                    $text,
                );
            }
        }
    }

    public function down(): void
    {
        DB::table('cat_menus')->where('slug', 'provider_booking_fields')->delete();
        DB::table('cat_menus')->where('slug', 'provider_reservations')->delete();
    }
};
