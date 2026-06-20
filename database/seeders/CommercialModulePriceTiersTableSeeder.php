<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialModulePriceTiersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commercial_module_price_tiers')->delete();
        
        \DB::table('commercial_module_price_tiers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'module_price_id' => 1,
                'from_users' => 2,
                'to_users' => 4,
                'price_per_user' => '25.00',
            ),
            1 => 
            array (
                'id' => 2,
                'module_price_id' => 1,
                'from_users' => 5,
                'to_users' => 10,
                'price_per_user' => '15.00',
            ),
        ));
        
        
    }
}