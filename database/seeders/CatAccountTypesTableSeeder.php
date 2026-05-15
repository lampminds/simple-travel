<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatAccountTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_account_types')->delete();
        
        \DB::table('cat_account_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'operator',
                'active' => 1,
                'sort_order' => 9999,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'provider',
                'active' => 1,
                'sort_order' => 9999,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'agency',
                'active' => 1,
                'sort_order' => 9999,
            ),
        ));
        
        
    }
}