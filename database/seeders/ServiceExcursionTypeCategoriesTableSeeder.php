<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceExcursionTypeCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_excursion_type_categories')->delete();
        
        \DB::table('cat_service_excursion_type_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'general',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'culture',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'nature',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'adventure',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'water',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'gastronomy',
                'sort_order' => 9999,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'winter',
                'sort_order' => 9999,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'air',
                'sort_order' => 9999,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'transport',
                'sort_order' => 9999,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'rural',
                'sort_order' => 9999,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'special',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}