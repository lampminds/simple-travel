<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceFeatureTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cat_service_feature_translations')->delete();

        \DB::table('cat_service_feature_translations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'service_feature_id' => 1,
                'language_id' => 2,
                'name' => 'Comida orgánica',
            ),
            1 =>
            array (
                'id' => 2,
                'service_feature_id' => 2,
                'language_id' => 2,
                'name' => 'Ingredientes locales',
            ),
            2 =>
            array (
                'id' => 3,
                'service_feature_id' => 3,
                'language_id' => 2,
                'name' => 'Comida casera',
            ),
            3 =>
            array (
                'id' => 4,
                'service_feature_id' => 4,
                'language_id' => 2,
                'name' => 'Menú de temporada',
            ),
            4 =>
            array (
                'id' => 5,
                'service_feature_id' => 5,
                'language_id' => 2,
                'name' => 'Buena selección de vinos',
            ),
            5 =>
            array (
                'id' => 6,
                'service_feature_id' => 6,
                'language_id' => 2,
                'name' => 'Cerveza artesanal',
            ),
            6 =>
            array (
                'id' => 7,
                'service_feature_id' => 7,
                'language_id' => 2,
                'name' => 'Cócteles',
            ),
            7 =>
            array (
                'id' => 8,
                'service_feature_id' => 8,
                'language_id' => 2,
                'name' => 'Café de especialidad',
            ),
            8 =>
            array (
                'id' => 9,
                'service_feature_id' => 9,
                'language_id' => 2,
                'name' => 'Opciones vegetarianas',
            ),
            9 =>
            array (
                'id' => 10,
                'service_feature_id' => 10,
                'language_id' => 2,
                'name' => 'Opciones veganas',
            ),
            10 =>
            array (
                'id' => 11,
                'service_feature_id' => 11,
                'language_id' => 2,
                'name' => 'Opciones sin gluten',
            ),
            11 =>
            array (
                'id' => 12,
                'service_feature_id' => 12,
                'language_id' => 2,
                'name' => 'Sin lactosa',
            ),
            12 =>
            array (
                'id' => 13,
                'service_feature_id' => 13,
                'language_id' => 2,
                'name' => 'Servicio a la mesa',
            ),
            13 =>
            array (
                'id' => 14,
                'service_feature_id' => 14,
                'language_id' => 2,
                'name' => 'Autoservicio',
            ),
            14 =>
            array (
                'id' => 15,
                'service_feature_id' => 15,
                'language_id' => 2,
                'name' => 'Para llevar',
            ),
            15 =>
            array (
                'id' => 16,
                'service_feature_id' => 16,
                'language_id' => 2,
                'name' => 'Delivery',
            ),
            16 =>
            array (
                'id' => 17,
                'service_feature_id' => 17,
                'language_id' => 2,
                'name' => 'Sommelier',
            ),
            17 =>
            array (
                'id' => 18,
                'service_feature_id' => 18,
                'language_id' => 2,
                'name' => 'Personal multilingüe',
            ),
            18 =>
            array (
                'id' => 19,
                'service_feature_id' => 19,
                'language_id' => 2,
                'name' => 'Música en vivo',
            ),
            19 =>
            array (
                'id' => 20,
                'service_feature_id' => 20,
                'language_id' => 2,
                'name' => 'DJ',
            ),
            20 =>
            array (
                'id' => 21,
                'service_feature_id' => 21,
                'language_id' => 2,
                'name' => 'Ambiente romántico',
            ),
            21 =>
            array (
                'id' => 22,
                'service_feature_id' => 22,
                'language_id' => 2,
                'name' => 'Familiar',
            ),
            22 =>
            array (
                'id' => 23,
                'service_feature_id' => 23,
                'language_id' => 2,
                'name' => 'Solo adultos',
            ),
            23 =>
            array (
                'id' => 24,
                'service_feature_id' => 24,
                'language_id' => 2,
                'name' => 'Vista panorámica',
            ),
            24 =>
            array (
                'id' => 25,
                'service_feature_id' => 25,
                'language_id' => 2,
                'name' => 'Mesas al aire libre',
            ),
            25 =>
            array (
                'id' => 26,
                'service_feature_id' => 26,
                'language_id' => 2,
                'name' => 'Interior',
            ),
            26 =>
            array (
                'id' => 27,
                'service_feature_id' => 27,
                'language_id' => 2,
                'name' => 'Terraza',
            ),
            27 =>
            array (
                'id' => 28,
                'service_feature_id' => 28,
                'language_id' => 2,
                'name' => 'Estacionamiento',
            ),
            28 =>
            array (
                'id' => 29,
                'service_feature_id' => 29,
                'language_id' => 2,
                'name' => 'WiFi',
            ),
            29 =>
            array (
                'id' => 30,
                'service_feature_id' => 30,
                'language_id' => 2,
                'name' => 'Aire acondicionado',
            ),
            30 =>
            array (
                'id' => 31,
                'service_feature_id' => 31,
                'language_id' => 2,
                'name' => 'Calefacción',
            ),
            31 =>
            array (
                'id' => 32,
                'service_feature_id' => 32,
                'language_id' => 2,
                'name' => 'Acceso para sillas de ruedas',
            ),
            32 =>
            array (
                'id' => 33,
                'service_feature_id' => 33,
                'language_id' => 2,
                'name' => 'Baño accesible',
            ),
            33 =>
            array (
                'id' => 34,
                'service_feature_id' => 34,
                'language_id' => 2,
                'name' => 'Tarjetas de crédito',
            ),
            34 =>
            array (
                'id' => 35,
                'service_feature_id' => 35,
                'language_id' => 2,
                'name' => 'Tarjetas de débito',
            ),
            35 =>
            array (
                'id' => 36,
                'service_feature_id' => 36,
                'language_id' => 2,
                'name' => 'Efectivo',
            ),
            36 =>
            array (
                'id' => 37,
                'service_feature_id' => 37,
                'language_id' => 2,
                'name' => 'Pagos digitales',
            ),
            37 =>
            array (
                'id' => 38,
                'service_feature_id' => 38,
                'language_id' => 2,
                'name' => 'Requiere reserva',
            ),
            38 =>
            array (
                'id' => 39,
                'service_feature_id' => 39,
                'language_id' => 2,
                'name' => 'Reserva recomendada',
            ),
            39 =>
            array (
                'id' => 40,
                'service_feature_id' => 40,
                'language_id' => 2,
                'name' => 'Sin reserva',
            ),
            40 =>
            array (
                'id' => 41,
                'service_feature_id' => 41,
                'language_id' => 2,
                'name' => 'Frente a la playa',
            ),
            41 =>
            array (
                'id' => 42,
                'service_feature_id' => 42,
                'language_id' => 2,
                'name' => 'Centro',
            ),
            42 =>
            array (
                'id' => 43,
                'service_feature_id' => 43,
                'language_id' => 2,
                'name' => 'Vista a la montaña',
            ),
            43 =>
            array (
                'id' => 44,
                'service_feature_id' => 44,
                'language_id' => 2,
                'name' => 'Vista al lago',
            ),
            44 =>
            array (
                'id' => 45,
                'service_feature_id' => 45,
                'language_id' => 2,
                'name' => 'Experiencia guiada',
            ),
            45 =>
            array (
                'id' => 46,
                'service_feature_id' => 46,
                'language_id' => 2,
                'name' => 'Interactiva',
            ),
            46 =>
            array (
                'id' => 47,
                'service_feature_id' => 47,
                'language_id' => 2,
                'name' => 'Incluye espectáculo',
            ),
        ));


    }
}
