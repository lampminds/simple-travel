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
            2 => 
            array (
                'id' => 11,
                'parameter_definition_id' => 3,
                'language_id' => 2,
                'name' => 'Opción por defecto al visualizar invitaciones a empresas',
                'description' => NULL,
                'help' => NULL,
            ),
            3 => 
            array (
                'id' => 12,
                'parameter_definition_id' => 4,
                'language_id' => 1,
                'name' => 'Decimals',
                'description' => NULL,
                'help' => NULL,
            ),
            4 => 
            array (
                'id' => 13,
                'parameter_definition_id' => 4,
                'language_id' => 2,
                'name' => 'Decimales',
                'description' => NULL,
                'help' => NULL,
            ),
            5 => 
            array (
                'id' => 14,
                'parameter_definition_id' => 5,
                'language_id' => 1,
                'name' => 'Thousands separator',
                'description' => NULL,
                'help' => NULL,
            ),
            6 => 
            array (
                'id' => 15,
                'parameter_definition_id' => 5,
                'language_id' => 2,
                'name' => 'Separador de miles',
                'description' => NULL,
                'help' => NULL,
            ),
            7 => 
            array (
                'id' => 16,
                'parameter_definition_id' => 5,
                'language_id' => 3,
                'name' => 'Separador de miles',
                'description' => NULL,
                'help' => NULL,
            ),
            8 => 
            array (
                'id' => 17,
                'parameter_definition_id' => 6,
                'language_id' => 2,
                'name' => 'Redondeo de decimales en precio de planes',
                'description' => NULL,
                'help' => '0: redondea $35.123,10 a $35.123
-2: redondea $35.123,10 a $35.100
-3: redondea $35.123,10 a $35.000',
            ),
            9 => 
            array (
                'id' => 22,
                'parameter_definition_id' => 7,
                'language_id' => 2,
                'name' => 'Formato de fecha',
                'description' => NULL,
                'help' => NULL,
            ),
        ));
        
        
    }
}