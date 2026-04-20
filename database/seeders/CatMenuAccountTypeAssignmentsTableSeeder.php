<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatMenuAccountTypeAssignmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_menu_account_type_assignments')->delete();
        
        \DB::table('cat_menu_account_type_assignments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'menu_id' => 2,
                'type_id' => 4,
            ),
            1 => 
            array (
                'id' => 2,
                'menu_id' => 2,
                'type_id' => 5,
            ),
            2 => 
            array (
                'id' => 4,
                'menu_id' => 1,
                'type_id' => 3,
            ),
            3 => 
            array (
                'id' => 5,
                'menu_id' => 1,
                'type_id' => 6,
            ),
            4 => 
            array (
                'id' => 6,
                'menu_id' => 1,
                'type_id' => 4,
            ),
            5 => 
            array (
                'id' => 7,
                'menu_id' => 1,
                'type_id' => 5,
            ),
            6 => 
            array (
                'id' => 8,
                'menu_id' => 3,
                'type_id' => 3,
            ),
            7 => 
            array (
                'id' => 9,
                'menu_id' => 3,
                'type_id' => 6,
            ),
            8 => 
            array (
                'id' => 10,
                'menu_id' => 3,
                'type_id' => 4,
            ),
            9 => 
            array (
                'id' => 11,
                'menu_id' => 3,
                'type_id' => 5,
            ),
            10 => 
            array (
                'id' => 12,
                'menu_id' => 4,
                'type_id' => 3,
            ),
            11 => 
            array (
                'id' => 13,
                'menu_id' => 4,
                'type_id' => 6,
            ),
            12 => 
            array (
                'id' => 14,
                'menu_id' => 4,
                'type_id' => 4,
            ),
            13 => 
            array (
                'id' => 15,
                'menu_id' => 4,
                'type_id' => 5,
            ),
            14 => 
            array (
                'id' => 16,
                'menu_id' => 5,
                'type_id' => 3,
            ),
            15 => 
            array (
                'id' => 17,
                'menu_id' => 5,
                'type_id' => 6,
            ),
            16 => 
            array (
                'id' => 18,
                'menu_id' => 5,
                'type_id' => 4,
            ),
            17 => 
            array (
                'id' => 19,
                'menu_id' => 5,
                'type_id' => 5,
            ),
            18 => 
            array (
                'id' => 20,
                'menu_id' => 6,
                'type_id' => 3,
            ),
            19 => 
            array (
                'id' => 21,
                'menu_id' => 6,
                'type_id' => 6,
            ),
            20 => 
            array (
                'id' => 22,
                'menu_id' => 6,
                'type_id' => 4,
            ),
            21 => 
            array (
                'id' => 23,
                'menu_id' => 6,
                'type_id' => 5,
            ),
            22 => 
            array (
                'id' => 24,
                'menu_id' => 7,
                'type_id' => 3,
            ),
            23 => 
            array (
                'id' => 26,
                'menu_id' => 9,
                'type_id' => 6,
            ),
            24 => 
            array (
                'id' => 27,
                'menu_id' => 10,
                'type_id' => 6,
            ),
            25 => 
            array (
                'id' => 31,
                'menu_id' => 11,
                'type_id' => 4,
            ),
            26 => 
            array (
                'id' => 32,
                'menu_id' => 11,
                'type_id' => 5,
            ),
            27 => 
            array (
                'id' => 33,
                'menu_id' => 12,
                'type_id' => 4,
            ),
            28 => 
            array (
                'id' => 34,
                'menu_id' => 12,
                'type_id' => 5,
            ),
            29 => 
            array (
                'id' => 35,
                'menu_id' => 13,
                'type_id' => 4,
            ),
            30 => 
            array (
                'id' => 36,
                'menu_id' => 13,
                'type_id' => 5,
            ),
            31 => 
            array (
                'id' => 37,
                'menu_id' => 14,
                'type_id' => 3,
            ),
            32 => 
            array (
                'id' => 38,
                'menu_id' => 14,
                'type_id' => 6,
            ),
            33 => 
            array (
                'id' => 39,
                'menu_id' => 15,
                'type_id' => 3,
            ),
            34 => 
            array (
                'id' => 40,
                'menu_id' => 15,
                'type_id' => 6,
            ),
            35 => 
            array (
                'id' => 41,
                'menu_id' => 16,
                'type_id' => 3,
            ),
            36 => 
            array (
                'id' => 42,
                'menu_id' => 16,
                'type_id' => 6,
            ),
            37 => 
            array (
                'id' => 46,
                'menu_id' => 2,
                'type_id' => 3,
            ),
            38 => 
            array (
                'id' => 47,
                'menu_id' => 11,
                'type_id' => 3,
            ),
            39 => 
            array (
                'id' => 48,
                'menu_id' => 11,
                'type_id' => 6,
            ),
            40 => 
            array (
                'id' => 49,
                'menu_id' => 13,
                'type_id' => 3,
            ),
            41 => 
            array (
                'id' => 50,
                'menu_id' => 13,
                'type_id' => 6,
            ),
        ));
        
        
    }
}