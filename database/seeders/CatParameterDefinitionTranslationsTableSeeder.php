<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatParameterDefinitionTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_parameter_definition_translations')->delete();
        
        \DB::table('cat_parameter_definition_translations')->insert(array (
            0 => 
            array (
                'id' => 7,
                'parameter_definition_id' => 1,
                'language_id' => 1,
                'name' => 'Days an invitation is valid for, in days',
                'description' => NULL,
                'help' => NULL,
            ),
            1 => 
            array (
                'id' => 8,
                'parameter_definition_id' => 2,
                'language_id' => 2,
                'name' => 'Cantidad máxima de reintentos de envío de una invitación',
                'description' => NULL,
                'help' => NULL,
            ),
        ));
        
        
    }
}