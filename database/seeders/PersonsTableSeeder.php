<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PersonsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('persons')->delete();
        
        \DB::table('persons')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Valeria Moura',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Gabriel Schillaci',
            ),
        ));
        
        
    }
}