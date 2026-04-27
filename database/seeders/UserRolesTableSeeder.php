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
                'account_id' => 1,
                'name' => 'admin',
                'guard_name' => 'web',
            ),
            1 => 
            array (
                'id' => 5,
                'account_id' => 1,
                'name' => 'guest',
                'guard_name' => 'web',
            ),
            2 => 
            array (
                'id' => 3,
                'account_id' => 1,
                'name' => 'manager',
                'guard_name' => 'web',
            ),
            3 => 
            array (
                'id' => 2,
                'account_id' => 1,
                'name' => 'owner',
                'guard_name' => 'web',
            ),
            4 => 
            array (
                'id' => 4,
                'account_id' => 1,
                'name' => 'user',
                'guard_name' => 'web',
            ),
        ));
        
        
    }
}