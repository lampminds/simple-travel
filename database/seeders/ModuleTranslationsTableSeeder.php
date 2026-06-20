<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('module_translations')->delete();
        
        \DB::table('module_translations')->insert(array (
            0 => 
            array (
                'id' => 6,
                'module_id' => 6,
                'language_id' => 2,
                'name' => 'Automatizaciones',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 7,
                'module_id' => 7,
                'language_id' => 2,
                'name' => 'B2B/Agencias',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 10,
                'module_id' => 10,
                'language_id' => 2,
                'name' => 'Reportes avanzados',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 11,
                'module_id' => 11,
                'language_id' => 2,
                'name' => 'Integraciones',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 12,
                'module_id' => 12,
                'language_id' => 2,
                'name' => 'API',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 16,
                'module_id' => 2,
                'language_id' => 2,
                'name' => 'Sitio web',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 17,
                'module_id' => 3,
                'language_id' => 2,
                'name' => 'Motor de reservas',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 18,
                'module_id' => 4,
                'language_id' => 2,
                'name' => 'Manejo de clientes',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 19,
                'module_id' => 5,
                'language_id' => 2,
                'name' => 'Finanzas',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 20,
                'module_id' => 1,
                'language_id' => 2,
                'name' => 'Operación básica',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 21,
                'module_id' => 8,
                'language_id' => 2,
                'name' => 'Facturación',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 22,
                'module_id' => 9,
                'language_id' => 2,
            'name' => 'Globalización (asistencia con IA)',
                'description' => NULL,
            ),
        ));
        
        
    }
}