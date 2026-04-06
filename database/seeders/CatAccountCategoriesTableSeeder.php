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
            2 => 
            array (
                'id' => 3,
                'group' => 'type',
                'code' => 'provider',
                'active' => 1,
                'sort_order' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'group' => 'type',
                'code' => 'tour_operator',
                'active' => 1,
                'sort_order' => 3,
            ),
            4 => 
            array (
                'id' => 5,
                'group' => 'type',
                'code' => 'wholesaler',
                'active' => 1,
                'sort_order' => 4,
            ),
            5 => 
            array (
                'id' => 6,
                'group' => 'type',
                'code' => 'agency',
                'active' => 1,
                'sort_order' => 2,
            ),
            6 => 
            array (
                'id' => 7,
                'group' => 'tax_id',
                'code' => 'OTA',
                'active' => 1,
                'sort_order' => 9999,
            ),
        ));
        
        
    }
}
