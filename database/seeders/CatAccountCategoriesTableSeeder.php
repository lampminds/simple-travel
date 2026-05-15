<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatAccountCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cat_account_categories')->delete();

        \DB::table('cat_account_categories')->insert(array (
            0 =>
            array (
                'id' => 1,
                'group' => 'tax_id',
                'code' => 'CUIT',
                'active' => 1,
                'sort_order' => 9999,
            ),
            1 =>
            array (
                'id' => 2,
                'group' => 'tax_id',
                'code' => 'DNI',
                'active' => 1,
                'sort_order' => 9999,
            ),
        ));


    }
}
