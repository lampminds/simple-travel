<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menus with an exclusion row for every active account type behave like the old
     * "no assignments" whitelist and should show for everyone under the new model.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cat_menu_account_type_exclusions')) {
            return;
        }

        $allTypeIds = DB::table('cat_account_types')->where('active', true)->pluck('id');
        if ($allTypeIds->isEmpty()) {
            return;
        }

        foreach (DB::table('cat_menus')->pluck('id') as $menuId) {
            $excludedForMenu = DB::table('cat_menu_account_type_exclusions')
                ->where('menu_id', $menuId)
                ->whereIn('account_type_id', $allTypeIds)
                ->pluck('account_type_id');

            if ($excludedForMenu->count() === $allTypeIds->count()) {
                DB::table('cat_menu_account_type_exclusions')->where('menu_id', $menuId)->delete();
            }
        }
    }

    public function down(): void
    {
        // Irreversible cleanup of incorrectly migrated rows.
    }
};
