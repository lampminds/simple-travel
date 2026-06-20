<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialModulePricesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commercial_module_prices')->delete();
        
        \DB::table('commercial_module_prices')->insert(array (
            0 => 
            array (
                'id' => 1,
                'module_id' => 1,
                'billing_type' => 'per_user',
                'base_price' => '250.00',
                'included_users' => 1,
                'price_per_user' => '25.00',
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'module_id' => 2,
                'billing_type' => 'fixed',
                'base_price' => '100.00',
                'included_users' => NULL,
                'price_per_user' => NULL,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'module_id' => 3,
                'billing_type' => 'fixed',
                'base_price' => '90.00',
                'included_users' => NULL,
                'price_per_user' => NULL,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'module_id' => 4,
                'billing_type' => 'fixed',
                'base_price' => '45.00',
                'included_users' => NULL,
                'price_per_user' => NULL,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'module_id' => 5,
                'billing_type' => 'per_user',
                'base_price' => '35.00',
                'included_users' => 1,
                'price_per_user' => '25.00',
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'module_id' => 8,
                'billing_type' => 'fixed',
                'base_price' => '70.00',
                'included_users' => NULL,
                'price_per_user' => NULL,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'module_id' => 9,
                'billing_type' => 'fixed',
                'base_price' => '45.00',
                'included_users' => NULL,
                'price_per_user' => NULL,
                'active' => 1,
            ),
        ));
        
        
    }
}