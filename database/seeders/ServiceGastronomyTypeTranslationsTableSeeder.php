<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyTypeTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_type_translations')->delete();
        
        \DB::table('cat_service_gastronomy_type_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_gastronomy_type_id' => 1,
                'language_id' => 2,
                'name' => 'Reserva en restaurante',
                'description' => 'Reserva de mesa sin menú predefinido',
            ),
            1 => 
            array (
                'id' => 2,
                'service_gastronomy_type_id' => 2,
                'language_id' => 2,
                'name' => 'Menú fijo',
            'description' => 'Experiencia con menú cerrado (ej: cena 3 pasos)',
            ),
            2 => 
            array (
                'id' => 3,
                'service_gastronomy_type_id' => 3,
                'language_id' => 2,
                'name' => 'Menú degustación',
                'description' => 'Secuencia de platos seleccionados',
            ),
            3 => 
            array (
                'id' => 4,
                'service_gastronomy_type_id' => 4,
                'language_id' => 2,
                'name' => 'Experiencia gastronómica',
            'description' => 'Evento estructurado (cena show, etc.)',
            ),
            4 => 
            array (
                'id' => 5,
                'service_gastronomy_type_id' => 5,
                'language_id' => 2,
                'name' => 'Clase de cocina',
                'description' => 'Actividad participativa',
            ),
            5 => 
            array (
                'id' => 6,
                'service_gastronomy_type_id' => 6,
                'language_id' => 2,
                'name' => 'Degustación',
            'description' => 'Experiencia de prueba (vino, cerveza, etc.)',
            ),
            6 => 
            array (
                'id' => 7,
                'service_gastronomy_type_id' => 7,
                'language_id' => 2,
                'name' => 'Experiencia privada',
                'description' => 'Evento exclusivo para grupo',
            ),
            7 => 
            array (
                'id' => 8,
                'service_gastronomy_type_id' => 8,
                'language_id' => 2,
                'name' => 'Evento gastronómico',
            'description' => 'Evento puntual (feria, festival, etc.)',
            ),
        ));
        
        
    }
}