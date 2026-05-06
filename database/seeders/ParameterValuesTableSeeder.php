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
            1 => 
            array (
                'id' => 8,
                'parameter_definition_id' => 3,
                'account_id' => NULL,
                'value' => 'all',
            ),
            2 => 
            array (
                'id' => 9,
                'parameter_definition_id' => 4,
                'account_id' => NULL,
                'value' => '0',
            ),
            3 => 
            array (
                'id' => 10,
                'parameter_definition_id' => 5,
                'account_id' => NULL,
                'value' => ',',
            ),
        ));
        
        
    }
}