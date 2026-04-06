<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceDetailTopicCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_detail_topic_categories')->delete();
        
        \DB::table('cat_service_detail_topic_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'general_information',
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'trade_policies',
                'sort_order' => 2,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'policies',
                'sort_order' => 3,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'service_details',
                'sort_order' => 4,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'passenger_policies',
                'sort_order' => 5,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'passenger_requirements',
                'sort_order' => 6,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'preparation',
                'sort_order' => 7,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'transport',
                'sort_order' => 8,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'hotel_details',
                'sort_order' => 9,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'legals',
                'sort_order' => 10,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'assistance',
                'sort_order' => 11,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'local_regulations',
                'sort_order' => 12,
                'active' => 1,
            ),
        ));
        
        
    }
}