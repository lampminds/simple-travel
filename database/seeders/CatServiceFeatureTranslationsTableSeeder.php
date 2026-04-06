<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatServiceFeatureTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_feature_translations')->delete();
        
        \DB::table('cat_service_feature_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_feature_id' => 1,
                'language_id' => 1,
                'name' => 'Organic Food',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'service_feature_id' => 2,
                'language_id' => 1,
                'name' => 'Locally Sourced',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'service_feature_id' => 3,
                'language_id' => 1,
                'name' => 'Homemade',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'service_feature_id' => 4,
                'language_id' => 1,
                'name' => 'Seasonal Menu',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'service_feature_id' => 5,
                'language_id' => 1,
                'name' => 'Wine Selection',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'service_feature_id' => 6,
                'language_id' => 1,
                'name' => 'Craft Beer',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'service_feature_id' => 7,
                'language_id' => 1,
                'name' => 'Cocktails',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'service_feature_id' => 8,
                'language_id' => 1,
                'name' => 'Specialty Coffee',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'service_feature_id' => 9,
                'language_id' => 1,
                'name' => 'Vegetarian Options',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'service_feature_id' => 10,
                'language_id' => 1,
                'name' => 'Vegan Options',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'service_feature_id' => 11,
                'language_id' => 1,
                'name' => 'Gluten Free Options',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'service_feature_id' => 12,
                'language_id' => 1,
                'name' => 'Lactose Free Options',
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'service_feature_id' => 13,
                'language_id' => 1,
                'name' => 'Table Service',
                'description' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'service_feature_id' => 14,
                'language_id' => 1,
                'name' => 'Self Service',
                'description' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'service_feature_id' => 15,
                'language_id' => 1,
                'name' => 'Takeaway',
                'description' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'service_feature_id' => 16,
                'language_id' => 1,
                'name' => 'Delivery',
                'description' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'service_feature_id' => 17,
                'language_id' => 1,
                'name' => 'Sommelier',
                'description' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'service_feature_id' => 18,
                'language_id' => 1,
                'name' => 'Multilingual Staff',
                'description' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'service_feature_id' => 19,
                'language_id' => 1,
                'name' => 'Live Music',
                'description' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'service_feature_id' => 20,
                'language_id' => 1,
                'name' => 'Dj',
                'description' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'service_feature_id' => 21,
                'language_id' => 1,
                'name' => 'Romantic',
                'description' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'service_feature_id' => 22,
                'language_id' => 1,
                'name' => 'Family Friendly',
                'description' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'service_feature_id' => 23,
                'language_id' => 1,
                'name' => 'Adults Only',
                'description' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'service_feature_id' => 24,
                'language_id' => 1,
                'name' => 'Panoramic View',
                'description' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'service_feature_id' => 25,
                'language_id' => 1,
                'name' => 'Outdoor Seating',
                'description' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'service_feature_id' => 26,
                'language_id' => 1,
                'name' => 'Indoor Seating',
                'description' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'service_feature_id' => 27,
                'language_id' => 1,
                'name' => 'Terrace',
                'description' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'service_feature_id' => 28,
                'language_id' => 1,
                'name' => 'Parking',
                'description' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'service_feature_id' => 29,
                'language_id' => 1,
                'name' => 'Wifi',
                'description' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'service_feature_id' => 30,
                'language_id' => 1,
                'name' => 'Air Conditioning',
                'description' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'service_feature_id' => 31,
                'language_id' => 1,
                'name' => 'Heating',
                'description' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'service_feature_id' => 32,
                'language_id' => 1,
                'name' => 'Wheelchair Access',
                'description' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'service_feature_id' => 33,
                'language_id' => 1,
                'name' => 'Accessible Restroom',
                'description' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'service_feature_id' => 34,
                'language_id' => 1,
                'name' => 'Credit Cards',
                'description' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'service_feature_id' => 35,
                'language_id' => 1,
                'name' => 'Debit Cards',
                'description' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'service_feature_id' => 36,
                'language_id' => 1,
                'name' => 'Cash',
                'description' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'service_feature_id' => 37,
                'language_id' => 1,
                'name' => 'Digital Payments',
                'description' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'service_feature_id' => 38,
                'language_id' => 1,
                'name' => 'Reservation Required',
                'description' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'service_feature_id' => 39,
                'language_id' => 1,
                'name' => 'Reservation Recommended',
                'description' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'service_feature_id' => 40,
                'language_id' => 1,
                'name' => 'Walk Ins Allowed',
                'description' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'service_feature_id' => 41,
                'language_id' => 1,
                'name' => 'Beachfront',
                'description' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'service_feature_id' => 42,
                'language_id' => 1,
                'name' => 'City Center',
                'description' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'service_feature_id' => 43,
                'language_id' => 1,
                'name' => 'Mountain View',
                'description' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'service_feature_id' => 44,
                'language_id' => 1,
                'name' => 'Lake View',
                'description' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'service_feature_id' => 45,
                'language_id' => 1,
                'name' => 'Guided Experience',
                'description' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'service_feature_id' => 46,
                'language_id' => 1,
                'name' => 'Interactive',
                'description' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'service_feature_id' => 47,
                'language_id' => 1,
                'name' => 'Show Included',
                'description' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
                'service_feature_id' => 48,
                'language_id' => 1,
                'name' => 'Wifi Rooms',
                'description' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
                'service_feature_id' => 49,
                'language_id' => 1,
                'name' => 'Wifi Public Areas',
                'description' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
                'service_feature_id' => 50,
                'language_id' => 1,
                'name' => 'Internet Terminal',
                'description' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'service_feature_id' => 51,
                'language_id' => 1,
                'name' => 'Business Center',
                'description' => NULL,
            ),
            51 => 
            array (
                'id' => 52,
                'service_feature_id' => 52,
                'language_id' => 1,
                'name' => 'Laundry',
                'description' => NULL,
            ),
            52 => 
            array (
                'id' => 53,
                'service_feature_id' => 53,
                'language_id' => 1,
                'name' => 'Dry Cleaning',
                'description' => NULL,
            ),
            53 => 
            array (
                'id' => 54,
                'service_feature_id' => 54,
                'language_id' => 1,
                'name' => 'Ironing Service',
                'description' => NULL,
            ),
            54 => 
            array (
                'id' => 55,
                'service_feature_id' => 55,
                'language_id' => 1,
                'name' => 'Daily Housekeeping',
                'description' => NULL,
            ),
            55 => 
            array (
                'id' => 56,
                'service_feature_id' => 56,
                'language_id' => 1,
                'name' => 'Currency Exchange',
                'description' => NULL,
            ),
            56 => 
            array (
                'id' => 57,
                'service_feature_id' => 57,
                'language_id' => 1,
                'name' => 'Atm On Site',
                'description' => NULL,
            ),
            57 => 
            array (
                'id' => 58,
                'service_feature_id' => 58,
                'language_id' => 1,
                'name' => 'Gift Shop',
                'description' => NULL,
            ),
            58 => 
            array (
                'id' => 59,
                'service_feature_id' => 59,
                'language_id' => 1,
                'name' => 'Hair Salon',
                'description' => NULL,
            ),
            59 => 
            array (
                'id' => 60,
                'service_feature_id' => 60,
                'language_id' => 1,
                'name' => 'Room Minibar',
                'description' => NULL,
            ),
            60 => 
            array (
                'id' => 61,
                'service_feature_id' => 61,
                'language_id' => 1,
                'name' => 'Coffee Maker',
                'description' => NULL,
            ),
            61 => 
            array (
                'id' => 62,
                'service_feature_id' => 62,
                'language_id' => 1,
                'name' => 'Indoor Pool',
                'description' => NULL,
            ),
            62 => 
            array (
                'id' => 63,
                'service_feature_id' => 63,
                'language_id' => 1,
                'name' => 'Outdoor Pool',
                'description' => NULL,
            ),
            63 => 
            array (
                'id' => 64,
                'service_feature_id' => 64,
                'language_id' => 1,
                'name' => 'Gym',
                'description' => NULL,
            ),
            64 => 
            array (
                'id' => 65,
                'service_feature_id' => 65,
                'language_id' => 1,
                'name' => 'Spa',
                'description' => NULL,
            ),
            65 => 
            array (
                'id' => 66,
                'service_feature_id' => 66,
                'language_id' => 1,
                'name' => 'Sauna',
                'description' => NULL,
            ),
            66 => 
            array (
                'id' => 67,
                'service_feature_id' => 67,
                'language_id' => 1,
                'name' => 'Jacuzzi',
                'description' => NULL,
            ),
            67 => 
            array (
                'id' => 68,
                'service_feature_id' => 68,
                'language_id' => 1,
                'name' => 'Massage Service',
                'description' => NULL,
            ),
            68 => 
            array (
                'id' => 69,
                'service_feature_id' => 69,
                'language_id' => 1,
                'name' => 'Yoga Room',
                'description' => NULL,
            ),
            69 => 
            array (
                'id' => 70,
                'service_feature_id' => 70,
                'language_id' => 1,
                'name' => 'Garden',
                'description' => NULL,
            ),
            70 => 
            array (
                'id' => 71,
                'service_feature_id' => 71,
                'language_id' => 1,
                'name' => 'Private Beach',
                'description' => NULL,
            ),
            71 => 
            array (
                'id' => 72,
                'service_feature_id' => 72,
                'language_id' => 1,
                'name' => 'Kids Club',
                'description' => NULL,
            ),
            72 => 
            array (
                'id' => 73,
                'service_feature_id' => 73,
                'language_id' => 1,
                'name' => 'Playground',
                'description' => NULL,
            ),
            73 => 
            array (
                'id' => 74,
                'service_feature_id' => 74,
                'language_id' => 1,
                'name' => 'Babysitting',
                'description' => NULL,
            ),
            74 => 
            array (
                'id' => 75,
                'service_feature_id' => 75,
                'language_id' => 1,
                'name' => 'Family Rooms',
                'description' => NULL,
            ),
            75 => 
            array (
                'id' => 76,
                'service_feature_id' => 76,
                'language_id' => 1,
                'name' => 'Child Friendly',
                'description' => NULL,
            ),
            76 => 
            array (
                'id' => 77,
                'service_feature_id' => 77,
                'language_id' => 1,
                'name' => 'Free Parking',
                'description' => NULL,
            ),
            77 => 
            array (
                'id' => 78,
                'service_feature_id' => 78,
                'language_id' => 1,
                'name' => 'Paid Parking',
                'description' => NULL,
            ),
            78 => 
            array (
                'id' => 79,
                'service_feature_id' => 79,
                'language_id' => 1,
                'name' => 'Valet Parking',
                'description' => NULL,
            ),
            79 => 
            array (
                'id' => 80,
                'service_feature_id' => 80,
                'language_id' => 1,
                'name' => 'Electric Charging',
                'description' => NULL,
            ),
            80 => 
            array (
                'id' => 81,
                'service_feature_id' => 81,
                'language_id' => 1,
                'name' => 'Airport Shuttle',
                'description' => NULL,
            ),
            81 => 
            array (
                'id' => 82,
                'service_feature_id' => 82,
                'language_id' => 1,
                'name' => 'Car Rental',
                'description' => NULL,
            ),
            82 => 
            array (
                'id' => 83,
                'service_feature_id' => 83,
                'language_id' => 1,
                'name' => 'Free Bicycle',
                'description' => NULL,
            ),
            83 => 
            array (
                'id' => 84,
                'service_feature_id' => 84,
                'language_id' => 1,
                'name' => 'Bicycle Rental',
                'description' => NULL,
            ),
            84 => 
            array (
                'id' => 85,
                'service_feature_id' => 85,
                'language_id' => 1,
                'name' => 'Front Desk 24Hr',
                'description' => NULL,
            ),
            85 => 
            array (
                'id' => 86,
                'service_feature_id' => 86,
                'language_id' => 1,
                'name' => 'Concierge',
                'description' => NULL,
            ),
            86 => 
            array (
                'id' => 87,
                'service_feature_id' => 87,
                'language_id' => 1,
                'name' => 'Express Checkin',
                'description' => NULL,
            ),
            87 => 
            array (
                'id' => 88,
                'service_feature_id' => 88,
                'language_id' => 1,
                'name' => 'Checkout Express',
                'description' => NULL,
            ),
            88 => 
            array (
                'id' => 89,
                'service_feature_id' => 89,
                'language_id' => 1,
                'name' => 'Luggage Storage',
                'description' => NULL,
            ),
            89 => 
            array (
                'id' => 90,
                'service_feature_id' => 90,
                'language_id' => 1,
                'name' => 'Tour Desk',
                'description' => NULL,
            ),
            90 => 
            array (
                'id' => 91,
                'service_feature_id' => 91,
                'language_id' => 1,
                'name' => 'Breakfast Available',
                'description' => NULL,
            ),
            91 => 
            array (
                'id' => 92,
                'service_feature_id' => 92,
                'language_id' => 1,
                'name' => 'Restaurant On Site',
                'description' => NULL,
            ),
            92 => 
            array (
                'id' => 93,
                'service_feature_id' => 93,
                'language_id' => 1,
                'name' => 'Room Service',
                'description' => NULL,
            ),
            93 => 
            array (
                'id' => 94,
                'service_feature_id' => 94,
                'language_id' => 1,
                'name' => 'Coffee Shop',
                'description' => NULL,
            ),
            94 => 
            array (
                'id' => 95,
                'service_feature_id' => 95,
                'language_id' => 1,
                'name' => 'Snack Bar',
                'description' => NULL,
            ),
            95 => 
            array (
                'id' => 96,
                'service_feature_id' => 96,
                'language_id' => 1,
                'name' => 'Buffet Breakfast',
                'description' => NULL,
            ),
            96 => 
            array (
                'id' => 97,
                'service_feature_id' => 97,
                'language_id' => 1,
                'name' => 'Continental Breakfast',
                'description' => NULL,
            ),
            97 => 
            array (
                'id' => 98,
                'service_feature_id' => 98,
                'language_id' => 1,
                'name' => 'Meeting Rooms',
                'description' => NULL,
            ),
            98 => 
            array (
                'id' => 99,
                'service_feature_id' => 99,
                'language_id' => 1,
                'name' => 'Conference Facilities',
                'description' => NULL,
            ),
            99 => 
            array (
                'id' => 100,
                'service_feature_id' => 100,
                'language_id' => 1,
                'name' => 'Banquet Hall',
                'description' => NULL,
            ),
            100 => 
            array (
                'id' => 101,
                'service_feature_id' => 101,
                'language_id' => 1,
                'name' => 'Event Planning',
                'description' => NULL,
            ),
            101 => 
            array (
                'id' => 102,
                'service_feature_id' => 102,
                'language_id' => 1,
                'name' => 'Audiovisual Equipment',
                'description' => NULL,
            ),
            102 => 
            array (
                'id' => 104,
                'service_feature_id' => 104,
                'language_id' => 1,
                'name' => 'Accessible Rooms',
                'description' => NULL,
            ),
            103 => 
            array (
                'id' => 106,
                'service_feature_id' => 106,
                'language_id' => 1,
                'name' => 'Pets Allowed',
                'description' => NULL,
            ),
            104 => 
            array (
                'id' => 107,
                'service_feature_id' => 107,
                'language_id' => 1,
                'name' => 'Smoking Allowed',
                'description' => NULL,
            ),
            105 => 
            array (
                'id' => 108,
                'service_feature_id' => 108,
                'language_id' => 1,
                'name' => 'Non Smoking Rooms',
                'description' => NULL,
            ),
            106 => 
            array (
                'id' => 109,
                'service_feature_id' => 109,
                'language_id' => 1,
                'name' => 'Safe At Reception',
                'description' => NULL,
            ),
            107 => 
            array (
                'id' => 110,
                'service_feature_id' => 110,
                'language_id' => 1,
                'name' => 'Safe At Room',
                'description' => NULL,
            ),
            108 => 
            array (
                'id' => 111,
                'service_feature_id' => 111,
                'language_id' => 1,
                'name' => 'Security 24Hr',
                'description' => NULL,
            ),
            109 => 
            array (
                'id' => 112,
                'service_feature_id' => 112,
                'language_id' => 1,
                'name' => 'Smoke Detectors',
                'description' => NULL,
            ),
            110 => 
            array (
                'id' => 113,
                'service_feature_id' => 113,
                'language_id' => 1,
                'name' => 'Cctv',
                'description' => NULL,
            ),
            111 => 
            array (
                'id' => 114,
                'service_feature_id' => 114,
                'language_id' => 1,
                'name' => 'Hiking',
                'description' => NULL,
            ),
            112 => 
            array (
                'id' => 115,
                'service_feature_id' => 115,
                'language_id' => 1,
                'name' => 'Ski Storage',
                'description' => NULL,
            ),
            113 => 
            array (
                'id' => 116,
                'service_feature_id' => 116,
                'language_id' => 1,
                'name' => 'Ski Rental',
                'description' => NULL,
            ),
            114 => 
            array (
                'id' => 117,
                'service_feature_id' => 117,
                'language_id' => 1,
                'name' => 'Ski School',
                'description' => NULL,
            ),
            115 => 
            array (
                'id' => 118,
                'service_feature_id' => 118,
                'language_id' => 1,
                'name' => 'Cycling',
                'description' => NULL,
            ),
            116 => 
            array (
                'id' => 119,
                'service_feature_id' => 119,
                'language_id' => 1,
                'name' => 'Water Sports',
                'description' => NULL,
            ),
            117 => 
            array (
                'id' => 120,
                'service_feature_id' => 1,
                'language_id' => 2,
                'name' => 'Comida orgánica',
                'description' => NULL,
            ),
            118 => 
            array (
                'id' => 121,
                'service_feature_id' => 2,
                'language_id' => 2,
                'name' => 'Ingredientes locales',
                'description' => NULL,
            ),
            119 => 
            array (
                'id' => 122,
                'service_feature_id' => 3,
                'language_id' => 2,
                'name' => 'Comida casera',
                'description' => NULL,
            ),
            120 => 
            array (
                'id' => 123,
                'service_feature_id' => 4,
                'language_id' => 2,
                'name' => 'Menú de temporada',
                'description' => NULL,
            ),
            121 => 
            array (
                'id' => 124,
                'service_feature_id' => 5,
                'language_id' => 2,
                'name' => 'Buena selección de vinos',
                'description' => NULL,
            ),
            122 => 
            array (
                'id' => 125,
                'service_feature_id' => 6,
                'language_id' => 2,
                'name' => 'Cerveza artesanal',
                'description' => NULL,
            ),
            123 => 
            array (
                'id' => 126,
                'service_feature_id' => 7,
                'language_id' => 2,
                'name' => 'Cócteles',
                'description' => NULL,
            ),
            124 => 
            array (
                'id' => 127,
                'service_feature_id' => 8,
                'language_id' => 2,
                'name' => 'Café de especialidad',
                'description' => NULL,
            ),
            125 => 
            array (
                'id' => 128,
                'service_feature_id' => 9,
                'language_id' => 2,
                'name' => 'Opciones vegetarianas',
                'description' => NULL,
            ),
            126 => 
            array (
                'id' => 129,
                'service_feature_id' => 10,
                'language_id' => 2,
                'name' => 'Opciones veganas',
                'description' => NULL,
            ),
            127 => 
            array (
                'id' => 130,
                'service_feature_id' => 11,
                'language_id' => 2,
                'name' => 'Opciones sin gluten',
                'description' => NULL,
            ),
            128 => 
            array (
                'id' => 131,
                'service_feature_id' => 12,
                'language_id' => 2,
                'name' => 'Sin lactosa',
                'description' => NULL,
            ),
            129 => 
            array (
                'id' => 132,
                'service_feature_id' => 13,
                'language_id' => 2,
                'name' => 'Servicio a la mesa',
                'description' => NULL,
            ),
            130 => 
            array (
                'id' => 133,
                'service_feature_id' => 14,
                'language_id' => 2,
                'name' => 'Autoservicio',
                'description' => NULL,
            ),
            131 => 
            array (
                'id' => 134,
                'service_feature_id' => 15,
                'language_id' => 2,
                'name' => 'Para llevar',
                'description' => NULL,
            ),
            132 => 
            array (
                'id' => 135,
                'service_feature_id' => 16,
                'language_id' => 2,
                'name' => 'Delivery',
                'description' => NULL,
            ),
            133 => 
            array (
                'id' => 136,
                'service_feature_id' => 17,
                'language_id' => 2,
                'name' => 'Sommelier',
                'description' => NULL,
            ),
            134 => 
            array (
                'id' => 137,
                'service_feature_id' => 18,
                'language_id' => 2,
                'name' => 'Personal multilingüe',
                'description' => NULL,
            ),
            135 => 
            array (
                'id' => 138,
                'service_feature_id' => 19,
                'language_id' => 2,
                'name' => 'Música en vivo',
                'description' => NULL,
            ),
            136 => 
            array (
                'id' => 139,
                'service_feature_id' => 20,
                'language_id' => 2,
                'name' => 'DJ',
                'description' => NULL,
            ),
            137 => 
            array (
                'id' => 140,
                'service_feature_id' => 21,
                'language_id' => 2,
                'name' => 'Ambiente romántico',
                'description' => NULL,
            ),
            138 => 
            array (
                'id' => 141,
                'service_feature_id' => 22,
                'language_id' => 2,
                'name' => 'Familiar',
                'description' => NULL,
            ),
            139 => 
            array (
                'id' => 142,
                'service_feature_id' => 23,
                'language_id' => 2,
                'name' => 'Solo adultos',
                'description' => NULL,
            ),
            140 => 
            array (
                'id' => 143,
                'service_feature_id' => 24,
                'language_id' => 2,
                'name' => 'Vista panorámica',
                'description' => NULL,
            ),
            141 => 
            array (
                'id' => 144,
                'service_feature_id' => 25,
                'language_id' => 2,
                'name' => 'Mesas al aire libre',
                'description' => NULL,
            ),
            142 => 
            array (
                'id' => 145,
                'service_feature_id' => 26,
                'language_id' => 2,
                'name' => 'Interior',
                'description' => NULL,
            ),
            143 => 
            array (
                'id' => 146,
                'service_feature_id' => 27,
                'language_id' => 2,
                'name' => 'Terraza',
                'description' => NULL,
            ),
            144 => 
            array (
                'id' => 147,
                'service_feature_id' => 28,
                'language_id' => 2,
                'name' => 'Estacionamiento',
                'description' => NULL,
            ),
            145 => 
            array (
                'id' => 148,
                'service_feature_id' => 29,
                'language_id' => 2,
                'name' => 'WiFi',
                'description' => NULL,
            ),
            146 => 
            array (
                'id' => 149,
                'service_feature_id' => 30,
                'language_id' => 2,
                'name' => 'Aire acondicionado',
                'description' => NULL,
            ),
            147 => 
            array (
                'id' => 150,
                'service_feature_id' => 31,
                'language_id' => 2,
                'name' => 'Calefacción',
                'description' => NULL,
            ),
            148 => 
            array (
                'id' => 151,
                'service_feature_id' => 32,
                'language_id' => 2,
                'name' => 'Acceso para sillas de ruedas',
                'description' => NULL,
            ),
            149 => 
            array (
                'id' => 152,
                'service_feature_id' => 33,
                'language_id' => 2,
                'name' => 'Baño accesible',
                'description' => NULL,
            ),
            150 => 
            array (
                'id' => 153,
                'service_feature_id' => 34,
                'language_id' => 2,
                'name' => 'Tarjetas de crédito',
                'description' => NULL,
            ),
            151 => 
            array (
                'id' => 154,
                'service_feature_id' => 35,
                'language_id' => 2,
                'name' => 'Tarjetas de débito',
                'description' => NULL,
            ),
            152 => 
            array (
                'id' => 155,
                'service_feature_id' => 36,
                'language_id' => 2,
                'name' => 'Efectivo',
                'description' => NULL,
            ),
            153 => 
            array (
                'id' => 156,
                'service_feature_id' => 37,
                'language_id' => 2,
                'name' => 'Pagos digitales',
                'description' => NULL,
            ),
            154 => 
            array (
                'id' => 157,
                'service_feature_id' => 38,
                'language_id' => 2,
                'name' => 'Requiere reserva',
                'description' => NULL,
            ),
            155 => 
            array (
                'id' => 158,
                'service_feature_id' => 39,
                'language_id' => 2,
                'name' => 'Reserva recomendada',
                'description' => NULL,
            ),
            156 => 
            array (
                'id' => 159,
                'service_feature_id' => 40,
                'language_id' => 2,
                'name' => 'Sin reserva',
                'description' => NULL,
            ),
            157 => 
            array (
                'id' => 160,
                'service_feature_id' => 41,
                'language_id' => 2,
                'name' => 'Frente a la playa',
                'description' => NULL,
            ),
            158 => 
            array (
                'id' => 161,
                'service_feature_id' => 42,
                'language_id' => 2,
                'name' => 'Centro',
                'description' => NULL,
            ),
            159 => 
            array (
                'id' => 162,
                'service_feature_id' => 43,
                'language_id' => 2,
                'name' => 'Vista a la montaña',
                'description' => NULL,
            ),
            160 => 
            array (
                'id' => 163,
                'service_feature_id' => 44,
                'language_id' => 2,
                'name' => 'Vista al lago',
                'description' => NULL,
            ),
            161 => 
            array (
                'id' => 164,
                'service_feature_id' => 45,
                'language_id' => 2,
                'name' => 'Experiencia guiada',
                'description' => NULL,
            ),
            162 => 
            array (
                'id' => 165,
                'service_feature_id' => 46,
                'language_id' => 2,
                'name' => 'Interactiva',
                'description' => NULL,
            ),
            163 => 
            array (
                'id' => 166,
                'service_feature_id' => 47,
                'language_id' => 2,
                'name' => 'Incluye espectáculo',
                'description' => NULL,
            ),
            164 => 
            array (
                'id' => 167,
                'service_feature_id' => 48,
                'language_id' => 2,
                'name' => 'WiFi en las habitaciones',
                'description' => NULL,
            ),
            165 => 
            array (
                'id' => 168,
                'service_feature_id' => 49,
                'language_id' => 2,
                'name' => 'WiFi en las zonas comunes',
                'description' => NULL,
            ),
            166 => 
            array (
                'id' => 169,
                'service_feature_id' => 50,
                'language_id' => 2,
                'name' => 'Terminal de internet',
                'description' => NULL,
            ),
            167 => 
            array (
                'id' => 170,
                'service_feature_id' => 51,
                'language_id' => 2,
                'name' => 'Centro de negocios',
                'description' => NULL,
            ),
            168 => 
            array (
                'id' => 171,
                'service_feature_id' => 52,
                'language_id' => 2,
                'name' => 'Lavandería',
                'description' => NULL,
            ),
            169 => 
            array (
                'id' => 172,
                'service_feature_id' => 53,
                'language_id' => 2,
                'name' => 'Limpieza en seco',
                'description' => NULL,
            ),
            170 => 
            array (
                'id' => 173,
                'service_feature_id' => 54,
                'language_id' => 2,
                'name' => 'Servicio de planchado',
                'description' => NULL,
            ),
            171 => 
            array (
                'id' => 174,
                'service_feature_id' => 55,
                'language_id' => 2,
                'name' => 'Limpieza diaria',
                'description' => NULL,
            ),
            172 => 
            array (
                'id' => 175,
                'service_feature_id' => 56,
                'language_id' => 2,
                'name' => 'Cambio de divisas',
                'description' => NULL,
            ),
            173 => 
            array (
                'id' => 176,
                'service_feature_id' => 57,
                'language_id' => 2,
                'name' => 'Cajero automático en el establecimiento',
                'description' => NULL,
            ),
            174 => 
            array (
                'id' => 177,
                'service_feature_id' => 58,
                'language_id' => 2,
                'name' => 'Tienda de regalos',
                'description' => NULL,
            ),
            175 => 
            array (
                'id' => 178,
                'service_feature_id' => 59,
                'language_id' => 2,
                'name' => 'Salón de belleza',
                'description' => NULL,
            ),
            176 => 
            array (
                'id' => 179,
                'service_feature_id' => 60,
                'language_id' => 2,
                'name' => 'Minibar en la habitación',
                'description' => NULL,
            ),
            177 => 
            array (
                'id' => 180,
                'service_feature_id' => 61,
                'language_id' => 2,
                'name' => 'Cafetera',
                'description' => NULL,
            ),
            178 => 
            array (
                'id' => 181,
                'service_feature_id' => 62,
                'language_id' => 2,
                'name' => 'Piscina cubierta',
                'description' => NULL,
            ),
            179 => 
            array (
                'id' => 182,
                'service_feature_id' => 63,
                'language_id' => 2,
                'name' => 'Piscina al aire libre',
                'description' => NULL,
            ),
            180 => 
            array (
                'id' => 183,
                'service_feature_id' => 64,
                'language_id' => 2,
                'name' => 'Gimnasio',
                'description' => NULL,
            ),
            181 => 
            array (
                'id' => 184,
                'service_feature_id' => 65,
                'language_id' => 2,
                'name' => 'Spa',
                'description' => NULL,
            ),
            182 => 
            array (
                'id' => 185,
                'service_feature_id' => 66,
                'language_id' => 2,
                'name' => 'Sauna',
                'description' => NULL,
            ),
            183 => 
            array (
                'id' => 186,
                'service_feature_id' => 67,
                'language_id' => 2,
                'name' => 'Jacuzzi',
                'description' => NULL,
            ),
            184 => 
            array (
                'id' => 187,
                'service_feature_id' => 68,
                'language_id' => 2,
                'name' => 'Servicio de masajes',
                'description' => NULL,
            ),
            185 => 
            array (
                'id' => 188,
                'service_feature_id' => 69,
                'language_id' => 2,
                'name' => 'Sala de yoga',
                'description' => NULL,
            ),
            186 => 
            array (
                'id' => 189,
                'service_feature_id' => 70,
                'language_id' => 2,
                'name' => 'Jardín',
                'description' => NULL,
            ),
            187 => 
            array (
                'id' => 190,
                'service_feature_id' => 71,
                'language_id' => 2,
                'name' => 'Playa privada',
                'description' => NULL,
            ),
            188 => 
            array (
                'id' => 191,
                'service_feature_id' => 72,
                'language_id' => 2,
                'name' => 'Club infantil',
                'description' => NULL,
            ),
            189 => 
            array (
                'id' => 192,
                'service_feature_id' => 73,
                'language_id' => 2,
                'name' => 'Zona de juegos',
                'description' => NULL,
            ),
            190 => 
            array (
                'id' => 193,
                'service_feature_id' => 74,
                'language_id' => 2,
                'name' => 'Servicio de niñera',
                'description' => NULL,
            ),
            191 => 
            array (
                'id' => 194,
                'service_feature_id' => 75,
                'language_id' => 2,
                'name' => 'Habitaciones familiares',
                'description' => NULL,
            ),
            192 => 
            array (
                'id' => 195,
                'service_feature_id' => 76,
                'language_id' => 2,
                'name' => 'Apto para niños',
                'description' => NULL,
            ),
            193 => 
            array (
                'id' => 196,
                'service_feature_id' => 77,
                'language_id' => 2,
                'name' => 'Parking gratuito',
                'description' => NULL,
            ),
            194 => 
            array (
                'id' => 197,
                'service_feature_id' => 78,
                'language_id' => 2,
                'name' => 'Parking de pago',
                'description' => NULL,
            ),
            195 => 
            array (
                'id' => 198,
                'service_feature_id' => 79,
                'language_id' => 2,
                'name' => 'Parking con aparcacoches',
                'description' => NULL,
            ),
            196 => 
            array (
                'id' => 199,
                'service_feature_id' => 80,
                'language_id' => 2,
                'name' => 'Estación de carga eléctrica',
                'description' => NULL,
            ),
            197 => 
            array (
                'id' => 200,
                'service_feature_id' => 81,
                'language_id' => 2,
                'name' => 'Traslado al aeropuerto',
                'description' => NULL,
            ),
            198 => 
            array (
                'id' => 201,
                'service_feature_id' => 82,
                'language_id' => 2,
                'name' => 'Alquiler de autos',
                'description' => NULL,
            ),
            199 => 
            array (
                'id' => 202,
                'service_feature_id' => 83,
                'language_id' => 2,
                'name' => 'Bicicleta gratis',
                'description' => NULL,
            ),
            200 => 
            array (
                'id' => 203,
                'service_feature_id' => 84,
                'language_id' => 2,
                'name' => 'Alquiler de bicicletas',
                'description' => NULL,
            ),
            201 => 
            array (
                'id' => 204,
                'service_feature_id' => 85,
                'language_id' => 2,
                'name' => 'Recepción 24h',
                'description' => NULL,
            ),
            202 => 
            array (
                'id' => 205,
                'service_feature_id' => 86,
                'language_id' => 2,
                'name' => 'Conserjería',
                'description' => NULL,
            ),
            203 => 
            array (
                'id' => 206,
                'service_feature_id' => 87,
                'language_id' => 2,
                'name' => 'Check-in exprés',
                'description' => NULL,
            ),
            204 => 
            array (
                'id' => 207,
                'service_feature_id' => 88,
                'language_id' => 2,
                'name' => 'Salida exprés',
                'description' => NULL,
            ),
            205 => 
            array (
                'id' => 208,
                'service_feature_id' => 89,
                'language_id' => 2,
                'name' => 'Consigna de equipaje',
                'description' => NULL,
            ),
            206 => 
            array (
                'id' => 209,
                'service_feature_id' => 90,
                'language_id' => 2,
                'name' => 'Mostrador de excursiones',
                'description' => NULL,
            ),
            207 => 
            array (
                'id' => 210,
                'service_feature_id' => 91,
                'language_id' => 2,
                'name' => 'Desayuno disponible',
                'description' => NULL,
            ),
            208 => 
            array (
                'id' => 211,
                'service_feature_id' => 92,
                'language_id' => 2,
                'name' => 'Restaurante en el establecimiento',
                'description' => NULL,
            ),
            209 => 
            array (
                'id' => 212,
                'service_feature_id' => 93,
                'language_id' => 2,
                'name' => 'Servicio de habitaciones',
                'description' => NULL,
            ),
            210 => 
            array (
                'id' => 213,
                'service_feature_id' => 94,
                'language_id' => 2,
                'name' => 'Cafetería',
                'description' => NULL,
            ),
            211 => 
            array (
                'id' => 214,
                'service_feature_id' => 95,
                'language_id' => 2,
                'name' => 'Bar de snacks',
                'description' => NULL,
            ),
            212 => 
            array (
                'id' => 215,
                'service_feature_id' => 96,
                'language_id' => 2,
                'name' => 'Desayuno tipo buffet',
                'description' => NULL,
            ),
            213 => 
            array (
                'id' => 216,
                'service_feature_id' => 97,
                'language_id' => 2,
                'name' => 'Desayuno continental',
                'description' => NULL,
            ),
            214 => 
            array (
                'id' => 217,
                'service_feature_id' => 98,
                'language_id' => 2,
                'name' => 'Salas de reuniones',
                'description' => NULL,
            ),
            215 => 
            array (
                'id' => 218,
                'service_feature_id' => 99,
                'language_id' => 2,
                'name' => 'Instalaciones para conferencias',
                'description' => NULL,
            ),
            216 => 
            array (
                'id' => 219,
                'service_feature_id' => 100,
                'language_id' => 2,
                'name' => 'Salón de banquetes',
                'description' => NULL,
            ),
            217 => 
            array (
                'id' => 220,
                'service_feature_id' => 101,
                'language_id' => 2,
                'name' => 'Organización de eventos',
                'description' => NULL,
            ),
            218 => 
            array (
                'id' => 221,
                'service_feature_id' => 102,
                'language_id' => 2,
                'name' => 'Equipamiento audiovisual',
                'description' => NULL,
            ),
            219 => 
            array (
                'id' => 223,
                'service_feature_id' => 104,
                'language_id' => 2,
                'name' => 'Habitaciones accesibles',
                'description' => NULL,
            ),
            220 => 
            array (
                'id' => 225,
                'service_feature_id' => 106,
                'language_id' => 2,
                'name' => 'Se admiten mascotas',
                'description' => NULL,
            ),
            221 => 
            array (
                'id' => 226,
                'service_feature_id' => 107,
                'language_id' => 2,
                'name' => 'Se permite fumar',
                'description' => NULL,
            ),
            222 => 
            array (
                'id' => 227,
                'service_feature_id' => 108,
                'language_id' => 2,
                'name' => 'Habitaciones para no fumadores',
                'description' => NULL,
            ),
            223 => 
            array (
                'id' => 228,
                'service_feature_id' => 109,
                'language_id' => 2,
                'name' => 'Caja fuerte en recepción',
                'description' => NULL,
            ),
            224 => 
            array (
                'id' => 229,
                'service_feature_id' => 110,
                'language_id' => 2,
                'name' => 'Caja fuerte en la habitación',
                'description' => NULL,
            ),
            225 => 
            array (
                'id' => 230,
                'service_feature_id' => 111,
                'language_id' => 2,
                'name' => 'Seguridad 24h',
                'description' => NULL,
            ),
            226 => 
            array (
                'id' => 231,
                'service_feature_id' => 112,
                'language_id' => 2,
                'name' => 'Detectores de humo',
                'description' => NULL,
            ),
            227 => 
            array (
                'id' => 232,
                'service_feature_id' => 113,
                'language_id' => 2,
                'name' => 'CCTV',
                'description' => NULL,
            ),
            228 => 
            array (
                'id' => 233,
                'service_feature_id' => 114,
                'language_id' => 2,
                'name' => 'Senderismo',
                'description' => NULL,
            ),
            229 => 
            array (
                'id' => 234,
                'service_feature_id' => 115,
                'language_id' => 2,
                'name' => 'Guardaesquís',
                'description' => NULL,
            ),
            230 => 
            array (
                'id' => 235,
                'service_feature_id' => 116,
                'language_id' => 2,
                'name' => 'Alquiler de esquíes',
                'description' => NULL,
            ),
            231 => 
            array (
                'id' => 236,
                'service_feature_id' => 117,
                'language_id' => 2,
                'name' => 'Escuela de esquí',
                'description' => NULL,
            ),
            232 => 
            array (
                'id' => 237,
                'service_feature_id' => 118,
                'language_id' => 2,
                'name' => 'Ciclismo',
                'description' => NULL,
            ),
            233 => 
            array (
                'id' => 238,
                'service_feature_id' => 119,
                'language_id' => 2,
                'name' => 'Deportes acuáticos',
                'description' => NULL,
            ),
            234 => 
            array (
                'id' => 239,
                'service_feature_id' => 1,
                'language_id' => 3,
                'name' => 'Comida orgânica',
                'description' => NULL,
            ),
            235 => 
            array (
                'id' => 240,
                'service_feature_id' => 2,
                'language_id' => 3,
                'name' => 'Ingredientes locais',
                'description' => NULL,
            ),
            236 => 
            array (
                'id' => 241,
                'service_feature_id' => 3,
                'language_id' => 3,
                'name' => 'Comida caseira',
                'description' => NULL,
            ),
            237 => 
            array (
                'id' => 242,
                'service_feature_id' => 4,
                'language_id' => 3,
                'name' => 'Menu sazonal',
                'description' => NULL,
            ),
            238 => 
            array (
                'id' => 243,
                'service_feature_id' => 5,
                'language_id' => 3,
                'name' => 'Boa seleção de vinhos',
                'description' => NULL,
            ),
            239 => 
            array (
                'id' => 244,
                'service_feature_id' => 6,
                'language_id' => 3,
                'name' => 'Cerveja artesanal',
                'description' => NULL,
            ),
            240 => 
            array (
                'id' => 245,
                'service_feature_id' => 7,
                'language_id' => 3,
                'name' => 'Coquetéis',
                'description' => NULL,
            ),
            241 => 
            array (
                'id' => 246,
                'service_feature_id' => 8,
                'language_id' => 3,
                'name' => 'Café de especialidade',
                'description' => NULL,
            ),
            242 => 
            array (
                'id' => 247,
                'service_feature_id' => 9,
                'language_id' => 3,
                'name' => 'Opções vegetarianas',
                'description' => NULL,
            ),
            243 => 
            array (
                'id' => 248,
                'service_feature_id' => 10,
                'language_id' => 3,
                'name' => 'Opções veganas',
                'description' => NULL,
            ),
            244 => 
            array (
                'id' => 249,
                'service_feature_id' => 11,
                'language_id' => 3,
                'name' => 'Opções sem glúten',
                'description' => NULL,
            ),
            245 => 
            array (
                'id' => 250,
                'service_feature_id' => 12,
                'language_id' => 3,
                'name' => 'Sem lactose',
                'description' => NULL,
            ),
            246 => 
            array (
                'id' => 251,
                'service_feature_id' => 13,
                'language_id' => 3,
                'name' => 'Serviço à mesa',
                'description' => NULL,
            ),
            247 => 
            array (
                'id' => 252,
                'service_feature_id' => 14,
                'language_id' => 3,
                'name' => 'Autoatendimento',
                'description' => NULL,
            ),
            248 => 
            array (
                'id' => 253,
                'service_feature_id' => 15,
                'language_id' => 3,
                'name' => 'Para levar',
                'description' => NULL,
            ),
            249 => 
            array (
                'id' => 254,
                'service_feature_id' => 16,
                'language_id' => 3,
                'name' => 'Delivery',
                'description' => NULL,
            ),
            250 => 
            array (
                'id' => 255,
                'service_feature_id' => 17,
                'language_id' => 3,
                'name' => 'Sommelier',
                'description' => NULL,
            ),
            251 => 
            array (
                'id' => 256,
                'service_feature_id' => 18,
                'language_id' => 3,
                'name' => 'Equipe multilíngue',
                'description' => NULL,
            ),
            252 => 
            array (
                'id' => 257,
                'service_feature_id' => 19,
                'language_id' => 3,
                'name' => 'Música ao vivo',
                'description' => NULL,
            ),
            253 => 
            array (
                'id' => 258,
                'service_feature_id' => 20,
                'language_id' => 3,
                'name' => 'DJ',
                'description' => NULL,
            ),
            254 => 
            array (
                'id' => 259,
                'service_feature_id' => 21,
                'language_id' => 3,
                'name' => 'Ambiente romântico',
                'description' => NULL,
            ),
            255 => 
            array (
                'id' => 260,
                'service_feature_id' => 22,
                'language_id' => 3,
                'name' => 'Familiar',
                'description' => NULL,
            ),
            256 => 
            array (
                'id' => 261,
                'service_feature_id' => 23,
                'language_id' => 3,
                'name' => 'Apenas adultos',
                'description' => NULL,
            ),
            257 => 
            array (
                'id' => 262,
                'service_feature_id' => 24,
                'language_id' => 3,
                'name' => 'Vista panorâmica',
                'description' => NULL,
            ),
            258 => 
            array (
                'id' => 263,
                'service_feature_id' => 25,
                'language_id' => 3,
                'name' => 'Mesas ao ar livre',
                'description' => NULL,
            ),
            259 => 
            array (
                'id' => 264,
                'service_feature_id' => 26,
                'language_id' => 3,
                'name' => 'Interior',
                'description' => NULL,
            ),
            260 => 
            array (
                'id' => 265,
                'service_feature_id' => 27,
                'language_id' => 3,
                'name' => 'Terraço',
                'description' => NULL,
            ),
            261 => 
            array (
                'id' => 266,
                'service_feature_id' => 28,
                'language_id' => 3,
                'name' => 'Estacionamento',
                'description' => NULL,
            ),
            262 => 
            array (
                'id' => 267,
                'service_feature_id' => 29,
                'language_id' => 3,
                'name' => 'Wi-Fi',
                'description' => NULL,
            ),
            263 => 
            array (
                'id' => 268,
                'service_feature_id' => 30,
                'language_id' => 3,
                'name' => 'Ar-condicionado',
                'description' => NULL,
            ),
            264 => 
            array (
                'id' => 269,
                'service_feature_id' => 31,
                'language_id' => 3,
                'name' => 'Aquecimento',
                'description' => NULL,
            ),
            265 => 
            array (
                'id' => 270,
                'service_feature_id' => 32,
                'language_id' => 3,
                'name' => 'Acesso para cadeiras de rodas',
                'description' => NULL,
            ),
            266 => 
            array (
                'id' => 271,
                'service_feature_id' => 33,
                'language_id' => 3,
                'name' => 'Banheiro acessível',
                'description' => NULL,
            ),
            267 => 
            array (
                'id' => 272,
                'service_feature_id' => 34,
                'language_id' => 3,
                'name' => 'Cartões de crédito',
                'description' => NULL,
            ),
            268 => 
            array (
                'id' => 273,
                'service_feature_id' => 35,
                'language_id' => 3,
                'name' => 'Cartões de débito',
                'description' => NULL,
            ),
            269 => 
            array (
                'id' => 274,
                'service_feature_id' => 36,
                'language_id' => 3,
                'name' => 'Dinheiro',
                'description' => NULL,
            ),
            270 => 
            array (
                'id' => 275,
                'service_feature_id' => 37,
                'language_id' => 3,
                'name' => 'Pagamentos digitais',
                'description' => NULL,
            ),
            271 => 
            array (
                'id' => 276,
                'service_feature_id' => 38,
                'language_id' => 3,
                'name' => 'Requer reserva',
                'description' => NULL,
            ),
            272 => 
            array (
                'id' => 277,
                'service_feature_id' => 39,
                'language_id' => 3,
                'name' => 'Reserva recomendada',
                'description' => NULL,
            ),
            273 => 
            array (
                'id' => 278,
                'service_feature_id' => 40,
                'language_id' => 3,
                'name' => 'Sem reserva',
                'description' => NULL,
            ),
            274 => 
            array (
                'id' => 279,
                'service_feature_id' => 41,
                'language_id' => 3,
                'name' => 'Frente à praia',
                'description' => NULL,
            ),
            275 => 
            array (
                'id' => 280,
                'service_feature_id' => 42,
                'language_id' => 3,
                'name' => 'Centro',
                'description' => NULL,
            ),
            276 => 
            array (
                'id' => 281,
                'service_feature_id' => 43,
                'language_id' => 3,
                'name' => 'Vista para a montanha',
                'description' => NULL,
            ),
            277 => 
            array (
                'id' => 282,
                'service_feature_id' => 44,
                'language_id' => 3,
                'name' => 'Vista para o lago',
                'description' => NULL,
            ),
            278 => 
            array (
                'id' => 283,
                'service_feature_id' => 45,
                'language_id' => 3,
                'name' => 'Experiência guiada',
                'description' => NULL,
            ),
            279 => 
            array (
                'id' => 284,
                'service_feature_id' => 46,
                'language_id' => 3,
                'name' => 'Interativa',
                'description' => NULL,
            ),
            280 => 
            array (
                'id' => 285,
                'service_feature_id' => 47,
                'language_id' => 3,
                'name' => 'Inclui show',
                'description' => NULL,
            ),
            281 => 
            array (
                'id' => 286,
                'service_feature_id' => 48,
                'language_id' => 3,
                'name' => 'Wi-Fi nos quartos',
                'description' => NULL,
            ),
            282 => 
            array (
                'id' => 287,
                'service_feature_id' => 49,
                'language_id' => 3,
                'name' => 'Wi-Fi nas áreas comuns',
                'description' => NULL,
            ),
            283 => 
            array (
                'id' => 288,
                'service_feature_id' => 50,
                'language_id' => 3,
                'name' => 'Terminal de internet',
                'description' => NULL,
            ),
            284 => 
            array (
                'id' => 289,
                'service_feature_id' => 51,
                'language_id' => 3,
                'name' => 'Centro de negócios',
                'description' => NULL,
            ),
            285 => 
            array (
                'id' => 290,
                'service_feature_id' => 52,
                'language_id' => 3,
                'name' => 'Lavanderia',
                'description' => NULL,
            ),
            286 => 
            array (
                'id' => 291,
                'service_feature_id' => 53,
                'language_id' => 3,
                'name' => 'Lavagem a seco',
                'description' => NULL,
            ),
            287 => 
            array (
                'id' => 292,
                'service_feature_id' => 54,
                'language_id' => 3,
                'name' => 'Serviço de passar roupa',
                'description' => NULL,
            ),
            288 => 
            array (
                'id' => 293,
                'service_feature_id' => 55,
                'language_id' => 3,
                'name' => 'Limpeza diária',
                'description' => NULL,
            ),
            289 => 
            array (
                'id' => 294,
                'service_feature_id' => 56,
                'language_id' => 3,
                'name' => 'Câmbio de moeda',
                'description' => NULL,
            ),
            290 => 
            array (
                'id' => 295,
                'service_feature_id' => 57,
                'language_id' => 3,
                'name' => 'Caixa eletrônico no local',
                'description' => NULL,
            ),
            291 => 
            array (
                'id' => 296,
                'service_feature_id' => 58,
                'language_id' => 3,
                'name' => 'Loja de presentes',
                'description' => NULL,
            ),
            292 => 
            array (
                'id' => 297,
                'service_feature_id' => 59,
                'language_id' => 3,
                'name' => 'Salão de beleza',
                'description' => NULL,
            ),
            293 => 
            array (
                'id' => 298,
                'service_feature_id' => 60,
                'language_id' => 3,
                'name' => 'Mini-bar no quarto',
                'description' => NULL,
            ),
            294 => 
            array (
                'id' => 299,
                'service_feature_id' => 61,
                'language_id' => 3,
                'name' => 'Cafeteira',
                'description' => NULL,
            ),
            295 => 
            array (
                'id' => 300,
                'service_feature_id' => 62,
                'language_id' => 3,
                'name' => 'Piscina coberta',
                'description' => NULL,
            ),
            296 => 
            array (
                'id' => 301,
                'service_feature_id' => 63,
                'language_id' => 3,
                'name' => 'Piscina ao ar livre',
                'description' => NULL,
            ),
            297 => 
            array (
                'id' => 302,
                'service_feature_id' => 64,
                'language_id' => 3,
                'name' => 'Academia',
                'description' => NULL,
            ),
            298 => 
            array (
                'id' => 303,
                'service_feature_id' => 65,
                'language_id' => 3,
                'name' => 'Spa',
                'description' => NULL,
            ),
            299 => 
            array (
                'id' => 304,
                'service_feature_id' => 66,
                'language_id' => 3,
                'name' => 'Sauna',
                'description' => NULL,
            ),
            300 => 
            array (
                'id' => 305,
                'service_feature_id' => 67,
                'language_id' => 3,
                'name' => 'Jacuzzi',
                'description' => NULL,
            ),
            301 => 
            array (
                'id' => 306,
                'service_feature_id' => 68,
                'language_id' => 3,
                'name' => 'Serviço de massagens',
                'description' => NULL,
            ),
            302 => 
            array (
                'id' => 307,
                'service_feature_id' => 69,
                'language_id' => 3,
                'name' => 'Sala de ioga',
                'description' => NULL,
            ),
            303 => 
            array (
                'id' => 308,
                'service_feature_id' => 70,
                'language_id' => 3,
                'name' => 'Jardim',
                'description' => NULL,
            ),
            304 => 
            array (
                'id' => 309,
                'service_feature_id' => 71,
                'language_id' => 3,
                'name' => 'Praia privativa',
                'description' => NULL,
            ),
            305 => 
            array (
                'id' => 310,
                'service_feature_id' => 72,
                'language_id' => 3,
                'name' => 'Clube infantil',
                'description' => NULL,
            ),
            306 => 
            array (
                'id' => 311,
                'service_feature_id' => 73,
                'language_id' => 3,
                'name' => 'Parque infantil',
                'description' => NULL,
            ),
            307 => 
            array (
                'id' => 312,
                'service_feature_id' => 74,
                'language_id' => 3,
                'name' => 'Serviço de babá',
                'description' => NULL,
            ),
            308 => 
            array (
                'id' => 313,
                'service_feature_id' => 75,
                'language_id' => 3,
                'name' => 'Quartos familiares',
                'description' => NULL,
            ),
            309 => 
            array (
                'id' => 314,
                'service_feature_id' => 76,
                'language_id' => 3,
                'name' => 'Amigável para crianças',
                'description' => NULL,
            ),
            310 => 
            array (
                'id' => 315,
                'service_feature_id' => 77,
                'language_id' => 3,
                'name' => 'Estacionamento gratuito',
                'description' => NULL,
            ),
            311 => 
            array (
                'id' => 316,
                'service_feature_id' => 78,
                'language_id' => 3,
                'name' => 'Estacionamento pago',
                'description' => NULL,
            ),
            312 => 
            array (
                'id' => 317,
                'service_feature_id' => 79,
                'language_id' => 3,
                'name' => 'Estacionamento com manobrista',
                'description' => NULL,
            ),
            313 => 
            array (
                'id' => 318,
                'service_feature_id' => 80,
                'language_id' => 3,
                'name' => 'Estação de recarga elétrica',
                'description' => NULL,
            ),
            314 => 
            array (
                'id' => 319,
                'service_feature_id' => 81,
                'language_id' => 3,
                'name' => 'Traslado para o aeroporto',
                'description' => NULL,
            ),
            315 => 
            array (
                'id' => 320,
                'service_feature_id' => 82,
                'language_id' => 3,
                'name' => 'Aluguel de carros',
                'description' => NULL,
            ),
            316 => 
            array (
                'id' => 321,
                'service_feature_id' => 83,
                'language_id' => 3,
                'name' => 'Bicicleta grátis',
                'description' => NULL,
            ),
            317 => 
            array (
                'id' => 322,
                'service_feature_id' => 84,
                'language_id' => 3,
                'name' => 'Aluguel de bicicletas',
                'description' => NULL,
            ),
            318 => 
            array (
                'id' => 323,
                'service_feature_id' => 85,
                'language_id' => 3,
                'name' => 'Recepção 24h',
                'description' => NULL,
            ),
            319 => 
            array (
                'id' => 324,
                'service_feature_id' => 86,
                'language_id' => 3,
                'name' => 'Concierge',
                'description' => NULL,
            ),
            320 => 
            array (
                'id' => 325,
                'service_feature_id' => 87,
                'language_id' => 3,
                'name' => 'Check-in expresso',
                'description' => NULL,
            ),
            321 => 
            array (
                'id' => 326,
                'service_feature_id' => 88,
                'language_id' => 3,
                'name' => 'Check-out expresso',
                'description' => NULL,
            ),
            322 => 
            array (
                'id' => 327,
                'service_feature_id' => 89,
                'language_id' => 3,
                'name' => 'Depósito de bagagens',
                'description' => NULL,
            ),
            323 => 
            array (
                'id' => 328,
                'service_feature_id' => 90,
                'language_id' => 3,
                'name' => 'Balcão de turismo',
                'description' => NULL,
            ),
            324 => 
            array (
                'id' => 329,
                'service_feature_id' => 91,
                'language_id' => 3,
                'name' => 'Café da manhã disponível',
                'description' => NULL,
            ),
            325 => 
            array (
                'id' => 330,
                'service_feature_id' => 92,
                'language_id' => 3,
                'name' => 'Restaurante no local',
                'description' => NULL,
            ),
            326 => 
            array (
                'id' => 331,
                'service_feature_id' => 93,
                'language_id' => 3,
                'name' => 'Serviço de quarto',
                'description' => NULL,
            ),
            327 => 
            array (
                'id' => 332,
                'service_feature_id' => 94,
                'language_id' => 3,
                'name' => 'Cafeteria',
                'description' => NULL,
            ),
            328 => 
            array (
                'id' => 333,
                'service_feature_id' => 95,
                'language_id' => 3,
                'name' => 'Bar de lanches',
                'description' => NULL,
            ),
            329 => 
            array (
                'id' => 334,
                'service_feature_id' => 96,
                'language_id' => 3,
                'name' => 'Café da manhã buffet',
                'description' => NULL,
            ),
            330 => 
            array (
                'id' => 335,
                'service_feature_id' => 97,
                'language_id' => 3,
                'name' => 'Café da manhã continental',
                'description' => NULL,
            ),
            331 => 
            array (
                'id' => 336,
                'service_feature_id' => 98,
                'language_id' => 3,
                'name' => 'Salas de reunião',
                'description' => NULL,
            ),
            332 => 
            array (
                'id' => 337,
                'service_feature_id' => 99,
                'language_id' => 3,
                'name' => 'Instalações para conferências',
                'description' => NULL,
            ),
            333 => 
            array (
                'id' => 338,
                'service_feature_id' => 100,
                'language_id' => 3,
                'name' => 'Salão de banquetes',
                'description' => NULL,
            ),
            334 => 
            array (
                'id' => 339,
                'service_feature_id' => 101,
                'language_id' => 3,
                'name' => 'Organização de eventos',
                'description' => NULL,
            ),
            335 => 
            array (
                'id' => 340,
                'service_feature_id' => 102,
                'language_id' => 3,
                'name' => 'Equipamentos audiovisuais',
                'description' => NULL,
            ),
            336 => 
            array (
                'id' => 342,
                'service_feature_id' => 104,
                'language_id' => 3,
                'name' => 'Quartos acessíveis',
                'description' => NULL,
            ),
            337 => 
            array (
                'id' => 344,
                'service_feature_id' => 106,
                'language_id' => 3,
                'name' => 'Animais de estimação permitidos',
                'description' => NULL,
            ),
            338 => 
            array (
                'id' => 345,
                'service_feature_id' => 107,
                'language_id' => 3,
                'name' => 'Permitido fumar',
                'description' => NULL,
            ),
            339 => 
            array (
                'id' => 346,
                'service_feature_id' => 108,
                'language_id' => 3,
                'name' => 'Quartos para não fumantes',
                'description' => NULL,
            ),
            340 => 
            array (
                'id' => 347,
                'service_feature_id' => 109,
                'language_id' => 3,
                'name' => 'Cofre na recepção',
                'description' => NULL,
            ),
            341 => 
            array (
                'id' => 348,
                'service_feature_id' => 110,
                'language_id' => 3,
                'name' => 'Cofre no quarto',
                'description' => NULL,
            ),
            342 => 
            array (
                'id' => 349,
                'service_feature_id' => 111,
                'language_id' => 3,
                'name' => 'Segurança 24h',
                'description' => NULL,
            ),
            343 => 
            array (
                'id' => 350,
                'service_feature_id' => 112,
                'language_id' => 3,
                'name' => 'Detectores de fumaça',
                'description' => NULL,
            ),
            344 => 
            array (
                'id' => 351,
                'service_feature_id' => 113,
                'language_id' => 3,
                'name' => 'CFTV',
                'description' => NULL,
            ),
            345 => 
            array (
                'id' => 352,
                'service_feature_id' => 114,
                'language_id' => 3,
                'name' => 'Caminhada',
                'description' => NULL,
            ),
            346 => 
            array (
                'id' => 353,
                'service_feature_id' => 115,
                'language_id' => 3,
                'name' => 'Armazenamento de esquis',
                'description' => NULL,
            ),
            347 => 
            array (
                'id' => 354,
                'service_feature_id' => 116,
                'language_id' => 3,
                'name' => 'Aluguel de esquis',
                'description' => NULL,
            ),
            348 => 
            array (
                'id' => 355,
                'service_feature_id' => 117,
                'language_id' => 3,
                'name' => 'Escola de esqui',
                'description' => NULL,
            ),
            349 => 
            array (
                'id' => 356,
                'service_feature_id' => 118,
                'language_id' => 3,
                'name' => 'Ciclismo',
                'description' => NULL,
            ),
            350 => 
            array (
                'id' => 357,
                'service_feature_id' => 119,
                'language_id' => 3,
                'name' => 'Esportes aquáticos',
                'description' => NULL,
            ),
            351 => 
            array (
                'id' => 358,
                'service_feature_id' => 120,
                'language_id' => 2,
                'name' => 'Piscina',
                'description' => NULL,
            ),
            352 => 
            array (
                'id' => 359,
                'service_feature_id' => 121,
                'language_id' => 2,
                'name' => 'Bienestar',
                'description' => NULL,
            ),
            353 => 
            array (
                'id' => 360,
                'service_feature_id' => 122,
                'language_id' => 2,
                'name' => 'Desayuno',
                'description' => NULL,
            ),
            354 => 
            array (
                'id' => 361,
                'service_feature_id' => 123,
                'language_id' => 2,
                'name' => 'Comida',
                'description' => NULL,
            ),
            355 => 
            array (
                'id' => 362,
                'service_feature_id' => 103,
                'language_id' => 1,
                'name' => 'Accessibility',
                'description' => NULL,
            ),
            356 => 
            array (
                'id' => 363,
                'service_feature_id' => 103,
                'language_id' => 2,
                'name' => 'Accesibilidad',
                'description' => NULL,
            ),
            357 => 
            array (
                'id' => 364,
                'service_feature_id' => 105,
                'language_id' => 1,
                'name' => 'Accessible Bathroom',
                'description' => NULL,
            ),
            358 => 
            array (
                'id' => 365,
                'service_feature_id' => 105,
                'language_id' => 2,
                'name' => 'Baño accesible',
                'description' => NULL,
            ),
            359 => 
            array (
                'id' => 366,
                'service_feature_id' => 105,
                'language_id' => 3,
                'name' => 'Banheiro acessível',
                'description' => NULL,
            ),
        ));
        
        
    }
}