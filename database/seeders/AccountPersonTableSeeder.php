<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountPersonTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('account_person')->delete();
        
        \DB::table('account_person')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_id' => 2,
                'person_id' => 1,
                'contact_department_id' => 1,
                'contact_position_id' => 3,
                'is_primary' => 1,
                'is_active' => 1,
                'is_public_contact' => 0,
                'is_preferred_contact_mode' => 0,
            ),
        ));
        
        
    }
}