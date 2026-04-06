<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceGastronomyMenuCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_gastronomy_menu_category_translations')->delete();
        
        \DB::table('cat_service_gastronomy_menu_category_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_gastronomy_menu_category_id' => 1,
                'language_id' => 2,
                'name' => 'Aperitivos',
            ),
            1 => 
            array (
                'id' => 2,
                'service_gastronomy_menu_category_id' => 2,
                'language_id' => 2,
                'name' => 'Sopas',
            ),
            2 => 
            array (
                'id' => 3,
                'service_gastronomy_menu_category_id' => 3,
                'language_id' => 2,
                'name' => 'Ensaladas',
            ),
            3 => 
            array (
                'id' => 4,
                'service_gastronomy_menu_category_id' => 4,
                'language_id' => 2,
                'name' => 'Pastas',
            ),
            4 => 
            array (
                'id' => 5,
                'service_gastronomy_menu_category_id' => 5,
                'language_id' => 2,
                'name' => 'Arroces',
            ),
            5 => 
            array (
                'id' => 6,
                'service_gastronomy_menu_category_id' => 6,
                'language_id' => 2,
                'name' => 'Carnes',
            ),
            6 => 
            array (
                'id' => 7,
                'service_gastronomy_menu_category_id' => 7,
                'language_id' => 2,
                'name' => 'Mariscos',
            ),
            7 => 
            array (
                'id' => 8,
                'service_gastronomy_menu_category_id' => 8,
                'language_id' => 2,
                'name' => 'Vegetarianos',
            ),
            8 => 
            array (
                'id' => 9,
                'service_gastronomy_menu_category_id' => 9,
                'language_id' => 2,
                'name' => 'Veganos',
            ),
            9 => 
            array (
                'id' => 10,
                'service_gastronomy_menu_category_id' => 10,
                'language_id' => 2,
                'name' => 'Guarniciones',
            ),
            10 => 
            array (
                'id' => 11,
                'service_gastronomy_menu_category_id' => 11,
                'language_id' => 2,
                'name' => 'Snacks',
            ),
            11 => 
            array (
                'id' => 12,
                'service_gastronomy_menu_category_id' => 12,
                'language_id' => 2,
                'name' => 'Sándwiches',
            ),
            12 => 
            array (
                'id' => 13,
                'service_gastronomy_menu_category_id' => 13,
                'language_id' => 2,
                'name' => 'Pizza',
            ),
            13 => 
            array (
                'id' => 14,
                'service_gastronomy_menu_category_id' => 14,
                'language_id' => 2,
                'name' => 'Parrilla',
            ),
            14 => 
            array (
                'id' => 15,
                'service_gastronomy_menu_category_id' => 15,
                'language_id' => 2,
                'name' => 'Panadería',
            ),
            15 => 
            array (
                'id' => 16,
                'service_gastronomy_menu_category_id' => 16,
                'language_id' => 2,
                'name' => 'Pastelería',
            ),
            16 => 
            array (
                'id' => 17,
                'service_gastronomy_menu_category_id' => 17,
                'language_id' => 2,
                'name' => 'Desayuno',
            ),
            17 => 
            array (
                'id' => 18,
                'service_gastronomy_menu_category_id' => 18,
                'language_id' => 2,
                'name' => 'Brunch',
            ),
            18 => 
            array (
                'id' => 19,
                'service_gastronomy_menu_category_id' => 19,
                'language_id' => 2,
                'name' => 'Infantil',
            ),
            19 => 
            array (
                'id' => 20,
                'service_gastronomy_menu_category_id' => 20,
                'language_id' => 2,
                'name' => 'Pasos degustación',
            ),
            20 => 
            array (
                'id' => 21,
                'service_gastronomy_menu_category_id' => 21,
                'language_id' => 2,
                'name' => 'Bebidas alcohólicas',
            ),
            21 => 
            array (
                'id' => 22,
                'service_gastronomy_menu_category_id' => 22,
                'language_id' => 2,
                'name' => 'Bebidas sin alcohol',
            ),
            22 => 
            array (
                'id' => 23,
                'service_gastronomy_menu_category_id' => 23,
                'language_id' => 2,
                'name' => 'Vinos',
            ),
            23 => 
            array (
                'id' => 24,
                'service_gastronomy_menu_category_id' => 24,
                'language_id' => 2,
                'name' => 'Cervezas',
            ),
            24 => 
            array (
                'id' => 25,
                'service_gastronomy_menu_category_id' => 25,
                'language_id' => 2,
                'name' => 'Cócteles',
            ),
            25 => 
            array (
                'id' => 26,
                'service_gastronomy_menu_category_id' => 26,
                'language_id' => 2,
                'name' => 'Bebidas calientes',
            ),
            26 => 
            array (
                'id' => 27,
                'service_gastronomy_menu_category_id' => 27,
                'language_id' => 2,
                'name' => 'Bebidas frías',
            ),
        ));
        
        
    }
}