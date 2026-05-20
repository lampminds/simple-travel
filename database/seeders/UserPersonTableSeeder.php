<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserPersonTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_person')->delete();
        
        \DB::table('user_person')->insert(array (
            0 => 
            array (
                'id' => 2,
                'user_id' => 2,
                'person_id' => 2,
            ),
        ));
        
        
    }
}