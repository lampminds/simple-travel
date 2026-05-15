<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatMenuAccountTypeExclusionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_menu_account_type_exclusions')->delete();
        
        \DB::table('cat_menu_account_type_exclusions')->insert(array (
            0 => 
            array (
                'id' => 5,
                'menu_id' => 12,
                'account_type_id' => 2,
            ),
            1 => 
            array (
                'id' => 1,
                'menu_id' => 22,
                'account_type_id' => 1,
            ),
            2 => 
            array (
                'id' => 2,
                'menu_id' => 22,
                'account_type_id' => 3,
            ),
            3 => 
            array (
                'id' => 3,
                'menu_id' => 23,
                'account_type_id' => 2,
            ),
            4 => 
            array (
                'id' => 4,
                'menu_id' => 23,
                'account_type_id' => 3,
            ),
        ));
        
        
    }
}