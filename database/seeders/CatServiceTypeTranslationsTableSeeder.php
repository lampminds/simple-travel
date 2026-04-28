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
                'id' => 1,
                'service_type_id' => 1,
                'language_id' => 1,
                'name' => 'Hotel',
            ),
            1 =>
            array (
                'id' => 2,
                'service_type_id' => 1,
                'language_id' => 2,
                'name' => 'Hotel',
            ),
            2 =>
            array (
                'id' => 3,
                'service_type_id' => 1,
                'language_id' => 3,
                'name' => 'Hotel',
            ),
            3 =>
            array (
                'id' => 4,
                'service_type_id' => 2,
                'language_id' => 1,
                'name' => 'Transfer',
            ),
            4 =>
            array (
                'id' => 5,
                'service_type_id' => 2,
                'language_id' => 2,
                'name' => 'Traslado',
            ),
            5 =>
            array (
                'id' => 6,
                'service_type_id' => 2,
                'language_id' => 3,
                'name' => 'Transfer',
            ),
            6 =>
            array (
                'id' => 7,
                'service_type_id' => 3,
                'language_id' => 1,
                'name' => 'Tour',
            ),
            7 =>
            array (
                'id' => 8,
                'service_type_id' => 3,
                'language_id' => 2,
                'name' => 'Entertainment',
            ),
            8 =>
            array (
                'id' => 9,
                'service_type_id' => 3,
                'language_id' => 3,
                'name' => 'Tour',
            ),
            9 =>
            array (
                'id' => 10,
                'service_type_id' => 4,
                'language_id' => 1,
                'name' => 'Gastronomy',
            ),
            10 =>
            array (
                'id' => 11,
                'service_type_id' => 4,
                'language_id' => 2,
                'name' => 'Gastronomía',
            ),
            11 =>
            array (
                'id' => 12,
                'service_type_id' => 4,
                'language_id' => 3,
                'name' => 'Gastronomia',
            ),
        ));


    }
}
