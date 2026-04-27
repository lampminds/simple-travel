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
                    'nick' => 'eb-group-srl',
                    'code' => 'eb-group-srl-299',
                    'name' => 'EB GROUP SRL',
                    'commercial_name' => 'EB GROUP SRL',
                    'url' => NULL,
                    'email' => 'promocao@ebgrouptravel.com',
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
