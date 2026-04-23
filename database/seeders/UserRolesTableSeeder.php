<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('user_roles')->delete();

        \DB::table('user_roles')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'admin',
                'guard_name' => 'web',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'owner',
                'guard_name' => 'web',
            ),
        ));


    }
}
