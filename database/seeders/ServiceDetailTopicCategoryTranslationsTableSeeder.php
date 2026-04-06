<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceDetailTopicCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_detail_topic_category_translations')->delete();
        
        \DB::table('cat_service_detail_topic_category_translations')->insert(array (
            0 => 
            array (
                'id' => 3,
                'service_detail_topic_category_id' => 1,
                'language_id' => 2,
                'name' => 'Información general del servicio',
                'description' => 'Información descriptiva básica',
            ),
            1 => 
            array (
                'id' => 5,
                'service_detail_topic_category_id' => 2,
                'language_id' => 2,
                'name' => 'Políticas comerciales',
                'description' => 'Condiciones de venta, pago, temas impositivos',
            ),
            2 => 
            array (
                'id' => 6,
                'service_detail_topic_category_id' => 3,
                'language_id' => 2,
                'name' => 'Cancelaciones y modificaciones',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 7,
                'service_detail_topic_category_id' => 4,
                'language_id' => 2,
                'name' => 'Operación del servicio',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 8,
                'service_detail_topic_category_id' => 5,
                'language_id' => 2,
                'name' => 'Reglas para pasajeros',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 9,
                'service_detail_topic_category_id' => 6,
                'language_id' => 2,
                'name' => 'Requisitos del pasajero',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 10,
                'service_detail_topic_category_id' => 7,
                'language_id' => 2,
                'name' => 'Logística y preparación',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 11,
                'service_detail_topic_category_id' => 8,
                'language_id' => 2,
                'name' => 'Transporte',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 12,
                'service_detail_topic_category_id' => 9,
                'language_id' => 2,
                'name' => 'Información específica de alojamiento',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 13,
                'service_detail_topic_category_id' => 10,
                'language_id' => 2,
                'name' => 'Información legal / responsabilidad',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 14,
                'service_detail_topic_category_id' => 11,
                'language_id' => 2,
                'name' => 'Contacto y asistencia',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 15,
                'service_detail_topic_category_id' => 12,
                'language_id' => 2,
                'name' => 'Información ambiental o local',
                'description' => NULL,
            ),
        ));
        
        
    }
}