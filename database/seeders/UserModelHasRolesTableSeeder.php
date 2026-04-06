<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserModelHasRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('user_model_has_roles')->delete();

        \DB::table('account_user')->updateOrInsert(
            ['account_id' => 1, 'user_id' => 2],
            ['created_at' => now(), 'updated_at' => now()]
        );

        \DB::table('user_model_has_roles')->insert(array (
            0 =>
            array (
                'role_id' => 1,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
                'account_id' => 1,
            ),
        ));


    }
}
