<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceFeatureCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cat_service_feature_categories')->delete();

        \DB::table('cat_service_feature_categories')->insert(array (
            0 =>
            array (
                'id' => 1,
                'code' => 'food',
                'sort_order' => 0,
                'active' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'drinks',
                'sort_order' => 1,
                'active' => 1,
            ),
            2 =>
            array (
                'id' => 3,
                'code' => 'dietary',
                'sort_order' => 2,
                'active' => 1,
            ),
            3 =>
            array (
                'id' => 4,
                'code' => 'service',
                'sort_order' => 3,
                'active' => 1,
            ),
            4 =>
            array (
                'id' => 5,
                'code' => 'ambiance',
                'sort_order' => 4,
                'active' => 1,
            ),
            5 =>
            array (
                'id' => 6,
                'code' => 'facilities',
                'sort_order' => 5,
                'active' => 1,
            ),
            6 =>
            array (
                'id' => 7,
                'code' => 'accessibility',
                'sort_order' => 6,
                'active' => 1,
            ),
            7 =>
            array (
                'id' => 8,
                'code' => 'payment',
                'sort_order' => 7,
                'active' => 1,
            ),
            8 =>
            array (
                'id' => 9,
                'code' => 'reservation',
                'sort_order' => 8,
                'active' => 1,
            ),
            9 =>
            array (
                'id' => 10,
                'code' => 'location',
                'sort_order' => 9,
                'active' => 1,
            ),
            10 =>
            array (
                'id' => 11,
                'code' => 'experience',
                'sort_order' => 10,
                'active' => 1,
            ),
        ));


    }
}
