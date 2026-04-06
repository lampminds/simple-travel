<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_types')->delete();
        
        \DB::table('cat_service_gastronomy_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'reservation',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'fixed_menu',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'tasting_menu',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'gastronomic_experience',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'cooking_class',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'tasting',
                'sort_order' => 9999,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'private_experience',
                'sort_order' => 9999,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'food_event',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}