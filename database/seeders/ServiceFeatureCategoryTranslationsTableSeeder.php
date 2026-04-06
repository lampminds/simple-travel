<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceFeatureCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cat_service_feature_category_translations')->delete();

        \DB::table('cat_service_feature_category_translations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'service_feature_category_id' => 1,
                'language_id' => 2,
                'name' => 'Comida',
            ),
            1 =>
            array (
                'id' => 2,
                'service_feature_category_id' => 2,
                'language_id' => 2,
                'name' => 'Bebidas',
            ),
            2 =>
            array (
                'id' => 3,
                'service_feature_category_id' => 3,
                'language_id' => 2,
                'name' => 'Dietas',
            ),
            3 =>
            array (
                'id' => 4,
                'service_feature_category_id' => 4,
                'language_id' => 2,
                'name' => 'Servicio',
            ),
            4 =>
            array (
                'id' => 5,
                'service_feature_category_id' => 5,
                'language_id' => 2,
                'name' => 'Ambiente',
            ),
            5 =>
            array (
                'id' => 6,
                'service_feature_category_id' => 6,
                'language_id' => 2,
                'name' => 'Instalaciones',
            ),
            6 =>
            array (
                'id' => 7,
                'service_feature_category_id' => 7,
                'language_id' => 2,
                'name' => 'Accesibilidad',
            ),
            7 =>
            array (
                'id' => 8,
                'service_feature_category_id' => 8,
                'language_id' => 2,
                'name' => 'Métodos de pago',
            ),
            8 =>
            array (
                'id' => 9,
                'service_feature_category_id' => 9,
                'language_id' => 2,
                'name' => 'Reservas',
            ),
            9 =>
            array (
                'id' => 10,
                'service_feature_category_id' => 10,
                'language_id' => 2,
                'name' => 'Ubicación',
            ),
            10 =>
            array (
                'id' => 11,
                'service_feature_category_id' => 11,
                'language_id' => 2,
                'name' => 'Experiencia',
            ),
        ));


    }
}
