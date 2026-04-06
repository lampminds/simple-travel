<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyMenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_menus')->delete();
        
        \DB::table('cat_service_gastronomy_menus')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'a_la_carte',
                'is_default' => 0,
                'sort_order' => 0,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'fixed_menu',
                'is_default' => 0,
                'sort_order' => 1,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'tasting_menu',
                'is_default' => 0,
                'sort_order' => 2,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'set_menu',
                'is_default' => 0,
                'sort_order' => 3,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'group_menu',
                'is_default' => 0,
                'sort_order' => 4,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'event_menu',
                'is_default' => 0,
                'sort_order' => 5,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'kids_menu',
                'is_default' => 0,
                'sort_order' => 6,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'beverage_menu',
                'is_default' => 0,
                'sort_order' => 7,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'wine_list',
                'is_default' => 0,
                'sort_order' => 8,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'brunch_menu',
                'is_default' => 0,
                'sort_order' => 9,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'breakfast_menu',
                'is_default' => 0,
                'sort_order' => 10,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'lunch_menu',
                'is_default' => 0,
                'sort_order' => 11,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'dinner_menu',
                'is_default' => 0,
                'sort_order' => 12,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'seasonal_menu',
                'is_default' => 0,
                'sort_order' => 13,
                'active' => 1,
            ),
        ));
        
        
    }
}