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
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-06-04 00:00:00',
                'is_active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1405.00000000',
                'units_per_usd_sell' => '1455.00000000',
                'starting_at' => '2026-06-04 00:00:00',
                'is_active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.95330147',
                'units_per_usd_sell' => '5.12657171',
                'starting_at' => '2026-06-04 00:00:00',
                'is_active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-20 00:00:00',
                'is_active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1370.00000000',
                'units_per_usd_sell' => '1420.00000000',
                'starting_at' => '2026-05-20 00:00:00',
                'is_active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.91855130',
                'units_per_usd_sell' => '5.09504959',
                'starting_at' => '2026-05-20 00:00:00',
                'is_active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-21 00:00:00',
                'is_active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1360.00000000',
                'units_per_usd_sell' => '1410.00000000',
                'starting_at' => '2026-05-21 00:00:00',
                'is_active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.90583679',
                'units_per_usd_sell' => '5.08319129',
                'starting_at' => '2026-05-21 00:00:00',
                'is_active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-22 00:00:00',
                'is_active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1375.00000000',
                'units_per_usd_sell' => '1425.00000000',
                'starting_at' => '2026-05-22 00:00:00',
                'is_active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.94877211',
                'units_per_usd_sell' => '5.12571306',
                'starting_at' => '2026-05-22 00:00:00',
                'is_active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-26 00:00:00',
                'is_active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1380.00000000',
                'units_per_usd_sell' => '1430.00000000',
                'starting_at' => '2026-05-26 00:00:00',
                'is_active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.93640798',
                'units_per_usd_sell' => '5.11225694',
                'starting_at' => '2026-05-26 00:00:00',
                'is_active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-27 00:00:00',
                'is_active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1380.00000000',
                'units_per_usd_sell' => '1430.00000000',
                'starting_at' => '2026-05-27 00:00:00',
                'is_active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.95834808',
                'units_per_usd_sell' => '5.13499839',
                'starting_at' => '2026-05-27 00:00:00',
                'is_active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-06-02 00:00:00',
                'is_active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1400.00000000',
                'units_per_usd_sell' => '1450.00000000',
                'starting_at' => '2026-06-02 00:00:00',
                'is_active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.93484593',
                'units_per_usd_sell' => '5.10807633',
                'starting_at' => '2026-06-02 00:00:00',
                'is_active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-06-03 00:00:00',
                'is_active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1410.00000000',
                'units_per_usd_sell' => '1460.00000000',
                'starting_at' => '2026-06-03 00:00:00',
                'is_active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.98703195',
                'units_per_usd_sell' => '5.16086888',
                'starting_at' => '2026-06-03 00:00:00',
                'is_active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-28 00:00:00',
                'is_active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1380.00000000',
                'units_per_usd_sell' => '1430.00000000',
                'starting_at' => '2026-05-28 00:00:00',
                'is_active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.94031703',
                'units_per_usd_sell' => '5.11630653',
                'starting_at' => '2026-05-28 00:00:00',
                'is_active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-05-29 00:00:00',
                'is_active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1380.00000000',
                'units_per_usd_sell' => '1430.00000000',
                'starting_at' => '2026-05-29 00:00:00',
                'is_active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.95732035',
                'units_per_usd_sell' => '5.13392360',
                'starting_at' => '2026-05-29 00:00:00',
                'is_active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-06-04 00:00:00',
                'is_active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1405.00000000',
                'units_per_usd_sell' => '1455.00000000',
                'starting_at' => '2026-06-04 00:00:00',
                'is_active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '4.95330147',
                'units_per_usd_sell' => '5.12657171',
                'starting_at' => '2026-06-04 00:00:00',
                'is_active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'account_id' => NULL,
                'currency_id' => 1,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1.00000000',
                'units_per_usd_sell' => '1.00000000',
                'starting_at' => '2026-06-05 00:00:00',
                'is_active' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'account_id' => NULL,
                'currency_id' => 2,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '1410.00000000',
                'units_per_usd_sell' => '1460.00000000',
                'starting_at' => '2026-06-05 00:00:00',
                'is_active' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'account_id' => NULL,
                'currency_id' => 3,
                'source' => 'dolarapi',
                'units_per_usd_buy' => '5.06079595',
                'units_per_usd_sell' => '5.23725294',
                'starting_at' => '2026-06-05 00:00:00',
                'is_active' => 1,
            ),
        ));
        
        
    }
}