<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatServiceActivityTypesTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('cat_service_activity_types')->delete();

        \DB::table('cat_service_activity_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'hiking',
                'service_activity_type_category_id' => 3,
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'trekking',
                'service_activity_type_category_id' => 3,
                'sort_order' => 2,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'nature_walk',
                'service_activity_type_category_id' => 3,
                'sort_order' => 3,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'birdwatching',
                'service_activity_type_category_id' => 3,
                'sort_order' => 4,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'geology_exploration',
                'service_activity_type_category_id' => 3,
                'sort_order' => 5,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'photography',
                'service_activity_type_category_id' => 3,
                'sort_order' => 6,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'nature_photography',
                'service_activity_type_category_id' => 3,
                'sort_order' => 7,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'kayaking',
                'service_activity_type_category_id' => 5,
                'sort_order' => 1,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'canoeing',
                'service_activity_type_category_id' => 5,
                'sort_order' => 2,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'rafting',
                'service_activity_type_category_id' => 5,
                'sort_order' => 3,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'stand_up_paddle',
                'service_activity_type_category_id' => 5,
                'sort_order' => 4,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'snorkeling',
                'service_activity_type_category_id' => 5,
                'sort_order' => 5,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'scuba_diving',
                'service_activity_type_category_id' => 5,
                'sort_order' => 6,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'swimming',
                'service_activity_type_category_id' => 5,
                'sort_order' => 7,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'fishing',
                'service_activity_type_category_id' => 5,
                'sort_order' => 8,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'fly_fishing',
                'service_activity_type_category_id' => 5,
                'sort_order' => 9,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'sailing',
                'service_activity_type_category_id' => 5,
                'sort_order' => 10,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'boating',
                'service_activity_type_category_id' => 5,
                'sort_order' => 11,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'motorboat',
                'service_activity_type_category_id' => 5,
                'sort_order' => 12,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'jet_ski',
                'service_activity_type_category_id' => 5,
                'sort_order' => 13,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'mountaineering',
                'service_activity_type_category_id' => 4,
                'sort_order' => 1,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'rock_climbing',
                'service_activity_type_category_id' => 4,
                'sort_order' => 2,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'ice_climbing',
                'service_activity_type_category_id' => 4,
                'sort_order' => 3,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'via_ferrata',
                'service_activity_type_category_id' => 4,
                'sort_order' => 4,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'canyoning',
                'service_activity_type_category_id' => 4,
                'sort_order' => 5,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'zipline',
                'service_activity_type_category_id' => 4,
                'sort_order' => 6,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'paragliding',
                'service_activity_type_category_id' => 4,
                'sort_order' => 7,
                'active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'hang_gliding',
                'service_activity_type_category_id' => 4,
                'sort_order' => 8,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'bungee_jumping',
                'service_activity_type_category_id' => 4,
                'sort_order' => 9,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'mountain_biking',
                'service_activity_type_category_id' => 4,
                'sort_order' => 10,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'downhill_biking',
                'service_activity_type_category_id' => 4,
                'sort_order' => 11,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'trail_running',
                'service_activity_type_category_id' => 4,
                'sort_order' => 12,
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'offroad',
                'service_activity_type_category_id' => 4,
                'sort_order' => 13,
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'atv_riding',
                'service_activity_type_category_id' => 4,
                'sort_order' => 14,
                'active' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'code' => 'buggy_riding',
                'service_activity_type_category_id' => 4,
                'sort_order' => 15,
                'active' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'code' => 'sandboarding',
                'service_activity_type_category_id' => 4,
                'sort_order' => 16,
                'active' => 1,
            ),
            36 => 
            array (
                'id' => 37,
                'code' => 'dune_bashing',
                'service_activity_type_category_id' => 4,
                'sort_order' => 17,
                'active' => 1,
            ),
            37 => 
            array (
                'id' => 38,
                'code' => 'adventure_course',
                'service_activity_type_category_id' => 4,
                'sort_order' => 18,
                'active' => 1,
            ),
            38 => 
            array (
                'id' => 39,
                'code' => 'skiing',
                'service_activity_type_category_id' => 7,
                'sort_order' => 1,
                'active' => 1,
            ),
            39 => 
            array (
                'id' => 40,
                'code' => 'snowboarding',
                'service_activity_type_category_id' => 7,
                'sort_order' => 2,
                'active' => 1,
            ),
            40 => 
            array (
                'id' => 41,
                'code' => 'cross_country_skiing',
                'service_activity_type_category_id' => 7,
                'sort_order' => 3,
                'active' => 1,
            ),
            41 => 
            array (
                'id' => 42,
                'code' => 'snowshoeing',
                'service_activity_type_category_id' => 7,
                'sort_order' => 4,
                'active' => 1,
            ),
            42 => 
            array (
                'id' => 43,
                'code' => 'sledding',
                'service_activity_type_category_id' => 7,
                'sort_order' => 5,
                'active' => 1,
            ),
            43 => 
            array (
                'id' => 44,
                'code' => 'dog_sledding',
                'service_activity_type_category_id' => 7,
                'sort_order' => 6,
                'active' => 1,
            ),
            44 => 
            array (
                'id' => 45,
                'code' => 'snowmobiling',
                'service_activity_type_category_id' => 7,
                'sort_order' => 7,
                'active' => 1,
            ),
            45 => 
            array (
                'id' => 46,
                'code' => 'ice_skating',
                'service_activity_type_category_id' => 7,
                'sort_order' => 8,
                'active' => 1,
            ),
            46 => 
            array (
                'id' => 47,
                'code' => 'winter_hiking',
                'service_activity_type_category_id' => 7,
                'sort_order' => 9,
                'active' => 1,
            ),
            47 => 
            array (
                'id' => 48,
                'code' => 'museum_visit',
                'service_activity_type_category_id' => 2,
                'sort_order' => 1,
                'active' => 1,
            ),
            48 => 
            array (
                'id' => 49,
                'code' => 'religious_sites',
                'service_activity_type_category_id' => 2,
                'sort_order' => 2,
                'active' => 1,
            ),
            49 => 
            array (
                'id' => 50,
                'code' => 'workshop',
                'service_activity_type_category_id' => 2,
                'sort_order' => 3,
                'active' => 1,
            ),
            50 => 
            array (
                'id' => 51,
                'code' => 'craft_workshop',
                'service_activity_type_category_id' => 2,
                'sort_order' => 4,
                'active' => 1,
            ),
            51 => 
            array (
                'id' => 52,
                'code' => 'photography_workshop',
                'service_activity_type_category_id' => 2,
                'sort_order' => 5,
                'active' => 1,
            ),
            52 => 
            array (
                'id' => 53,
                'code' => 'language_class',
                'service_activity_type_category_id' => 2,
                'sort_order' => 6,
                'active' => 1,
            ),
            53 => 
            array (
                'id' => 54,
                'code' => 'art_class',
                'service_activity_type_category_id' => 2,
                'sort_order' => 7,
                'active' => 1,
            ),
            54 => 
            array (
                'id' => 55,
                'code' => 'educational_workshop',
                'service_activity_type_category_id' => 2,
                'sort_order' => 8,
                'active' => 1,
            ),
            55 => 
            array (
                'id' => 56,
                'code' => 'food_tasting',
                'service_activity_type_category_id' => 6,
                'sort_order' => 1,
                'active' => 1,
            ),
            56 => 
            array (
                'id' => 57,
                'code' => 'wine_tasting',
                'service_activity_type_category_id' => 6,
                'sort_order' => 2,
                'active' => 1,
            ),
            57 => 
            array (
                'id' => 58,
                'code' => 'beer_tasting',
                'service_activity_type_category_id' => 6,
                'sort_order' => 3,
                'active' => 1,
            ),
            58 => 
            array (
                'id' => 59,
                'code' => 'spirits_tasting',
                'service_activity_type_category_id' => 6,
                'sort_order' => 4,
                'active' => 1,
            ),
            59 => 
            array (
                'id' => 60,
                'code' => 'cooking_class',
                'service_activity_type_category_id' => 6,
                'sort_order' => 5,
                'active' => 1,
            ),
            60 => 
            array (
                'id' => 61,
                'code' => 'vineyard_visit',
                'service_activity_type_category_id' => 6,
                'sort_order' => 6,
                'active' => 1,
            ),
            61 => 
            array (
                'id' => 62,
                'code' => 'brewery_visit',
                'service_activity_type_category_id' => 6,
                'sort_order' => 7,
                'active' => 1,
            ),
            62 => 
            array (
                'id' => 63,
                'code' => 'distillery_visit',
                'service_activity_type_category_id' => 6,
                'sort_order' => 8,
                'active' => 1,
            ),
            63 => 
            array (
                'id' => 64,
                'code' => 'scenic_flight',
                'service_activity_type_category_id' => 8,
                'sort_order' => 1,
                'active' => 1,
            ),
            64 => 
            array (
                'id' => 65,
                'code' => 'helicopter_tour',
                'service_activity_type_category_id' => 8,
                'sort_order' => 2,
                'active' => 1,
            ),
            65 => 
            array (
                'id' => 66,
                'code' => 'hot_air_balloon',
                'service_activity_type_category_id' => 8,
                'sort_order' => 3,
                'active' => 1,
            ),
            66 => 
            array (
                'id' => 67,
                'code' => 'train_ride',
                'service_activity_type_category_id' => 9,
                'sort_order' => 1,
                'active' => 1,
            ),
            67 => 
            array (
                'id' => 68,
                'code' => 'boat_cruise',
                'service_activity_type_category_id' => 9,
                'sort_order' => 2,
                'active' => 1,
            ),
            68 => 
            array (
                'id' => 69,
                'code' => 'catamaran_cruise',
                'service_activity_type_category_id' => 9,
                'sort_order' => 3,
                'active' => 1,
            ),
            69 => 
            array (
                'id' => 70,
                'code' => 'sunset_cruise',
                'service_activity_type_category_id' => 9,
                'sort_order' => 4,
                'active' => 1,
            ),
            70 => 
            array (
                'id' => 71,
                'code' => 'horseback_riding',
                'service_activity_type_category_id' => 9,
                'sort_order' => 5,
                'active' => 1,
            ),
            71 => 
            array (
                'id' => 72,
                'code' => 'horse_trekking',
                'service_activity_type_category_id' => 9,
                'sort_order' => 6,
                'active' => 1,
            ),
            72 => 
            array (
                'id' => 73,
                'code' => 'carriage_rides',
                'service_activity_type_category_id' => 9,
                'sort_order' => 7,
                'active' => 1,
            ),
            73 => 
            array (
                'id' => 74,
                'code' => 'cycling',
                'service_activity_type_category_id' => 9,
                'sort_order' => 8,
                'active' => 1,
            ),
            74 => 
            array (
                'id' => 75,
                'code' => 'bike_rental',
                'service_activity_type_category_id' => 9,
                'sort_order' => 9,
                'active' => 1,
            ),
            75 => 
            array (
                'id' => 76,
                'code' => 'animal_feeding',
                'service_activity_type_category_id' => 10,
                'sort_order' => 1,
                'active' => 1,
            ),
            76 => 
            array (
                'id' => 77,
                'code' => 'sheep_shearing',
                'service_activity_type_category_id' => 10,
                'sort_order' => 2,
                'active' => 1,
            ),
            77 => 
            array (
                'id' => 78,
                'code' => 'cheese_making',
                'service_activity_type_category_id' => 10,
                'sort_order' => 3,
                'active' => 1,
            ),
            78 => 
            array (
                'id' => 79,
                'code' => 'horse_training',
                'service_activity_type_category_id' => 10,
                'sort_order' => 4,
                'active' => 1,
            ),
            79 => 
            array (
                'id' => 80,
                'code' => 'spa',
                'service_activity_type_category_id' => 1,
                'sort_order' => 1,
                'active' => 1,
            ),
            80 => 
            array (
                'id' => 81,
                'code' => 'massage',
                'service_activity_type_category_id' => 1,
                'sort_order' => 2,
                'active' => 1,
            ),
            81 => 
            array (
                'id' => 82,
                'code' => 'thermal_baths',
                'service_activity_type_category_id' => 1,
                'sort_order' => 3,
                'active' => 1,
            ),
            82 => 
            array (
                'id' => 83,
                'code' => 'yoga',
                'service_activity_type_category_id' => 1,
                'sort_order' => 4,
                'active' => 1,
            ),
            83 => 
            array (
                'id' => 84,
                'code' => 'meditation',
                'service_activity_type_category_id' => 1,
                'sort_order' => 5,
                'active' => 1,
            ),
            84 => 
            array (
                'id' => 85,
                'code' => 'fitness',
                'service_activity_type_category_id' => 1,
                'sort_order' => 6,
                'active' => 1,
            ),
            85 => 
            array (
                'id' => 86,
                'code' => 'pilates',
                'service_activity_type_category_id' => 1,
                'sort_order' => 7,
                'active' => 1,
            ),
            86 => 
            array (
                'id' => 87,
                'code' => 'golf',
                'service_activity_type_category_id' => 1,
                'sort_order' => 8,
                'active' => 1,
            ),
            87 => 
            array (
                'id' => 88,
                'code' => 'mini_golf',
                'service_activity_type_category_id' => 1,
                'sort_order' => 9,
                'active' => 1,
            ),
            88 => 
            array (
                'id' => 89,
                'code' => 'tennis',
                'service_activity_type_category_id' => 1,
                'sort_order' => 10,
                'active' => 1,
            ),
            89 => 
            array (
                'id' => 90,
                'code' => 'paddle_tennis',
                'service_activity_type_category_id' => 1,
                'sort_order' => 11,
                'active' => 1,
            ),
            90 => 
            array (
                'id' => 91,
                'code' => 'table_tennis',
                'service_activity_type_category_id' => 1,
                'sort_order' => 12,
                'active' => 1,
            ),
            91 => 
            array (
                'id' => 92,
                'code' => 'bowling',
                'service_activity_type_category_id' => 1,
                'sort_order' => 13,
                'active' => 1,
            ),
            92 => 
            array (
                'id' => 93,
                'code' => 'billiards',
                'service_activity_type_category_id' => 1,
                'sort_order' => 14,
                'active' => 1,
            ),
            93 => 
            array (
                'id' => 94,
                'code' => 'archery',
                'service_activity_type_category_id' => 1,
                'sort_order' => 15,
                'active' => 1,
            ),
            94 => 
            array (
                'id' => 95,
                'code' => 'shooting_range',
                'service_activity_type_category_id' => 1,
                'sort_order' => 16,
                'active' => 1,
            ),
            95 => 
            array (
                'id' => 96,
                'code' => 'camping',
                'service_activity_type_category_id' => 1,
                'sort_order' => 17,
                'active' => 1,
            ),
            96 => 
            array (
                'id' => 97,
                'code' => 'glamping',
                'service_activity_type_category_id' => 1,
                'sort_order' => 18,
                'active' => 1,
            ),
            97 => 
            array (
                'id' => 98,
                'code' => 'treasure_hunt',
                'service_activity_type_category_id' => 11,
                'sort_order' => 1,
                'active' => 1,
            ),
        ));
    }
}
