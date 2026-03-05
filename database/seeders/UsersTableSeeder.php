<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'account_id' => NULL,
                'name' => 'System',
                'email' => 'system@simple-travel.net',
                'email_verified_at' => NULL,
                'password' => '',
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'two_factor_confirmed_at' => NULL,
                'kicked_out' => 0,
                'last_login_at' => NULL,
                'last_seen_at' => NULL,
                'remember_token' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'account_id' => NULL,
                'name' => 'Gabriel Schillaci',
                'email' => 'support@lampminds.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$yWav7/oR8OF7rx4czsdt1.0N4GW0JsYPwqp3vnsXY9VkjQiehVG/m',
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'two_factor_confirmed_at' => NULL,
                'kicked_out' => 0,
                'last_login_at' => NULL,
                'last_seen_at' => NULL,
                'remember_token' => NULL,
            ),
        ));


    }
}
