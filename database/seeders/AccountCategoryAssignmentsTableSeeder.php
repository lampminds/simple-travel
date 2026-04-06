<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Populates account_category_assignments (account ↔ cat_account_categories).
 */
class AccountCategoryAssignmentsTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('account_category_assignments')->delete();

        DB::table('account_category_assignments')->insert([
            [
                'id' => 1,
                'account_id' => 3,
                'account_category_id' => 3,
            ],
            [
                'id' => 2,
                'account_id' => 2,
                'account_category_id' => 5,
            ],
        ]);
    }
}
