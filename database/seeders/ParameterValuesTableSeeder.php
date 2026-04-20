<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ParameterValuesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('parameter_values')->delete();
        
        \DB::table('parameter_values')->insert(array (
            0 => 
            array (
                'id' => 6,
                'parameter_definition_id' => 1,
                'account_id' => NULL,
                'value' => '7',
            ),
        ));
        
        
    }
}