<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatServiceTypeTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_type_translations')->delete();
        
        \DB::table('cat_service_type_translations')->insert(array (
            0 => 
            array (
                'id' => 13,
                'service_type_id' => 1,
                'language_id' => 1,
                'name' => 'Hotel',
                'description' => 'Hotels, hostels, cabins',
            ),
            1 => 
            array (
                'id' => 14,
                'service_type_id' => 1,
                'language_id' => 2,
                'name' => 'Hotel',
                'description' => 'Hotel, hostel, cabaña, apart',
            ),
            2 => 
            array (
                'id' => 15,
                'service_type_id' => 1,
                'language_id' => 3,
                'name' => 'Hotel',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 16,
                'service_type_id' => 2,
                'language_id' => 1,
                'name' => 'Transfer',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 17,
                'service_type_id' => 2,
                'language_id' => 2,
                'name' => 'Traslado',
                'description' => 'Traslados punto a punto',
            ),
            5 => 
            array (
                'id' => 18,
                'service_type_id' => 2,
                'language_id' => 3,
                'name' => 'Transfer',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 19,
                'service_type_id' => 4,
                'language_id' => 1,
                'name' => 'Gastronomy',
                'description' => 'Restaurant, meals, experiences',
            ),
            7 => 
            array (
                'id' => 20,
                'service_type_id' => 4,
                'language_id' => 2,
                'name' => 'Gastronomía',
                'description' => 'Restaurants, comidas, experiencias',
            ),
            8 => 
            array (
                'id' => 21,
                'service_type_id' => 4,
                'language_id' => 3,
                'name' => 'Gastronomia',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 22,
                'service_type_id' => 3,
                'language_id' => 1,
                'name' => 'Tour',
                'description' => 'Tours, excursions, experiences',
            ),
            10 => 
            array (
                'id' => 23,
                'service_type_id' => 3,
                'language_id' => 2,
                'name' => 'Entertainment',
                'description' => 'Tours, excursiones, experiencias',
            ),
            11 => 
            array (
                'id' => 24,
                'service_type_id' => 3,
                'language_id' => 3,
                'name' => 'Tour',
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 25,
                'service_type_id' => 5,
                'language_id' => 1,
                'name' => 'Transport',
                'description' => 'Tickets: flights, bus, train',
            ),
            13 => 
            array (
                'id' => 26,
                'service_type_id' => 5,
                'language_id' => 2,
                'name' => 'Transportes',
                'description' => 'Tickets: vuelos, micros, trenes',
            ),
            14 => 
            array (
                'id' => 27,
                'service_type_id' => 6,
                'language_id' => 1,
                'name' => 'Rental',
                'description' => 'Car rental, equipment',
            ),
            15 => 
            array (
                'id' => 28,
                'service_type_id' => 6,
                'language_id' => 2,
                'name' => 'Rental',
                'description' => 'Autos de alquiler, equipamiento',
            ),
            16 => 
            array (
                'id' => 29,
                'service_type_id' => 6,
                'language_id' => 3,
                'name' => 'Rental',
                'description' => NULL,
            ),
            17 => 
            array (
                'id' => 30,
                'service_type_id' => 7,
                'language_id' => 1,
                'name' => 'Event',
                'description' => 'Shows, tickets',
            ),
            18 => 
            array (
                'id' => 31,
                'service_type_id' => 7,
                'language_id' => 2,
                'name' => 'Eventos',
                'description' => 'Shows, tickets locales',
            ),
            19 => 
            array (
                'id' => 32,
                'service_type_id' => 7,
                'language_id' => 3,
                'name' => 'Eventos',
                'description' => NULL,
            ),
            20 => 
            array (
                'id' => 33,
                'service_type_id' => 8,
                'language_id' => 1,
                'name' => 'Packages',
                'description' => 'Bundles / combos',
            ),
            21 => 
            array (
                'id' => 34,
                'service_type_id' => 8,
                'language_id' => 2,
                'name' => 'Paquetes',
                'description' => 'Bundles / combos',
            ),
            22 => 
            array (
                'id' => 35,
                'service_type_id' => 8,
                'language_id' => 3,
                'name' => 'Paquetes',
                'description' => 'Bundles / paquetes',
            ),
            23 => 
            array (
                'id' => 36,
                'service_type_id' => 9,
                'language_id' => 1,
                'name' => 'Other',
                'description' => 'Others',
            ),
            24 => 
            array (
                'id' => 37,
                'service_type_id' => 9,
                'language_id' => 2,
                'name' => 'Otros',
                'description' => 'Otros',
            ),
            25 => 
            array (
                'id' => 38,
                'service_type_id' => 9,
                'language_id' => 3,
                'name' => 'Otros',
                'description' => NULL,
            ),
        ));
        
        
    }
}