<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialPlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commercial_plans')->delete();
        
        \DB::table('commercial_plans')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'starter',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'professional',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'business',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'enterprise',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'agency_lite',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'agency_pro',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}