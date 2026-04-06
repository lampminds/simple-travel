<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceActivityCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_activity_category_translations')->delete();
        
        \DB::table('cat_service_activity_category_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_activity_category_id' => 1,
                'language_id' => 2,
                'name' => 'Naturaleza',
            ),
            1 => 
            array (
                'id' => 2,
                'service_activity_category_id' => 2,
                'language_id' => 2,
                'name' => 'Actividades acuáticas',
            ),
            2 => 
            array (
                'id' => 3,
                'service_activity_category_id' => 3,
                'language_id' => 2,
                'name' => 'Actividades de montaña',
            ),
            3 => 
            array (
                'id' => 4,
                'service_activity_category_id' => 4,
                'language_id' => 2,
                'name' => 'Actividades de nieve',
            ),
            4 => 
            array (
                'id' => 5,
                'service_activity_category_id' => 5,
                'language_id' => 2,
                'name' => 'Actividades ecuestres',
            ),
            5 => 
            array (
                'id' => 6,
                'service_activity_category_id' => 6,
                'language_id' => 2,
                'name' => 'Actividades culturales',
            ),
            6 => 
            array (
                'id' => 7,
                'service_activity_category_id' => 7,
                'language_id' => 2,
                'name' => 'Gastronomía',
            ),
            7 => 
            array (
                'id' => 8,
                'service_activity_category_id' => 8,
                'language_id' => 2,
                'name' => 'Bienestar',
            ),
            8 => 
            array (
                'id' => 9,
                'service_activity_category_id' => 9,
                'language_id' => 2,
                'name' => 'Actividades recreativas',
            ),
            9 => 
            array (
                'id' => 10,
                'service_activity_category_id' => 10,
                'language_id' => 2,
                'name' => 'Actividades familiares',
            ),
            10 => 
            array (
                'id' => 11,
                'service_activity_category_id' => 11,
                'language_id' => 2,
                'name' => 'Experiencias rurales',
            ),
            11 => 
            array (
                'id' => 12,
                'service_activity_category_id' => 12,
                'language_id' => 2,
                'name' => 'Transporte recreativo',
            ),
            12 => 
            array (
                'id' => 13,
                'service_activity_category_id' => 13,
                'language_id' => 2,
                'name' => 'Aventura',
            ),
            13 => 
            array (
                'id' => 14,
                'service_activity_category_id' => 14,
                'language_id' => 2,
                'name' => 'Educativas',
            ),
            14 => 
            array (
                'id' => 15,
                'service_activity_category_id' => 15,
                'language_id' => 2,
                'name' => 'Observación',
            ),
            15 => 
            array (
                'id' => 16,
                'service_activity_category_id' => 16,
                'language_id' => 2,
                'name' => 'Relax',
            ),
        ));
        
        
    }
}