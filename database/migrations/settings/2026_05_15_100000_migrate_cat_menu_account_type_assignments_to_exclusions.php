<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Invert whitelist rows (cat_menu_account_type_assignments) into exclusions.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cat_menu_account_type_assignments')) {
            return;
        }

        $typeIdColumn = Schema::hasColumn('cat_menu_account_type_assignments', 'account_type_id')
            ? 'account_type_id'
            : 'type_id';

        $allTypeIds = DB::table('cat_account_types')->where('active', true)->pluck('id');
        $now = now();

        foreach (DB::table('cat_menus')->pluck('id') as $menuId) {
            $allowed = DB::table('cat_menu_account_type_assignments')
                ->where('menu_id', $menuId)
                ->pluck($typeIdColumn);

            // Empty assignments under the old whitelist meant "hidden for all";
            // under exclusions, no rows means "visible for all" — do not insert exclusions.
            if ($allowed->isEmpty()) {
                continue;
            }

            $toExclude = $allTypeIds->diff($allowed);

            foreach ($toExclude as $typeId) {
                DB::table('cat_menu_account_type_exclusions')->insertOrIgnore([
                    'menu_id' => $menuId,
                    'account_type_id' => $typeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Schema::dropIfExists('cat_menu_account_type_assignments');
    }

    public function down(): void
    {
        if (Schema::hasTable('cat_menu_account_type_assignments')) {
            return;
        }

        Schema::create('cat_menu_account_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('cat_menus')->cascadeOnDelete();
            $table->foreignId('type_id')->constrained('cat_account_types')->cascadeOnDelete();
            $table->unique(['menu_id', 'type_id']);
            lmpStamps($table);
        });

        $allTypeIds = DB::table('cat_account_types')->where('active', true)->pluck('id');
        $now = now();

        foreach (DB::table('cat_menus')->pluck('id') as $menuId) {
            $excluded = DB::table('cat_menu_account_type_exclusions')
                ->where('menu_id', $menuId)
                ->pluck('account_type_id');

            $allowed = $excluded->isEmpty()
                ? collect()
                : $allTypeIds->diff($excluded);

            foreach ($allowed as $typeId) {
                DB::table('cat_menu_account_type_assignments')->insertOrIgnore([
                    'menu_id' => $menuId,
                    'type_id' => $typeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Schema::dropIfExists('cat_menu_account_type_exclusions');
    }
};
