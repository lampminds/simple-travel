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
                'id' => 15,
                'menu_id' => 3,
                'account_type_id' => 1,
            ),
            1 => 
            array (
                'id' => 16,
                'menu_id' => 3,
                'account_type_id' => 3,
            ),
            2 => 
            array (
                'id' => 12,
                'menu_id' => 10,
                'account_type_id' => 1,
            ),
            3 => 
            array (
                'id' => 13,
                'menu_id' => 10,
                'account_type_id' => 2,
            ),
            4 => 
            array (
                'id' => 5,
                'menu_id' => 12,
                'account_type_id' => 2,
            ),
            5 => 
            array (
                'id' => 9,
                'menu_id' => 19,
                'account_type_id' => 1,
            ),
            6 => 
            array (
                'id' => 8,
                'menu_id' => 19,
                'account_type_id' => 3,
            ),
            7 => 
            array (
                'id' => 14,
                'menu_id' => 20,
                'account_type_id' => 1,
            ),
            8 => 
            array (
                'id' => 7,
                'menu_id' => 20,
                'account_type_id' => 3,
            ),
            9 => 
            array (
                'id' => 1,
                'menu_id' => 22,
                'account_type_id' => 1,
            ),
            10 => 
            array (
                'id' => 2,
                'menu_id' => 22,
                'account_type_id' => 3,
            ),
            11 => 
            array (
                'id' => 3,
                'menu_id' => 23,
                'account_type_id' => 2,
            ),
            12 => 
            array (
                'id' => 4,
                'menu_id' => 23,
                'account_type_id' => 3,
            ),
            13 => 
            array (
                'id' => 11,
                'menu_id' => 24,
                'account_type_id' => 2,
            ),
        ));
        
        
    }
}