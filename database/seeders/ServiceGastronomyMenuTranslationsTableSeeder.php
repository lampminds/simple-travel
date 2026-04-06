<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyMenuTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_menu_translations')->delete();
        
        \DB::table('cat_service_gastronomy_menu_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_gastronomy_menu_id' => 1,
                'language_id' => 2,
                'name' => 'A la carta',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'service_gastronomy_menu_id' => 2,
                'language_id' => 2,
                'name' => 'Menú fijo',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'service_gastronomy_menu_id' => 3,
                'language_id' => 2,
                'name' => 'Menú degustación',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'service_gastronomy_menu_id' => 4,
                'language_id' => 2,
                'name' => 'Menú del día',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'service_gastronomy_menu_id' => 5,
                'language_id' => 2,
                'name' => 'Menú para grupos',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'service_gastronomy_menu_id' => 6,
                'language_id' => 2,
                'name' => 'Menú de evento',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'service_gastronomy_menu_id' => 7,
                'language_id' => 2,
                'name' => 'Menú infantil',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'service_gastronomy_menu_id' => 8,
                'language_id' => 2,
                'name' => 'Carta de bebidas',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'service_gastronomy_menu_id' => 9,
                'language_id' => 2,
                'name' => 'Carta de vinos',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'service_gastronomy_menu_id' => 10,
                'language_id' => 2,
                'name' => 'Menú brunch',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'service_gastronomy_menu_id' => 11,
                'language_id' => 2,
                'name' => 'Desayuno',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'service_gastronomy_menu_id' => 12,
                'language_id' => 2,
                'name' => 'Almuerzo',
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'service_gastronomy_menu_id' => 13,
                'language_id' => 2,
                'name' => 'Cena',
                'description' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'service_gastronomy_menu_id' => 14,
                'language_id' => 2,
                'name' => 'Menú de temporada',
                'description' => NULL,
            ),
        ));
        
        
    }
}