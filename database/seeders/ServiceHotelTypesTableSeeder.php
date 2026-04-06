<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceHotelTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_hotel_types')->delete();
        
        \DB::table('cat_service_hotel_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'boutique',
                'service_hotel_type_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'theme',
                'service_hotel_type_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'ecotel',
                'service_hotel_type_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'capsule',
                'service_hotel_type_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'bubble',
                'service_hotel_type_category_id' => 1,
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'urban',
                'service_hotel_type_category_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'resort',
                'service_hotel_type_category_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'airport',
                'service_hotel_type_category_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'nature',
                'service_hotel_type_category_id' => 2,
                'sort_order' => 9999,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'business',
                'service_hotel_type_category_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'deluxe',
                'service_hotel_type_category_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'motel',
                'service_hotel_type_category_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'apart',
                'service_hotel_type_category_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'all-inclusive',
                'service_hotel_type_category_id' => 3,
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}