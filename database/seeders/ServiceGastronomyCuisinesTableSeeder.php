<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyCuisinesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_cuisines')->delete();
        
        \DB::table('cat_service_gastronomy_cuisines')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'argentinian',
                'sort_order' => 0,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'patagonian',
                'sort_order' => 1,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'latin_american',
                'sort_order' => 2,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'mediterranean',
                'sort_order' => 3,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'italian',
                'sort_order' => 4,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'spanish',
                'sort_order' => 5,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'french',
                'sort_order' => 6,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'american',
                'sort_order' => 7,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'mexican',
                'sort_order' => 8,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'peruvian',
                'sort_order' => 9,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'brazilian',
                'sort_order' => 10,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'asian',
                'sort_order' => 11,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'japanese',
                'sort_order' => 12,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'chinese',
                'sort_order' => 13,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'thai',
                'sort_order' => 14,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'indian',
                'sort_order' => 15,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'middle_eastern',
                'sort_order' => 16,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'international',
                'sort_order' => 17,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'fusion',
                'sort_order' => 18,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'grill',
                'sort_order' => 19,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'bbq',
                'sort_order' => 20,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'steakhouse',
                'sort_order' => 21,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'seafood',
                'sort_order' => 22,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'pizza',
                'sort_order' => 23,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'burgers',
                'sort_order' => 24,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'sushi',
                'sort_order' => 25,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'tapas',
                'sort_order' => 26,
                'active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'sandwiches',
                'sort_order' => 27,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'fast_food',
                'sort_order' => 28,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'street_food',
                'sort_order' => 29,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'vegetarian',
                'sort_order' => 30,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'vegan',
                'sort_order' => 31,
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'gluten_free',
                'sort_order' => 32,
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'healthy',
                'sort_order' => 33,
                'active' => 1,
            ),
        ));
        
        
    }
}