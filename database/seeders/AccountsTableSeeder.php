<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('accounts')->delete();

        \DB::table('accounts')->insert(array (
            0 =>
            array (
                'id' => 1,
                'nick' => 'system',
                'code' => 'system-967',
                'name' => 'Main System',
                'commercial_name' => NULL,
                'url' => NULL,
                'email' => 'system@simple-travel.net',
                'phone' => NULL,
                'address_line1' => NULL,
                'address_line2' => NULL,
                'city_id' => NULL,
                'postal_code' => NULL,
                'status' => 'active',
            ),
            1 =>
            array (
                'id' => 2,
                'nick' => 'ebgroup',
                'code' => 'ebgroup-755',
                'name' => 'EB Group',
                'commercial_name' => 'EB Group SRL',
                'url' => NULL,
                'email' => 'consultas@privateservice.tur.ar',
                'phone' => NULL,
                'address_line1' => NULL,
                'address_line2' => NULL,
                'city_id' => NULL,
                'postal_code' => NULL,
                'status' => 'active',
            ),
            2 =>
            array (
                'id' => 3,
                'nick' => 'il-buon-mangiare',
                'code' => 'il-buon-mangiare-886',
                'name' => 'Il Buon Mangiare',
                'commercial_name' => 'Il Buon Mangiare',
                'url' => NULL,
                'email' => 'pietro@conte.com.ar',
                'phone' => NULL,
                'address_line1' => NULL,
                'address_line2' => NULL,
                'city_id' => NULL,
                'postal_code' => NULL,
                'status' => 'active',
            ),
        ));


    }
}
