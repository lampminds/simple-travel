<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceHotelTypeCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_hotel_type_categories')->delete();
        
        \DB::table('cat_service_hotel_type_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'design',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'Location',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'service',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}