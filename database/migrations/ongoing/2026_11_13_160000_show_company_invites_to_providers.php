<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Providers can invite other providers, including providers that are also operators.
     * The company-invite menu was hidden for the provider account type.
     */
    public function up(): void
    {
        $menuId = DB::table('cat_menus')->where('slug', 'invite_company')->value('id');
        $providerTypeId = DB::table('cat_account_types')->where('code', 'provider')->value('id');

        if ($menuId === null || $providerTypeId === null) {
            return;
        }

        DB::table('cat_menu_account_type_exclusions')
            ->where('menu_id', $menuId)
            ->where('account_type_id', $providerTypeId)
            ->delete();
    }

    public function down(): void
    {
        $menuId = DB::table('cat_menus')->where('slug', 'invite_company')->value('id');
        $providerTypeId = DB::table('cat_account_types')->where('code', 'provider')->value('id');

        if ($menuId === null || $providerTypeId === null) {
            return;
        }

        $exists = DB::table('cat_menu_account_type_exclusions')
            ->where('menu_id', $menuId)
            ->where('account_type_id', $providerTypeId)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('cat_menu_account_type_exclusions')->insert([
            'menu_id' => $menuId,
            'account_type_id' => $providerTypeId,
        ]);
    }
};
