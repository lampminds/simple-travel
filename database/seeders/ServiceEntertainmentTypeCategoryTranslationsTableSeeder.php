<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceEntertainmentTypeCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_entertainment_type_category_translations')->delete();
        
        \DB::table('cat_service_entertainment_type_category_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_entertainment_type_category_id' => 1,
                'language_id' => 2,
                'name' => 'Tours generales',
            ),
            1 => 
            array (
                'id' => 2,
                'service_entertainment_type_category_id' => 2,
                'language_id' => 2,
                'name' => 'Cultura e Historia',
            ),
            2 => 
            array (
                'id' => 3,
                'service_entertainment_type_category_id' => 3,
                'language_id' => 2,
                'name' => 'Naturaleza',
            ),
            3 => 
            array (
                'id' => 4,
                'service_entertainment_type_category_id' => 4,
                'language_id' => 2,
                'name' => 'Aventura',
            ),
            4 => 
            array (
                'id' => 5,
                'service_entertainment_type_category_id' => 5,
                'language_id' => 2,
                'name' => 'Actividades acuÃ¡ticas',
            ),
            5 => 
            array (
                'id' => 6,
                'service_entertainment_type_category_id' => 6,
                'language_id' => 2,
                'name' => 'GastronomÃ­a',
            ),
            6 => 
            array (
                'id' => 7,
                'service_entertainment_type_category_id' => 7,
                'language_id' => 2,
                'name' => 'Nieve',
            ),
            7 => 
            array (
                'id' => 8,
                'service_entertainment_type_category_id' => 8,
                'language_id' => 2,
                'name' => 'Experiencias aÃ©reas',
            ),
            8 => 
            array (
                'id' => 9,
                'service_entertainment_type_category_id' => 9,
                'language_id' => 2,
                'name' => 'Transporte turÃ­stico',
            ),
            9 => 
            array (
                'id' => 10,
                'service_entertainment_type_category_id' => 10,
                'language_id' => 2,
                'name' => 'Turismo rural',
            ),
            10 => 
            array (
                'id' => 11,
                'service_entertainment_type_category_id' => 11,
                'language_id' => 2,
                'name' => 'Experiencias especiales',
            ),
        ));
        
        
    }
}
