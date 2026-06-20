<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleAccountTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('module_account_types')->delete();
        
        \DB::table('module_account_types')->insert(array (
            0 => 
            array (
                'module_id' => 1,
                'account_type_id' => 1,
            ),
            1 => 
            array (
                'module_id' => 2,
                'account_type_id' => 1,
            ),
            2 => 
            array (
                'module_id' => 3,
                'account_type_id' => 1,
            ),
            3 => 
            array (
                'module_id' => 5,
                'account_type_id' => 1,
            ),
            4 => 
            array (
                'module_id' => 8,
                'account_type_id' => 1,
            ),
            5 => 
            array (
                'module_id' => 9,
                'account_type_id' => 1,
            ),
            6 => 
            array (
                'module_id' => 10,
                'account_type_id' => 1,
            ),
            7 => 
            array (
                'module_id' => 11,
                'account_type_id' => 1,
            ),
            8 => 
            array (
                'module_id' => 12,
                'account_type_id' => 1,
            ),
            9 => 
            array (
                'module_id' => 2,
                'account_type_id' => 2,
            ),
            10 => 
            array (
                'module_id' => 8,
                'account_type_id' => 2,
            ),
            11 => 
            array (
                'module_id' => 9,
                'account_type_id' => 2,
            ),
            12 => 
            array (
                'module_id' => 10,
                'account_type_id' => 2,
            ),
            13 => 
            array (
                'module_id' => 11,
                'account_type_id' => 2,
            ),
            14 => 
            array (
                'module_id' => 12,
                'account_type_id' => 2,
            ),
            15 => 
            array (
                'module_id' => 2,
                'account_type_id' => 3,
            ),
            16 => 
            array (
                'module_id' => 3,
                'account_type_id' => 3,
            ),
            17 => 
            array (
                'module_id' => 4,
                'account_type_id' => 3,
            ),
            18 => 
            array (
                'module_id' => 6,
                'account_type_id' => 3,
            ),
            19 => 
            array (
                'module_id' => 7,
                'account_type_id' => 3,
            ),
            20 => 
            array (
                'module_id' => 8,
                'account_type_id' => 3,
            ),
            21 => 
            array (
                'module_id' => 9,
                'account_type_id' => 3,
            ),
            22 => 
            array (
                'module_id' => 10,
                'account_type_id' => 3,
            ),
            23 => 
            array (
                'module_id' => 11,
                'account_type_id' => 3,
            ),
            24 => 
            array (
                'module_id' => 12,
                'account_type_id' => 3,
            ),
        ));
        
        
    }
}