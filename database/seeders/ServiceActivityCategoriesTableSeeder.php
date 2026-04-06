<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceActivityCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_activity_categories')->delete();
        
        \DB::table('cat_service_activity_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'nature',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'water',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'mountain',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'snow',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'horses',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'culture',
                'sort_order' => 9999,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'gastronomics',
                'sort_order' => 9999,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'welfare',
                'sort_order' => 9999,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'amusement',
                'sort_order' => 9999,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'family',
                'sort_order' => 9999,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'farm',
                'sort_order' => 9999,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'recreative_transport',
                'sort_order' => 9999,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'adventure',
                'sort_order' => 9999,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'educational',
                'sort_order' => 9999,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'watching',
                'sort_order' => 9999,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'relax',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}