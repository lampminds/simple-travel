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
                'id' => 18,
                'menu_id' => 7,
                'account_type_id' => 2,
            ),
            3 => 
            array (
                'id' => 19,
                'menu_id' => 7,
                'account_type_id' => 3,
            ),
            4 => 
            array (
                'id' => 31,
                'menu_id' => 9,
                'account_type_id' => 1,
            ),
            5 => 
            array (
                'id' => 28,
                'menu_id' => 9,
                'account_type_id' => 2,
            ),
            6 => 
            array (
                'id' => 12,
                'menu_id' => 10,
                'account_type_id' => 1,
            ),
            7 => 
            array (
                'id' => 13,
                'menu_id' => 10,
                'account_type_id' => 2,
            ),
            8 => 
            array (
                'id' => 5,
                'menu_id' => 12,
                'account_type_id' => 2,
            ),
            9 => 
            array (
                'id' => 17,
                'menu_id' => 17,
                'account_type_id' => 3,
            ),
            10 => 
            array (
                'id' => 9,
                'menu_id' => 19,
                'account_type_id' => 1,
            ),
            11 => 
            array (
                'id' => 8,
                'menu_id' => 19,
                'account_type_id' => 3,
            ),
            12 => 
            array (
                'id' => 14,
                'menu_id' => 20,
                'account_type_id' => 1,
            ),
            13 => 
            array (
                'id' => 7,
                'menu_id' => 20,
                'account_type_id' => 3,
            ),
            14 => 
            array (
                'id' => 1,
                'menu_id' => 22,
                'account_type_id' => 1,
            ),
            15 => 
            array (
                'id' => 2,
                'menu_id' => 22,
                'account_type_id' => 3,
            ),
            16 => 
            array (
                'id' => 3,
                'menu_id' => 23,
                'account_type_id' => 2,
            ),
            17 => 
            array (
                'id' => 4,
                'menu_id' => 23,
                'account_type_id' => 3,
            ),
            18 => 
            array (
                'id' => 11,
                'menu_id' => 24,
                'account_type_id' => 2,
            ),
            19 => 
            array (
                'id' => 22,
                'menu_id' => 24,
                'account_type_id' => 3,
            ),
            20 => 
            array (
                'id' => 20,
                'menu_id' => 26,
                'account_type_id' => 1,
            ),
            21 => 
            array (
                'id' => 21,
                'menu_id' => 26,
                'account_type_id' => 3,
            ),
            22 => 
            array (
                'id' => 23,
                'menu_id' => 27,
                'account_type_id' => 2,
            ),
            23 => 
            array (
                'id' => 24,
                'menu_id' => 27,
                'account_type_id' => 3,
            ),
            24 => 
            array (
                'id' => 25,
                'menu_id' => 28,
                'account_type_id' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'menu_id' => 28,
                'account_type_id' => 2,
            ),
            26 => 
            array (
                'id' => 33,
                'menu_id' => 29,
                'account_type_id' => 2,
            ),
            27 => 
            array (
                'id' => 32,
                'menu_id' => 29,
                'account_type_id' => 3,
            ),
            28 => 
            array (
                'id' => 34,
                'menu_id' => 30,
                'account_type_id' => 1,
            ),
            29 => 
            array (
                'id' => 35,
                'menu_id' => 30,
                'account_type_id' => 3,
            ),
            30 => 
            array (
                'id' => 36,
                'menu_id' => 31,
                'account_type_id' => 1,
            ),
            31 => 
            array (
                'id' => 37,
                'menu_id' => 31,
                'account_type_id' => 3,
            ),
            32 => 
            array (
                'id' => 38,
                'menu_id' => 32,
                'account_type_id' => 2,
            ),
            33 => 
            array (
                'id' => 39,
                'menu_id' => 32,
                'account_type_id' => 3,
            ),
            34 => 
            array (
                'id' => 40,
                'menu_id' => 33,
                'account_type_id' => 2,
            ),
            35 => 
            array (
                'id' => 41,
                'menu_id' => 33,
                'account_type_id' => 3,
            ),
            36 => 
            array (
                'id' => 42,
                'menu_id' => 34,
                'account_type_id' => 2,
            ),
            37 => 
            array (
                'id' => 43,
                'menu_id' => 34,
                'account_type_id' => 3,
            ),
        ));
        
        
    }
}