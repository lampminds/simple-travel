<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyCuisineTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_cuisine_translations')->delete();
        
        \DB::table('cat_service_gastronomy_cuisine_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_gastronomy_cuisine_id' => 1,
                'language_id' => 2,
                'name' => 'Argentina',
            ),
            1 => 
            array (
                'id' => 2,
                'service_gastronomy_cuisine_id' => 2,
                'language_id' => 2,
                'name' => 'Patagónica',
            ),
            2 => 
            array (
                'id' => 3,
                'service_gastronomy_cuisine_id' => 3,
                'language_id' => 2,
                'name' => 'Latinoamericana',
            ),
            3 => 
            array (
                'id' => 4,
                'service_gastronomy_cuisine_id' => 4,
                'language_id' => 2,
                'name' => 'Mediterránea',
            ),
            4 => 
            array (
                'id' => 5,
                'service_gastronomy_cuisine_id' => 5,
                'language_id' => 2,
                'name' => 'Italiana',
            ),
            5 => 
            array (
                'id' => 6,
                'service_gastronomy_cuisine_id' => 6,
                'language_id' => 2,
                'name' => 'Española',
            ),
            6 => 
            array (
                'id' => 7,
                'service_gastronomy_cuisine_id' => 7,
                'language_id' => 2,
                'name' => 'Francesa',
            ),
            7 => 
            array (
                'id' => 8,
                'service_gastronomy_cuisine_id' => 8,
                'language_id' => 2,
                'name' => 'Estadounidense',
            ),
            8 => 
            array (
                'id' => 9,
                'service_gastronomy_cuisine_id' => 9,
                'language_id' => 2,
                'name' => 'Mexicana',
            ),
            9 => 
            array (
                'id' => 10,
                'service_gastronomy_cuisine_id' => 10,
                'language_id' => 2,
                'name' => 'Peruana',
            ),
            10 => 
            array (
                'id' => 11,
                'service_gastronomy_cuisine_id' => 11,
                'language_id' => 2,
                'name' => 'Brasileña',
            ),
            11 => 
            array (
                'id' => 12,
                'service_gastronomy_cuisine_id' => 12,
                'language_id' => 2,
                'name' => 'Asiática',
            ),
            12 => 
            array (
                'id' => 13,
                'service_gastronomy_cuisine_id' => 13,
                'language_id' => 2,
                'name' => 'Japonesa',
            ),
            13 => 
            array (
                'id' => 14,
                'service_gastronomy_cuisine_id' => 14,
                'language_id' => 2,
                'name' => 'China',
            ),
            14 => 
            array (
                'id' => 15,
                'service_gastronomy_cuisine_id' => 15,
                'language_id' => 2,
                'name' => 'Tailandesa',
            ),
            15 => 
            array (
                'id' => 16,
                'service_gastronomy_cuisine_id' => 16,
                'language_id' => 2,
                'name' => 'India',
            ),
            16 => 
            array (
                'id' => 17,
                'service_gastronomy_cuisine_id' => 17,
                'language_id' => 2,
                'name' => 'Medio Oriente',
            ),
            17 => 
            array (
                'id' => 18,
                'service_gastronomy_cuisine_id' => 18,
                'language_id' => 2,
                'name' => 'Internacional',
            ),
            18 => 
            array (
                'id' => 19,
                'service_gastronomy_cuisine_id' => 19,
                'language_id' => 2,
                'name' => 'Fusión',
            ),
            19 => 
            array (
                'id' => 20,
                'service_gastronomy_cuisine_id' => 20,
                'language_id' => 2,
                'name' => 'Parrilla',
            ),
            20 => 
            array (
                'id' => 21,
                'service_gastronomy_cuisine_id' => 21,
                'language_id' => 2,
                'name' => 'Barbacoa',
            ),
            21 => 
            array (
                'id' => 22,
                'service_gastronomy_cuisine_id' => 22,
                'language_id' => 2,
                'name' => 'Steakhouse',
            ),
            22 => 
            array (
                'id' => 23,
                'service_gastronomy_cuisine_id' => 23,
                'language_id' => 2,
                'name' => 'Mariscos',
            ),
            23 => 
            array (
                'id' => 24,
                'service_gastronomy_cuisine_id' => 24,
                'language_id' => 2,
                'name' => 'Pizza',
            ),
            24 => 
            array (
                'id' => 25,
                'service_gastronomy_cuisine_id' => 25,
                'language_id' => 2,
                'name' => 'Hamburguesas',
            ),
            25 => 
            array (
                'id' => 26,
                'service_gastronomy_cuisine_id' => 26,
                'language_id' => 2,
                'name' => 'Sushi',
            ),
            26 => 
            array (
                'id' => 27,
                'service_gastronomy_cuisine_id' => 27,
                'language_id' => 2,
                'name' => 'Tapas',
            ),
            27 => 
            array (
                'id' => 28,
                'service_gastronomy_cuisine_id' => 28,
                'language_id' => 2,
                'name' => 'Sándwiches',
            ),
            28 => 
            array (
                'id' => 29,
                'service_gastronomy_cuisine_id' => 29,
                'language_id' => 2,
                'name' => 'Comida rápida',
            ),
            29 => 
            array (
                'id' => 30,
                'service_gastronomy_cuisine_id' => 30,
                'language_id' => 2,
                'name' => 'Comida callejera',
            ),
            30 => 
            array (
                'id' => 31,
                'service_gastronomy_cuisine_id' => 31,
                'language_id' => 2,
                'name' => 'Vegetariana',
            ),
            31 => 
            array (
                'id' => 32,
                'service_gastronomy_cuisine_id' => 32,
                'language_id' => 2,
                'name' => 'Vegana',
            ),
            32 => 
            array (
                'id' => 33,
                'service_gastronomy_cuisine_id' => 33,
                'language_id' => 2,
                'name' => 'Sin gluten',
            ),
            33 => 
            array (
                'id' => 34,
                'service_gastronomy_cuisine_id' => 34,
                'language_id' => 2,
                'name' => 'Saludable',
            ),
        ));
        
        
    }
}