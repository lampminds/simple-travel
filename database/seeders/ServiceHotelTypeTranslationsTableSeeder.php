<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceHotelTypeTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_hotel_type_translations')->delete();
        
        \DB::table('cat_service_hotel_type_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_hotel_type_id' => 1,
                'language_id' => 2,
                'name' => 'Hotel boutique',
                'description' => 'Establecimientos de diseño único, con atención personalizada y ambiente íntimo.',
            ),
            1 => 
            array (
                'id' => 2,
                'service_hotel_type_id' => 2,
                'language_id' => 2,
                'name' => 'Hotel Temático',
                'description' => 'Centrado en una temática específica para ofrecer una experiencia inmersiva.',
            ),
            2 => 
            array (
                'id' => 3,
                'service_hotel_type_id' => 3,
                'language_id' => 2,
                'name' => 'Hotel Ecológico',
                'description' => 'Diseñado con prácticas sostenibles y amigables con el medio ambiente.',
            ),
            3 => 
            array (
                'id' => 4,
                'service_hotel_type_id' => 4,
                'language_id' => 2,
                'name' => 'Hotel cápsula',
                'description' => 'Originarios de Japón, ofrecen cabinas pequeñas y funcionales para estancias cortas.',
            ),
            4 => 
            array (
                'id' => 5,
                'service_hotel_type_id' => 5,
                'language_id' => 2,
                'name' => 'Hotel burbuja',
                'description' => 'Habitaciones transparentes o semitransparentes pensadas para el turismo astronómico o en la naturaleza.',
            ),
            5 => 
            array (
                'id' => 6,
                'service_hotel_type_id' => 6,
                'language_id' => 2,
                'name' => 'Hotel urbano',
                'description' => 'Situado en el centro de ciudades o cerca de puntos turísticos, ideal para negocios o turismo urbano.',
            ),
            6 => 
            array (
                'id' => 7,
                'service_hotel_type_id' => 7,
                'language_id' => 2,
            'name' => 'Resort (Complejo Turístico)',
            'description' => ' Ubicado en zonas vacacionales (playa, montaña), ofrece alojamiento, comida y entretenimiento en un mismo lugar.',
            ),
            7 => 
            array (
                'id' => 8,
                'service_hotel_type_id' => 8,
                'language_id' => 2,
                'name' => 'Hotel de aeropuerto',
                'description' => 'Orientado a viajeros en tránsito o estancias muy cortas.',
            ),
            8 => 
            array (
                'id' => 9,
                'service_hotel_type_id' => 9,
                'language_id' => 2,
                'name' => 'Hotel de naturaleza o rústico',
                'description' => 'Situados en entornos rurales o naturales.',
            ),
            9 => 
            array (
                'id' => 10,
                'service_hotel_type_id' => 10,
                'language_id' => 2,
                'name' => 'Hotel de negocios',
                'description' => 'Diseñado para ejecutivos, con salas de reuniones, Wi-Fi y cerca de centros financieros.',
            ),
            10 => 
            array (
                'id' => 11,
                'service_hotel_type_id' => 11,
                'language_id' => 2,
            'name' => 'Hotel de lujo (5 estrellas)',
                'description' => 'Ofrece servicios premium, alta gastronomía y comodidades de clase mundial.',
            ),
            11 => 
            array (
                'id' => 12,
                'service_hotel_type_id' => 12,
                'language_id' => 2,
                'name' => 'Motel',
                'description' => 'Alojamientos sencillos, generalmente a pie de carretera, con aparcamiento próximo a las habitaciones.',
            ),
            12 => 
            array (
                'id' => 13,
                'service_hotel_type_id' => 13,
                'language_id' => 2,
                'name' => 'Apart Hotel',
                'description' => 'Apartamentos con servicios hoteleros, ideales para estancias prolongadas.',
            ),
            13 => 
            array (
                'id' => 14,
                'service_hotel_type_id' => 14,
                'language_id' => 2,
            'name' => 'All-Inclusive (Todo Incluido)',
                'description' => 'La tarifa cubre alojamiento, comidas, bebidas y actividades.',
            ),
        ));
        
        
    }
}