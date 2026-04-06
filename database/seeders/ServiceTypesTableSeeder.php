<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cat_service_types')->delete();

        \DB::table('cat_service_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'code' => 'hotel',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'transport',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 =>
            array (
                'id' => 3,
                'code' => 'excursion',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 =>
            array (
                'id' => 4,
                'code' => 'gastronomy',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));


    }
}
