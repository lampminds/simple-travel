<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_permissions')->delete();
        
        \DB::table('user_permissions')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'manage_account',
                'guard_name' => 'web',
            ),
            1 => 
            array (
                'id' => 1,
                'name' => 'manage_users',
                'guard_name' => 'web',
            ),
        ));
        
        
    }
}