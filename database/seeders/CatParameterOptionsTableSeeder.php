<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatParameterOptionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_parameter_options')->delete();
        
        \DB::table('cat_parameter_options')->insert(array (
            0 => 
            array (
                'id' => 12,
                'parameter_definition_id' => 3,
                'value' => 'declined',
                'sort_order' => 30,
            ),
            1 => 
            array (
                'id' => 13,
                'parameter_definition_id' => 3,
                'value' => 'all',
                'sort_order' => 0,
            ),
            2 => 
            array (
                'id' => 14,
                'parameter_definition_id' => 3,
                'value' => 'pending',
                'sort_order' => 10,
            ),
            3 => 
            array (
                'id' => 15,
                'parameter_definition_id' => 3,
                'value' => 'accepted',
                'sort_order' => 20,
            ),
            4 => 
            array (
                'id' => 16,
                'parameter_definition_id' => 3,
                'value' => 'expired',
                'sort_order' => 40,
            ),
            5 => 
            array (
                'id' => 17,
                'parameter_definition_id' => 3,
                'value' => 'revoked',
                'sort_order' => 50,
            ),
            6 => 
            array (
                'id' => 18,
                'parameter_definition_id' => 4,
                'value' => '3',
                'sort_order' => 0,
            ),
            7 => 
            array (
                'id' => 19,
                'parameter_definition_id' => 4,
                'value' => '2',
                'sort_order' => 0,
            ),
            8 => 
            array (
                'id' => 20,
                'parameter_definition_id' => 4,
                'value' => '1',
                'sort_order' => 0,
            ),
            9 => 
            array (
                'id' => 21,
                'parameter_definition_id' => 4,
                'value' => '0',
                'sort_order' => 0,
            ),
            10 => 
            array (
                'id' => 22,
                'parameter_definition_id' => 5,
                'value' => '.',
                'sort_order' => 0,
            ),
            11 => 
            array (
                'id' => 23,
                'parameter_definition_id' => 5,
                'value' => ',',
                'sort_order' => 0,
            ),
            12 => 
            array (
                'id' => 36,
                'parameter_definition_id' => 7,
                'value' => '1',
                'sort_order' => 10,
            ),
            13 => 
            array (
                'id' => 37,
                'parameter_definition_id' => 7,
                'value' => '2',
                'sort_order' => 20,
            ),
            14 => 
            array (
                'id' => 38,
                'parameter_definition_id' => 7,
                'value' => '3',
                'sort_order' => 30,
            ),
        ));
        
        
    }
}