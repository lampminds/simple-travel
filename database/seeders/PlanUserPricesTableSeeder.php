<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanUserPricesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('plan_user_prices')->delete();

        \DB::table('plan_user_prices')->insert(array (
            0 =>
            array (
                'id' => 1,
                'up_to' => 4,
            ),
            1 =>
            array (
                'id' => 2,
                'up_to' => 10,
            ),
            2 =>
            array (
                'id' => 3,
                'up_to' => 20,
            ),
        ));
    }
}
