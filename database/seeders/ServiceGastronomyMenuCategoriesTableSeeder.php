<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyMenuCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_menu_categories')->delete();
        
        \DB::table('cat_service_gastronomy_menu_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'appetizers',
                'sort_order' => 0,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'soups',
                'sort_order' => 1,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'salads',
                'sort_order' => 2,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'pasta',
                'sort_order' => 3,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'rice',
                'sort_order' => 4,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'meat',
                'sort_order' => 5,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'seafood',
                'sort_order' => 6,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'vegetarian',
                'sort_order' => 7,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'vegan',
                'sort_order' => 8,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'sides',
                'sort_order' => 9,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'snacks',
                'sort_order' => 10,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'sandwiches',
                'sort_order' => 11,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'pizza',
                'sort_order' => 12,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'grill',
                'sort_order' => 13,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'bakery',
                'sort_order' => 14,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'pastries',
                'sort_order' => 15,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'breakfast',
                'sort_order' => 16,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'brunch',
                'sort_order' => 17,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'kids',
                'sort_order' => 18,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'tasting_courses',
                'sort_order' => 19,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'drinks_alcoholic',
                'sort_order' => 20,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'drinks_non_alcoholic',
                'sort_order' => 21,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'wines',
                'sort_order' => 22,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'beers',
                'sort_order' => 23,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'cocktails',
                'sort_order' => 24,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'hot_drinks',
                'sort_order' => 25,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'cold_drinks',
                'sort_order' => 26,
                'active' => 1,
            ),
        ));
        
        
    }
}