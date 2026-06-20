<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('modules')->delete();
        
        \DB::table('modules')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'operator_core',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'website',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'booking_engine',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'crm',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'operator_finance',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'automation',
                'sort_order' => 9999,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'b2b',
                'sort_order' => 9999,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'invoicing',
                'sort_order' => 9999,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'globalization',
                'sort_order' => 9999,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'reports',
                'sort_order' => 9999,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'integrations',
                'sort_order' => 9999,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'api',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}