<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PersonContactMethodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('person_contact_methods')->delete();
        
        \DB::table('person_contact_methods')->insert(array (
            0 => 
            array (
                'id' => 1,
                'person_id' => 2,
                'contact_type_id' => 1,
                'value' => 'gabriel@schillaci.com',
                'is_primary' => 0,
                'is_verified' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'person_id' => 1,
                'contact_type_id' => 1,
                'value' => 'promocao@ebgrouptravel.com',
                'is_primary' => 1,
                'is_verified' => 1,
            ),
        ));
        
        
    }
}