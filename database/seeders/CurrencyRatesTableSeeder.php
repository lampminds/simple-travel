<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrencyRatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('currency_rates')->delete();

        \DB::table('currency_rates')->insert(array (
            0 =>
            array (
                'id' => 1,
                'currency_id' => 2,
                'units_per_usd_buy' => '1415.00000000',
                'units_per_usd_sell' => '1435.00000000',
                'starting_at' => '2026-01-01 00:00:00',
            ),
        ));


    }
}
