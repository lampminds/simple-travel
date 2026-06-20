<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialPlanAccountTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commercial_plan_account_types')->delete();
        
        \DB::table('commercial_plan_account_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'commercial_plan_id' => 1,
                'account_type_id' => 1,
            ),
            1 => 
            array (
                'id' => 4,
                'commercial_plan_id' => 2,
                'account_type_id' => 1,
            ),
            2 => 
            array (
                'id' => 7,
                'commercial_plan_id' => 3,
                'account_type_id' => 1,
            ),
            3 => 
            array (
                'id' => 10,
                'commercial_plan_id' => 4,
                'account_type_id' => 1,
            ),
            4 => 
            array (
                'id' => 13,
                'commercial_plan_id' => 5,
                'account_type_id' => 3,
            ),
            5 => 
            array (
                'id' => 14,
                'commercial_plan_id' => 6,
                'account_type_id' => 3,
            ),
        ));
        
        
    }
}