<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanUserPriceTranslationsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('plan_user_price_translations')->delete();

        \DB::table('plan_user_price_translations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'plan_user_price_id' => 1,
                'language_id' => 2,
                'price' => '250000.00',
                'description' => 'Precio base para 1 a 4 usuarios',
            ),
            1 =>
            array (
                'id' => 2,
                'plan_user_price_id' => 2,
                'language_id' => 2,
                'price' => '400000.00',
                'description' => 'Precio base para 5 a 10 usuarios',
            ),
            2 =>
            array (
                'id' => 3,
                'plan_user_price_id' => 3,
                'language_id' => 2,
                'price' => '650000.00',
                'description' => 'Precio base para 11 a 20 usuarios',
            ),
        ));
    }
}
