<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialPlanModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commercial_plan_modules')->delete();
        
        \DB::table('commercial_plan_modules')->insert(array (
            0 => 
            array (
                'id' => 1,
                'commercial_plan_id' => 1,
                'module_id' => 1,
                'sort_order' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'commercial_plan_id' => 1,
                'module_id' => 5,
                'sort_order' => 2,
            ),
        ));
        
        
    }
}