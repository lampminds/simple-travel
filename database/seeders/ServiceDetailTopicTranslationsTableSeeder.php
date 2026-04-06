<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceDetailTopicTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_detail_topic_translations')->delete();
        
        \DB::table('cat_service_detail_topic_translations')->insert(array (
            0 => 
            array (
                'id' => 3,
                'service_detail_topic_id' => 1,
                'language_id' => 2,
                'name' => 'Descripción general del servicio',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 4,
                'service_detail_topic_id' => 2,
                'language_id' => 2,
                'name' => 'Puntos destacados',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 5,
                'service_detail_topic_id' => 3,
                'language_id' => 2,
                'name' => 'Qué incluye el servicio',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 6,
                'service_detail_topic_id' => 4,
                'language_id' => 2,
                'name' => 'Qué no incluye el servicio',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 7,
                'service_detail_topic_id' => 5,
                'language_id' => 2,
                'name' => 'Recomendaciones',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 8,
                'service_detail_topic_id' => 6,
                'language_id' => 2,
                'name' => 'Información importante',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 9,
                'service_detail_topic_id' => 7,
                'language_id' => 2,
                'name' => 'Accessibilidad',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 10,
                'service_detail_topic_id' => 8,
                'language_id' => 2,
                'name' => 'Condiciones de venta',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 11,
                'service_detail_topic_id' => 9,
                'language_id' => 2,
                'name' => 'Condiciones de pago',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 14,
                'service_detail_topic_id' => 10,
                'language_id' => 2,
                'name' => 'Política de depósito',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 15,
                'service_detail_topic_id' => 11,
                'language_id' => 2,
                'name' => 'Condiciones de tarifa',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 16,
                'service_detail_topic_id' => 13,
                'language_id' => 2,
                'name' => 'Política de cancelación',
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 17,
                'service_detail_topic_id' => 14,
                'language_id' => 2,
                'name' => 'Política de cambios',
                'description' => NULL,
            ),
            13 => 
            array (
                'id' => 18,
                'service_detail_topic_id' => 15,
                'language_id' => 2,
                'name' => 'Política de no-show',
                'description' => NULL,
            ),
            14 => 
            array (
                'id' => 19,
                'service_detail_topic_id' => 16,
                'language_id' => 2,
                'name' => 'Política de reembolsos',
                'description' => NULL,
            ),
            15 => 
            array (
                'id' => 20,
                'service_detail_topic_id' => 17,
                'language_id' => 2,
                'name' => 'Horarios de operación',
                'description' => NULL,
            ),
            16 => 
            array (
                'id' => 21,
                'service_detail_topic_id' => 18,
                'language_id' => 2,
                'name' => 'Duración del servicio',
                'description' => NULL,
            ),
            17 => 
            array (
                'id' => 22,
                'service_detail_topic_id' => 19,
                'language_id' => 2,
                'name' => 'Período de operación',
                'description' => NULL,
            ),
            18 => 
            array (
                'id' => 23,
                'service_detail_topic_id' => 20,
                'language_id' => 2,
                'name' => 'Temporada',
                'description' => NULL,
            ),
            19 => 
            array (
                'id' => 24,
                'service_detail_topic_id' => 21,
                'language_id' => 2,
                'name' => 'Punto de encuentro',
                'description' => NULL,
            ),
            20 => 
            array (
                'id' => 25,
                'service_detail_topic_id' => 22,
                'language_id' => 2,
                'name' => 'Información de pickup',
                'description' => NULL,
            ),
            21 => 
            array (
                'id' => 26,
                'service_detail_topic_id' => 23,
                'language_id' => 2,
                'name' => 'Información de dropoff',
                'description' => NULL,
            ),
            22 => 
            array (
                'id' => 27,
                'service_detail_topic_id' => 24,
                'language_id' => 2,
                'name' => 'Política para niños',
                'description' => NULL,
            ),
            23 => 
            array (
                'id' => 28,
                'service_detail_topic_id' => 25,
                'language_id' => 2,
                'name' => 'política para bebés',
                'description' => NULL,
            ),
            24 => 
            array (
                'id' => 29,
                'service_detail_topic_id' => 26,
                'language_id' => 2,
                'name' => 'Política para estudiantes',
                'description' => NULL,
            ),
            25 => 
            array (
                'id' => 30,
                'service_detail_topic_id' => 27,
                'language_id' => 2,
                'name' => 'Política para mayores',
                'description' => NULL,
            ),
            26 => 
            array (
                'id' => 31,
                'service_detail_topic_id' => 28,
                'language_id' => 2,
                'name' => 'Política de grupos',
                'description' => NULL,
            ),
            27 => 
            array (
                'id' => 32,
                'service_detail_topic_id' => 29,
                'language_id' => 2,
                'name' => 'Edad mínima',
                'description' => NULL,
            ),
            28 => 
            array (
                'id' => 33,
                'service_detail_topic_id' => 30,
                'language_id' => 2,
                'name' => 'Edad máxima',
                'description' => NULL,
            ),
            29 => 
            array (
                'id' => 34,
                'service_detail_topic_id' => 31,
                'language_id' => 2,
                'name' => 'Requisitos físicos',
                'description' => NULL,
            ),
            30 => 
            array (
                'id' => 35,
                'service_detail_topic_id' => 32,
                'language_id' => 2,
                'name' => 'Requisitos de salud',
                'description' => NULL,
            ),
            31 => 
            array (
                'id' => 36,
                'service_detail_topic_id' => 33,
                'language_id' => 2,
                'name' => 'Equipamiento requerido',
                'description' => NULL,
            ),
            32 => 
            array (
                'id' => 37,
                'service_detail_topic_id' => 34,
                'language_id' => 2,
                'name' => 'Documentación requerida',
                'description' => NULL,
            ),
            33 => 
            array (
                'id' => 38,
                'service_detail_topic_id' => 35,
                'language_id' => 2,
                'name' => 'Qué llevar',
                'description' => NULL,
            ),
            34 => 
            array (
                'id' => 39,
                'service_detail_topic_id' => 36,
                'language_id' => 2,
                'name' => 'Qué vestir',
                'description' => NULL,
            ),
            35 => 
            array (
                'id' => 40,
                'service_detail_topic_id' => 37,
                'language_id' => 2,
                'name' => 'Condiciones climáticas',
                'description' => NULL,
            ),
            36 => 
            array (
                'id' => 41,
                'service_detail_topic_id' => 38,
                'language_id' => 2,
                'name' => 'Información de seguridad',
                'description' => NULL,
            ),
            37 => 
            array (
                'id' => 42,
                'service_detail_topic_id' => 39,
                'language_id' => 2,
                'name' => 'Transporte incluido',
                'description' => NULL,
            ),
            38 => 
            array (
                'id' => 43,
                'service_detail_topic_id' => 40,
                'language_id' => 2,
                'name' => 'Información del vehículo',
                'description' => NULL,
            ),
            39 => 
            array (
                'id' => 44,
                'service_detail_topic_id' => 41,
                'language_id' => 2,
                'name' => 'Area de pickup',
                'description' => NULL,
            ),
            40 => 
            array (
                'id' => 45,
                'service_detail_topic_id' => 42,
                'language_id' => 2,
                'name' => 'Condiciones del traslado',
                'description' => NULL,
            ),
            41 => 
            array (
                'id' => 46,
                'service_detail_topic_id' => 43,
                'language_id' => 2,
                'name' => 'Horario de check-in',
                'description' => NULL,
            ),
            42 => 
            array (
                'id' => 47,
                'service_detail_topic_id' => 44,
                'language_id' => 2,
                'name' => 'Horario de check-out',
                'description' => NULL,
            ),
            43 => 
            array (
                'id' => 48,
                'service_detail_topic_id' => 45,
                'language_id' => 2,
                'name' => 'Política de late checkout',
                'description' => NULL,
            ),
            44 => 
            array (
                'id' => 49,
                'service_detail_topic_id' => 46,
                'language_id' => 2,
                'name' => 'Política de early check-in',
                'description' => NULL,
            ),
            45 => 
            array (
                'id' => 50,
                'service_detail_topic_id' => 47,
                'language_id' => 2,
                'name' => 'Limpieza de habitaciones',
                'description' => NULL,
            ),
            46 => 
            array (
                'id' => 51,
                'service_detail_topic_id' => 48,
                'language_id' => 2,
                'name' => 'Planes de comida',
                'description' => NULL,
            ),
            47 => 
            array (
                'id' => 52,
                'service_detail_topic_id' => 49,
                'language_id' => 2,
                'name' => 'Política de mascotas',
                'description' => NULL,
            ),
            48 => 
            array (
                'id' => 53,
                'service_detail_topic_id' => 50,
                'language_id' => 2,
                'name' => 'Responsabilidad',
                'description' => NULL,
            ),
            49 => 
            array (
                'id' => 54,
                'service_detail_topic_id' => 51,
                'language_id' => 2,
                'name' => 'Seguros',
                'description' => NULL,
            ),
            50 => 
            array (
                'id' => 55,
                'service_detail_topic_id' => 52,
                'language_id' => 2,
                'name' => 'Términos y condiciones',
                'description' => NULL,
            ),
            51 => 
            array (
                'id' => 56,
                'service_detail_topic_id' => 53,
                'language_id' => 2,
                'name' => 'Contacto de emergencia',
                'description' => NULL,
            ),
            52 => 
            array (
                'id' => 57,
                'service_detail_topic_id' => 54,
                'language_id' => 2,
                'name' => 'Contacto del operador',
                'description' => NULL,
            ),
            53 => 
            array (
                'id' => 58,
                'service_detail_topic_id' => 55,
                'language_id' => 2,
                'name' => 'Soporte',
                'description' => NULL,
            ),
            54 => 
            array (
                'id' => 59,
                'service_detail_topic_id' => 56,
                'language_id' => 2,
                'name' => 'Política ambiental',
                'description' => NULL,
            ),
            55 => 
            array (
                'id' => 60,
                'service_detail_topic_id' => 57,
                'language_id' => 2,
                'name' => 'Regulaciones locales',
                'description' => NULL,
            ),
            56 => 
            array (
                'id' => 61,
                'service_detail_topic_id' => 58,
                'language_id' => 2,
                'name' => 'Reglas del parque',
                'description' => NULL,
            ),
        ));
        
        
    }
}