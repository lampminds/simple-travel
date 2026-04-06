<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('plans')->delete();

        \DB::table('plans')->insert(array (
            0 =>
            array (
                'id' => 1,
                'code' => 'starter',
                'sort_order' => 1,
                'active' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'web',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 =>
            array (
                'id' => 3,
                'code' => 'crm',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 =>
            array (
                'id' => 4,
                'code' => 'finances',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 =>
            array (
                'id' => 5,
                'code' => 'B2B',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 =>
            array (
                'id' => 6,
                'code' => 'accounting',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));


    }
}
