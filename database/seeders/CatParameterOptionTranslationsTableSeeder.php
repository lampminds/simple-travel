<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatParameterOptionTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_parameter_option_translations')->delete();
        
        \DB::table('cat_parameter_option_translations')->insert(array (
            0 => 
            array (
                'id' => 25,
                'parameter_option_id' => 12,
                'language_id' => 1,
                'name' => 'Declined',
            ),
            1 => 
            array (
                'id' => 26,
                'parameter_option_id' => 12,
                'language_id' => 2,
                'name' => 'Rechazada',
            ),
            2 => 
            array (
                'id' => 27,
                'parameter_option_id' => 13,
                'language_id' => 1,
                'name' => 'All',
            ),
            3 => 
            array (
                'id' => 28,
                'parameter_option_id' => 13,
                'language_id' => 2,
                'name' => 'Todos',
            ),
            4 => 
            array (
                'id' => 29,
                'parameter_option_id' => 14,
                'language_id' => 1,
                'name' => 'Pending',
            ),
            5 => 
            array (
                'id' => 30,
                'parameter_option_id' => 14,
                'language_id' => 2,
                'name' => 'Pendiente',
            ),
            6 => 
            array (
                'id' => 31,
                'parameter_option_id' => 14,
                'language_id' => 3,
                'name' => 'Pendente',
            ),
            7 => 
            array (
                'id' => 32,
                'parameter_option_id' => 15,
                'language_id' => 1,
                'name' => 'Accepted',
            ),
            8 => 
            array (
                'id' => 33,
                'parameter_option_id' => 15,
                'language_id' => 2,
                'name' => 'Aceptado',
            ),
            9 => 
            array (
                'id' => 34,
                'parameter_option_id' => 16,
                'language_id' => 1,
                'name' => 'Expired',
            ),
            10 => 
            array (
                'id' => 35,
                'parameter_option_id' => 16,
                'language_id' => 2,
                'name' => 'Expirada',
            ),
            11 => 
            array (
                'id' => 36,
                'parameter_option_id' => 17,
                'language_id' => 1,
                'name' => 'Revoked',
            ),
            12 => 
            array (
                'id' => 37,
                'parameter_option_id' => 17,
                'language_id' => 2,
                'name' => 'Revocada',
            ),
            13 => 
            array (
                'id' => 38,
                'parameter_option_id' => 18,
                'language_id' => 1,
                'name' => '.000',
            ),
            14 => 
            array (
                'id' => 39,
                'parameter_option_id' => 18,
                'language_id' => 2,
                'name' => ',000',
            ),
            15 => 
            array (
                'id' => 40,
                'parameter_option_id' => 18,
                'language_id' => 3,
                'name' => ',000',
            ),
            16 => 
            array (
                'id' => 41,
                'parameter_option_id' => 19,
                'language_id' => 1,
                'name' => '.00',
            ),
            17 => 
            array (
                'id' => 42,
                'parameter_option_id' => 19,
                'language_id' => 2,
                'name' => ',00',
            ),
            18 => 
            array (
                'id' => 43,
                'parameter_option_id' => 19,
                'language_id' => 3,
                'name' => ',00',
            ),
            19 => 
            array (
                'id' => 44,
                'parameter_option_id' => 20,
                'language_id' => 1,
                'name' => '.0',
            ),
            20 => 
            array (
                'id' => 45,
                'parameter_option_id' => 20,
                'language_id' => 2,
                'name' => ',0',
            ),
            21 => 
            array (
                'id' => 46,
                'parameter_option_id' => 20,
                'language_id' => 3,
                'name' => ',0',
            ),
            22 => 
            array (
                'id' => 47,
                'parameter_option_id' => 21,
                'language_id' => 1,
                'name' => 'none',
            ),
            23 => 
            array (
                'id' => 48,
                'parameter_option_id' => 21,
                'language_id' => 2,
                'name' => 'no',
            ),
            24 => 
            array (
                'id' => 49,
                'parameter_option_id' => 21,
                'language_id' => 3,
                'name' => 'no',
            ),
            25 => 
            array (
                'id' => 50,
                'parameter_option_id' => 22,
                'language_id' => 1,
                'name' => 'Period',
            ),
            26 => 
            array (
                'id' => 51,
                'parameter_option_id' => 22,
                'language_id' => 2,
                'name' => 'Punto',
            ),
            27 => 
            array (
                'id' => 52,
                'parameter_option_id' => 22,
                'language_id' => 3,
                'name' => 'Punto',
            ),
            28 => 
            array (
                'id' => 53,
                'parameter_option_id' => 23,
                'language_id' => 1,
                'name' => 'Comma',
            ),
            29 => 
            array (
                'id' => 54,
                'parameter_option_id' => 23,
                'language_id' => 2,
                'name' => 'Coma',
            ),
            30 => 
            array (
                'id' => 55,
                'parameter_option_id' => 23,
                'language_id' => 3,
                'name' => 'Coma',
            ),
            31 => 
            array (
                'id' => 92,
                'parameter_option_id' => 36,
                'language_id' => 1,
                'name' => 'en',
            ),
            32 => 
            array (
                'id' => 93,
                'parameter_option_id' => 36,
                'language_id' => 2,
                'name' => 'en',
            ),
            33 => 
            array (
                'id' => 94,
                'parameter_option_id' => 36,
                'language_id' => 3,
                'name' => 'en',
            ),
            34 => 
            array (
                'id' => 95,
                'parameter_option_id' => 37,
                'language_id' => 1,
                'name' => 'es',
            ),
            35 => 
            array (
                'id' => 96,
                'parameter_option_id' => 37,
                'language_id' => 2,
                'name' => 'es',
            ),
            36 => 
            array (
                'id' => 97,
                'parameter_option_id' => 37,
                'language_id' => 3,
                'name' => 'es',
            ),
            37 => 
            array (
                'id' => 98,
                'parameter_option_id' => 38,
                'language_id' => 1,
                'name' => 'pt',
            ),
            38 => 
            array (
                'id' => 99,
                'parameter_option_id' => 38,
                'language_id' => 2,
                'name' => 'pt',
            ),
            39 => 
            array (
                'id' => 100,
                'parameter_option_id' => 38,
                'language_id' => 3,
                'name' => 'pt',
            ),
        ));
        
        
    }
}