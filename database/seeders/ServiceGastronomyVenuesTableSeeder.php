<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyVenuesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_venues')->delete();
        
        \DB::table('cat_service_gastronomy_venues')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'restaurant',
                'sort_order' => 0,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'bar',
                'sort_order' => 1,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'cafe',
                'sort_order' => 2,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'bistro',
                'sort_order' => 3,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'fine_dining',
                'sort_order' => 4,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'casual_dining',
                'sort_order' => 5,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'fast_food',
                'sort_order' => 6,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'buffet',
                'sort_order' => 7,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'food_truck',
                'sort_order' => 8,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'street_food_stall',
                'sort_order' => 9,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'food_market',
                'sort_order' => 10,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'food_hall',
                'sort_order' => 11,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'winery',
                'sort_order' => 12,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'brewery',
                'sort_order' => 13,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'distillery',
                'sort_order' => 14,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'bakery',
                'sort_order' => 15,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'pastry_shop',
                'sort_order' => 16,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'deli',
                'sort_order' => 17,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'ice_cream_shop',
                'sort_order' => 18,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'chocolate_shop',
                'sort_order' => 19,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'tea_house',
                'sort_order' => 20,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'juice_bar',
                'sort_order' => 21,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'rooftop',
                'sort_order' => 22,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'beach_club',
                'sort_order' => 23,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'mountain_lodge',
                'sort_order' => 24,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'farm',
                'sort_order' => 25,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'vineyard',
                'sort_order' => 26,
                'active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'private_home',
                'sort_order' => 27,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'event_venue',
                'sort_order' => 28,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'pop_up_location',
                'sort_order' => 29,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'hotel_restaurant',
                'sort_order' => 30,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'resort_restaurant',
                'sort_order' => 31,
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'casino_restaurant',
                'sort_order' => 32,
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'mall_restaurant',
                'sort_order' => 33,
                'active' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'code' => 'airport_restaurant',
                'sort_order' => 34,
                'active' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'code' => 'train_station_restaurant',
                'sort_order' => 35,
                'active' => 1,
            ),
            36 => 
            array (
                'id' => 37,
                'code' => 'cruise_ship',
                'sort_order' => 36,
                'active' => 1,
            ),
            37 => 
            array (
                'id' => 38,
                'code' => 'outdoor_camp',
                'sort_order' => 37,
                'active' => 1,
            ),
        ));
        
        
    }
}