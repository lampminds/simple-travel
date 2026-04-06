<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CatServiceFeaturesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        // parent_id can point to rows that appear later in this array; FK checks would fail on insert order.
        Schema::disableForeignKeyConstraints();

        try {
            \DB::table('service_featurables')->delete();
            \DB::table('cat_service_feature_scopes')->delete();
            \DB::table('cat_service_feature_translations')->delete();
            \DB::table('cat_service_features')->delete();

            \DB::table('cat_service_features')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'organic_food',
                'service_feature_category_id' => 1,
                'sort_order' => 0,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'locally_sourced',
                'service_feature_category_id' => 1,
                'sort_order' => 1,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'homemade',
                'service_feature_category_id' => 1,
                'sort_order' => 2,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'seasonal_menu',
                'service_feature_category_id' => 1,
                'sort_order' => 3,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'wine_selection',
                'service_feature_category_id' => 2,
                'sort_order' => 4,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'craft_beer',
                'service_feature_category_id' => 2,
                'sort_order' => 5,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'cocktails',
                'service_feature_category_id' => 2,
                'sort_order' => 6,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'specialty_coffee',
                'service_feature_category_id' => 2,
                'sort_order' => 7,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'vegetarian_options',
                'service_feature_category_id' => 3,
                'sort_order' => 8,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'vegan_options',
                'service_feature_category_id' => 3,
                'sort_order' => 9,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'gluten_free_options',
                'service_feature_category_id' => 3,
                'sort_order' => 10,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'lactose_free_options',
                'service_feature_category_id' => 3,
                'sort_order' => 11,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'table_service',
                'service_feature_category_id' => 4,
                'sort_order' => 12,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'self_service',
                'service_feature_category_id' => 4,
                'sort_order' => 13,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'takeaway',
                'service_feature_category_id' => 4,
                'sort_order' => 14,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'delivery',
                'service_feature_category_id' => 4,
                'sort_order' => 15,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'sommelier',
                'service_feature_category_id' => 4,
                'sort_order' => 16,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'multilingual_staff',
                'service_feature_category_id' => 4,
                'sort_order' => 17,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'live_music',
                'service_feature_category_id' => 5,
                'sort_order' => 18,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'dj',
                'service_feature_category_id' => 5,
                'sort_order' => 19,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'romantic',
                'service_feature_category_id' => 5,
                'sort_order' => 20,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'family_friendly',
                'service_feature_category_id' => 5,
                'sort_order' => 21,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'adults_only',
                'service_feature_category_id' => 5,
                'sort_order' => 22,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'panoramic_view',
                'service_feature_category_id' => 5,
                'sort_order' => 23,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'outdoor_seating',
                'service_feature_category_id' => 6,
                'sort_order' => 24,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'indoor_seating',
                'service_feature_category_id' => 6,
                'sort_order' => 25,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'terrace',
                'service_feature_category_id' => 6,
                'sort_order' => 26,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'parking',
                'service_feature_category_id' => 6,
                'sort_order' => 27,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'wifi',
                'service_feature_category_id' => 6,
                'sort_order' => 28,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'air_conditioning',
                'service_feature_category_id' => 6,
                'sort_order' => 29,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'heating',
                'service_feature_category_id' => 6,
                'sort_order' => 30,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'wheelchair_access',
                'service_feature_category_id' => 7,
                'sort_order' => 31,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 103,
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'accessible_restroom',
                'service_feature_category_id' => 7,
                'sort_order' => 32,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 103,
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'credit_cards',
                'service_feature_category_id' => 8,
                'sort_order' => 33,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'code' => 'debit_cards',
                'service_feature_category_id' => 8,
                'sort_order' => 34,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'code' => 'cash',
                'service_feature_category_id' => 8,
                'sort_order' => 35,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'code' => 'digital_payments',
                'service_feature_category_id' => 8,
                'sort_order' => 36,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'code' => 'reservation_required',
                'service_feature_category_id' => 9,
                'sort_order' => 37,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'code' => 'reservation_recommended',
                'service_feature_category_id' => 9,
                'sort_order' => 38,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'code' => 'walk_ins_allowed',
                'service_feature_category_id' => 9,
                'sort_order' => 39,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'code' => 'beachfront',
                'service_feature_category_id' => 10,
                'sort_order' => 40,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'code' => 'city_center',
                'service_feature_category_id' => 10,
                'sort_order' => 41,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'code' => 'mountain_view',
                'service_feature_category_id' => 10,
                'sort_order' => 42,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'code' => 'lake_view',
                'service_feature_category_id' => 10,
                'sort_order' => 43,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'code' => 'guided_experience',
                'service_feature_category_id' => 11,
                'sort_order' => 44,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'code' => 'interactive',
                'service_feature_category_id' => 11,
                'sort_order' => 45,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'code' => 'show_included',
                'service_feature_category_id' => 11,
                'sort_order' => 46,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
                'code' => 'wifi_rooms',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 29,
            ),
            48 => 
            array (
                'id' => 49,
                'code' => 'wifi_public_areas',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 29,
            ),
            49 => 
            array (
                'id' => 50,
                'code' => 'internet_terminal',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'code' => 'business_center',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            51 => 
            array (
                'id' => 52,
                'code' => 'laundry',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            52 => 
            array (
                'id' => 53,
                'code' => 'dry_cleaning',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            53 => 
            array (
                'id' => 54,
                'code' => 'ironing_service',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            54 => 
            array (
                'id' => 55,
                'code' => 'daily_housekeeping',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            55 => 
            array (
                'id' => 56,
                'code' => 'currency_exchange',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            56 => 
            array (
                'id' => 57,
                'code' => 'atm_on_site',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            57 => 
            array (
                'id' => 58,
                'code' => 'gift_shop',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            58 => 
            array (
                'id' => 59,
                'code' => 'hair_salon',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            59 => 
            array (
                'id' => 60,
                'code' => 'room_minibar',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            60 => 
            array (
                'id' => 61,
                'code' => 'coffee_maker',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            61 => 
            array (
                'id' => 62,
                'code' => 'indoor_pool',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 120,
            ),
            62 => 
            array (
                'id' => 63,
                'code' => 'outdoor_pool',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 120,
            ),
            63 => 
            array (
                'id' => 64,
                'code' => 'gym',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 121,
            ),
            64 => 
            array (
                'id' => 65,
                'code' => 'spa',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 121,
            ),
            65 => 
            array (
                'id' => 66,
                'code' => 'sauna',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 121,
            ),
            66 => 
            array (
                'id' => 67,
                'code' => 'jacuzzi',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 121,
            ),
            67 => 
            array (
                'id' => 68,
                'code' => 'massage_service',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 121,
            ),
            68 => 
            array (
                'id' => 69,
                'code' => 'yoga_room',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 121,
            ),
            69 => 
            array (
                'id' => 70,
                'code' => 'garden',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            70 => 
            array (
                'id' => 71,
                'code' => 'private_beach',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            71 => 
            array (
                'id' => 72,
                'code' => 'kids_club',
                'service_feature_category_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            72 => 
            array (
                'id' => 73,
                'code' => 'playground',
                'service_feature_category_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            73 => 
            array (
                'id' => 74,
                'code' => 'babysitting',
                'service_feature_category_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            74 => 
            array (
                'id' => 75,
                'code' => 'family_rooms',
                'service_feature_category_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            75 => 
            array (
                'id' => 76,
                'code' => 'child_friendly',
                'service_feature_category_id' => 5,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            76 => 
            array (
                'id' => 77,
                'code' => 'free_parking',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 28,
            ),
            77 => 
            array (
                'id' => 78,
                'code' => 'paid_parking',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 28,
            ),
            78 => 
            array (
                'id' => 79,
                'code' => 'valet_parking',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 28,
            ),
            79 => 
            array (
                'id' => 80,
                'code' => 'electric_charging',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            80 => 
            array (
                'id' => 81,
                'code' => 'airport_shuttle',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            81 => 
            array (
                'id' => 82,
                'code' => 'car_rental',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            82 => 
            array (
                'id' => 83,
                'code' => 'free_bicycle',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            83 => 
            array (
                'id' => 84,
                'code' => 'bicycle_rental',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            84 => 
            array (
                'id' => 85,
                'code' => 'front_desk_24hr',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            85 => 
            array (
                'id' => 86,
                'code' => 'concierge',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            86 => 
            array (
                'id' => 87,
                'code' => 'express_checkin',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            87 => 
            array (
                'id' => 88,
                'code' => 'checkout_express',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            88 => 
            array (
                'id' => 89,
                'code' => 'luggage_storage',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            89 => 
            array (
                'id' => 90,
                'code' => 'tour_desk',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            90 => 
            array (
                'id' => 91,
                'code' => 'breakfast_available',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 122,
            ),
            91 => 
            array (
                'id' => 92,
                'code' => 'restaurant_on_site',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            92 => 
            array (
                'id' => 93,
                'code' => 'room_service',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            93 => 
            array (
                'id' => 94,
                'code' => 'coffee_shop',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            94 => 
            array (
                'id' => 95,
                'code' => 'snack_bar',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 123,
            ),
            95 => 
            array (
                'id' => 96,
                'code' => 'buffet_breakfast',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 122,
            ),
            96 => 
            array (
                'id' => 97,
                'code' => 'continental_breakfast',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 122,
            ),
            97 => 
            array (
                'id' => 98,
                'code' => 'meeting_rooms',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            98 => 
            array (
                'id' => 99,
                'code' => 'conference_facilities',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            99 => 
            array (
                'id' => 100,
                'code' => 'banquet_hall',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            100 => 
            array (
                'id' => 101,
                'code' => 'event_planning',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            101 => 
            array (
                'id' => 102,
                'code' => 'audiovisual_equipment',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            102 => 
            array (
                'id' => 103,
                'code' => 'accessible',
                'service_feature_category_id' => 7,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            103 => 
            array (
                'id' => 104,
                'code' => 'accessible_rooms',
                'service_feature_category_id' => 7,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => 103,
            ),
            104 => 
            array (
                'id' => 105,
                'code' => 'accessible_bathroom',
                'service_feature_category_id' => 7,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            105 => 
            array (
                'id' => 106,
                'code' => 'pets_allowed',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            106 => 
            array (
                'id' => 107,
                'code' => 'smoking_allowed',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            107 => 
            array (
                'id' => 108,
                'code' => 'non_smoking_rooms',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            108 => 
            array (
                'id' => 109,
                'code' => 'safe_at_reception',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            109 => 
            array (
                'id' => 110,
                'code' => 'safe_at_room',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            110 => 
            array (
                'id' => 111,
                'code' => 'security_24hr',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            111 => 
            array (
                'id' => 112,
                'code' => 'smoke_detectors',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            112 => 
            array (
                'id' => 113,
                'code' => 'cctv',
                'service_feature_category_id' => 4,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            113 => 
            array (
                'id' => 114,
                'code' => 'hiking',
                'service_feature_category_id' => 11,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            114 => 
            array (
                'id' => 115,
                'code' => 'ski_storage',
                'service_feature_category_id' => 11,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            115 => 
            array (
                'id' => 116,
                'code' => 'ski_rental',
                'service_feature_category_id' => 11,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            116 => 
            array (
                'id' => 117,
                'code' => 'ski_school',
                'service_feature_category_id' => 11,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            117 => 
            array (
                'id' => 118,
                'code' => 'cycling',
                'service_feature_category_id' => 11,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            118 => 
            array (
                'id' => 119,
                'code' => 'water_sports',
                'service_feature_category_id' => 11,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            119 => 
            array (
                'id' => 120,
                'code' => 'pool',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
            120 => 
            array (
                'id' => 121,
                'code' => 'wellness',
                'service_feature_category_id' => 6,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 0,
                'parent_id' => NULL,
            ),
            121 => 
            array (
                'id' => 122,
                'code' => 'breakfast',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 0,
                'parent_id' => NULL,
            ),
            122 => 
            array (
                'id' => 123,
                'code' => 'food',
                'service_feature_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
                'is_selectable' => 1,
                'parent_id' => NULL,
            ),
        ));
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}