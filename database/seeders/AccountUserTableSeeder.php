<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountUserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('account_user')->delete();

        \DB::table('account_user')->insert(array (
            0 =>
            array (
                'id' => 1,
                'account_id' => 1,
                'user_id' => 2,
            ),
        ));


    }
}
