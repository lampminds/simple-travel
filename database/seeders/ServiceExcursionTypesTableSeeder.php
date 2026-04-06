<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceExcursionTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_excursion_types')->delete();
        
        \DB::table('cat_service_excursion_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'city_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'walking_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 2,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'guided_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 3,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'self_guided_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 4,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'panoramic_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 5,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'day_trip',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 6,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'half_day_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 7,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'multi_day_tour',
                'service_excursion_type_category_id' => 1,
                'sort_order' => 8,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'cultural_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 1,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'historical_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 2,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'heritage_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 3,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'museum_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 4,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'architecture_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 5,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'religious_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 6,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'archaeological_tour',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 7,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'local_experience',
                'service_excursion_type_category_id' => 2,
                'sort_order' => 8,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'nature_tour',
                'service_excursion_type_category_id' => 3,
                'sort_order' => 1,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'wildlife_tour',
                'service_excursion_type_category_id' => 3,
                'sort_order' => 2,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'birdwatching_tour',
                'service_excursion_type_category_id' => 3,
                'sort_order' => 3,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'national_park_tour',
                'service_excursion_type_category_id' => 3,
                'sort_order' => 4,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'scenic_tour',
                'service_excursion_type_category_id' => 3,
                'sort_order' => 5,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'landscape_tour',
                'service_excursion_type_category_id' => 3,
                'sort_order' => 6,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'adventure_tour',
                'service_excursion_type_category_id' => 4,
                'sort_order' => 1,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'hiking_tour',
                'service_excursion_type_category_id' => 4,
                'sort_order' => 2,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'mountain_tour',
                'service_excursion_type_category_id' => 4,
                'sort_order' => 3,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'offroad_tour',
                'service_excursion_type_category_id' => 4,
                'sort_order' => 4,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'extreme_adventure',
                'service_excursion_type_category_id' => 4,
                'sort_order' => 5,
                'active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'boat_tour',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 1,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'lake_tour',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 2,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'river_tour',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 3,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'cruise',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 4,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'catamaran_tour',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 5,
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'kayak_tour',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 6,
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'rafting_trip',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 7,
                'active' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'code' => 'fishing_trip',
                'service_excursion_type_category_id' => 5,
                'sort_order' => 8,
                'active' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'code' => 'food_tour',
                'service_excursion_type_category_id' => 6,
                'sort_order' => 1,
                'active' => 1,
            ),
            36 => 
            array (
                'id' => 37,
                'code' => 'wine_tour',
                'service_excursion_type_category_id' => 6,
                'sort_order' => 2,
                'active' => 1,
            ),
            37 => 
            array (
                'id' => 38,
                'code' => 'brewery_tour',
                'service_excursion_type_category_id' => 6,
                'sort_order' => 3,
                'active' => 1,
            ),
            38 => 
            array (
                'id' => 39,
                'code' => 'distillery_tour',
                'service_excursion_type_category_id' => 6,
                'sort_order' => 4,
                'active' => 1,
            ),
            39 => 
            array (
                'id' => 40,
                'code' => 'culinary_tour',
                'service_excursion_type_category_id' => 6,
                'sort_order' => 5,
                'active' => 1,
            ),
            40 => 
            array (
                'id' => 41,
                'code' => 'farm_visit',
                'service_excursion_type_category_id' => 6,
                'sort_order' => 6,
                'active' => 1,
            ),
            41 => 
            array (
                'id' => 42,
                'code' => 'ranch_experience',
                'service_excursion_type_category_id' => 10,
                'sort_order' => 1,
                'active' => 1,
            ),
            42 => 
            array (
                'id' => 43,
                'code' => 'agrotourism',
                'service_excursion_type_category_id' => 10,
                'sort_order' => 2,
                'active' => 1,
            ),
            43 => 
            array (
                'id' => 44,
                'code' => 'farm_experience',
                'service_excursion_type_category_id' => 10,
                'sort_order' => 3,
                'active' => 1,
            ),
            44 => 
            array (
                'id' => 45,
                'code' => 'ski_tour',
                'service_excursion_type_category_id' => 7,
                'sort_order' => 1,
                'active' => 1,
            ),
            45 => 
            array (
                'id' => 46,
                'code' => 'winter_tour',
                'service_excursion_type_category_id' => 7,
                'sort_order' => 2,
                'active' => 1,
            ),
            46 => 
            array (
                'id' => 47,
                'code' => 'snowmobile_tour',
                'service_excursion_type_category_id' => 7,
                'sort_order' => 3,
                'active' => 1,
            ),
            47 => 
            array (
                'id' => 48,
                'code' => 'dog_sled_tour',
                'service_excursion_type_category_id' => 7,
                'sort_order' => 4,
                'active' => 1,
            ),
            48 => 
            array (
                'id' => 49,
                'code' => 'helicopter_tour',
                'service_excursion_type_category_id' => 8,
                'sort_order' => 1,
                'active' => 1,
            ),
            49 => 
            array (
                'id' => 50,
                'code' => 'scenic_flight',
                'service_excursion_type_category_id' => 8,
                'sort_order' => 2,
                'active' => 1,
            ),
            50 => 
            array (
                'id' => 51,
                'code' => 'hot_air_balloon_ride',
                'service_excursion_type_category_id' => 8,
                'sort_order' => 3,
                'active' => 1,
            ),
            51 => 
            array (
                'id' => 52,
                'code' => 'train_tour',
                'service_excursion_type_category_id' => 9,
                'sort_order' => 1,
                'active' => 1,
            ),
            52 => 
            array (
                'id' => 53,
                'code' => 'bike_tour',
                'service_excursion_type_category_id' => 9,
                'sort_order' => 2,
                'active' => 1,
            ),
            53 => 
            array (
                'id' => 54,
                'code' => 'horseback_tour',
                'service_excursion_type_category_id' => 9,
                'sort_order' => 3,
                'active' => 1,
            ),
            54 => 
            array (
                'id' => 55,
                'code' => 'atv_tour',
                'service_excursion_type_category_id' => 9,
                'sort_order' => 4,
                'active' => 1,
            ),
            55 => 
            array (
                'id' => 56,
                'code' => 'photography_tour',
                'service_excursion_type_category_id' => 11,
                'sort_order' => 1,
                'active' => 1,
            ),
            56 => 
            array (
                'id' => 57,
                'code' => 'sunset_tour',
                'service_excursion_type_category_id' => 11,
                'sort_order' => 2,
                'active' => 1,
            ),
            57 => 
            array (
                'id' => 58,
                'code' => 'sunrise_tour',
                'service_excursion_type_category_id' => 11,
                'sort_order' => 3,
                'active' => 1,
            ),
            58 => 
            array (
                'id' => 59,
                'code' => 'night_tour',
                'service_excursion_type_category_id' => 11,
                'sort_order' => 4,
                'active' => 1,
            ),
            59 => 
            array (
                'id' => 60,
                'code' => 'stargazing_tour',
                'service_excursion_type_category_id' => 11,
                'sort_order' => 5,
                'active' => 1,
            ),
            60 => 
            array (
                'id' => 61,
                'code' => 'festival_tour',
                'service_excursion_type_category_id' => 11,
                'sort_order' => 6,
                'active' => 1,
            ),
        ));
        
        
    }
}