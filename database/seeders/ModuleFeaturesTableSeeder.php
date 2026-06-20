<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleFeaturesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('module_features')->delete();
        
        \DB::table('module_features')->insert(array (
            0 => 
            array (
                'id' => 2,
                'module_id' => 5,
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 3,
                'module_id' => 9,
                'sort_order' => 1,
                'active' => 1,
            ),
        ));
        
        
    }
}