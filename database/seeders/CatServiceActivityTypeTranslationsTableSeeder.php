<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatServiceActivityTypeTranslationsTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('cat_service_activity_type_translations')->delete();

        \DB::table('cat_service_activity_type_translations')->insert($this->normalizeMojibake(array (
            0 => 
            array (
                'id' => 1,
                'service_activity_type_id' => 1,
                'language_id' => 2,
                'name' => 'Senderismo',
            ),
            1 => 
            array (
                'id' => 2,
                'service_activity_type_id' => 2,
                'language_id' => 2,
                'name' => 'Trekking',
            ),
            2 => 
            array (
                'id' => 3,
                'service_activity_type_id' => 3,
                'language_id' => 2,
                'name' => 'Caminata en la naturaleza',
            ),
            3 => 
            array (
                'id' => 4,
                'service_activity_type_id' => 4,
                'language_id' => 2,
                'name' => 'Observación de aves',
            ),
            4 => 
            array (
                'id' => 5,
                'service_activity_type_id' => 5,
                'language_id' => 2,
                'name' => 'Exploración geológica',
            ),
            5 => 
            array (
                'id' => 6,
                'service_activity_type_id' => 6,
                'language_id' => 2,
                'name' => 'Fotografía',
            ),
            6 => 
            array (
                'id' => 7,
                'service_activity_type_id' => 7,
                'language_id' => 2,
                'name' => 'Fotografía de naturaleza',
            ),
            7 => 
            array (
                'id' => 8,
                'service_activity_type_id' => 8,
                'language_id' => 2,
                'name' => 'Kayak',
            ),
            8 => 
            array (
                'id' => 9,
                'service_activity_type_id' => 9,
                'language_id' => 2,
                'name' => 'Canotaje',
            ),
            9 => 
            array (
                'id' => 10,
                'service_activity_type_id' => 10,
                'language_id' => 2,
                'name' => 'Rafting',
            ),
            10 => 
            array (
                'id' => 11,
                'service_activity_type_id' => 11,
                'language_id' => 2,
                'name' => 'Paddle surf',
            ),
            11 => 
            array (
                'id' => 12,
                'service_activity_type_id' => 12,
                'language_id' => 2,
                'name' => 'Snorkel',
            ),
            12 => 
            array (
                'id' => 13,
                'service_activity_type_id' => 13,
                'language_id' => 2,
                'name' => 'Buceo',
            ),
            13 => 
            array (
                'id' => 14,
                'service_activity_type_id' => 14,
                'language_id' => 2,
                'name' => 'Natación',
            ),
            14 => 
            array (
                'id' => 15,
                'service_activity_type_id' => 15,
                'language_id' => 2,
                'name' => 'Pesca',
            ),
            15 => 
            array (
                'id' => 16,
                'service_activity_type_id' => 16,
                'language_id' => 2,
                'name' => 'Pesca con mosca',
            ),
            16 => 
            array (
                'id' => 17,
                'service_activity_type_id' => 17,
                'language_id' => 2,
                'name' => 'Navegación a vela',
            ),
            17 => 
            array (
                'id' => 18,
                'service_activity_type_id' => 18,
                'language_id' => 2,
                'name' => 'Paseo en barco',
            ),
            18 => 
            array (
                'id' => 19,
                'service_activity_type_id' => 19,
                'language_id' => 2,
                'name' => 'Lancha',
            ),
            19 => 
            array (
                'id' => 20,
                'service_activity_type_id' => 20,
                'language_id' => 2,
                'name' => 'Moto de agua',
            ),
            20 => 
            array (
                'id' => 21,
                'service_activity_type_id' => 21,
                'language_id' => 2,
                'name' => 'Montañismo',
            ),
            21 => 
            array (
                'id' => 22,
                'service_activity_type_id' => 22,
                'language_id' => 2,
                'name' => 'Escalada en roca',
            ),
            22 => 
            array (
                'id' => 23,
                'service_activity_type_id' => 23,
                'language_id' => 2,
                'name' => 'Escalada en hielo',
            ),
            23 => 
            array (
                'id' => 24,
                'service_activity_type_id' => 24,
                'language_id' => 2,
                'name' => 'Vía ferrata',
            ),
            24 => 
            array (
                'id' => 25,
                'service_activity_type_id' => 25,
                'language_id' => 2,
                'name' => 'Barranquismo',
            ),
            25 => 
            array (
                'id' => 26,
                'service_activity_type_id' => 26,
                'language_id' => 2,
                'name' => 'Tirolesa',
            ),
            26 => 
            array (
                'id' => 27,
                'service_activity_type_id' => 27,
                'language_id' => 2,
                'name' => 'Parapente',
            ),
            27 => 
            array (
                'id' => 28,
                'service_activity_type_id' => 28,
                'language_id' => 2,
                'name' => 'Ala delta',
            ),
            28 => 
            array (
                'id' => 29,
                'service_activity_type_id' => 29,
                'language_id' => 2,
                'name' => 'Puenting',
            ),
            29 => 
            array (
                'id' => 30,
                'service_activity_type_id' => 30,
                'language_id' => 2,
                'name' => 'Ciclismo de montaña',
            ),
            30 => 
            array (
                'id' => 31,
                'service_activity_type_id' => 31,
                'language_id' => 2,
                'name' => 'Descenso en bicicleta',
            ),
            31 => 
            array (
                'id' => 32,
                'service_activity_type_id' => 32,
                'language_id' => 2,
                'name' => 'Trail running',
            ),
            32 => 
            array (
                'id' => 33,
                'service_activity_type_id' => 33,
                'language_id' => 2,
                'name' => 'Off-road',
            ),
            33 => 
            array (
                'id' => 34,
                'service_activity_type_id' => 34,
                'language_id' => 2,
                'name' => 'Paseo en cuatriciclo',
            ),
            34 => 
            array (
                'id' => 35,
                'service_activity_type_id' => 35,
                'language_id' => 2,
                'name' => 'Paseo en buggy',
            ),
            35 => 
            array (
                'id' => 36,
                'service_activity_type_id' => 36,
                'language_id' => 2,
                'name' => 'Sandboard',
            ),
            36 => 
            array (
                'id' => 37,
                'service_activity_type_id' => 37,
                'language_id' => 2,
                'name' => 'Recorrido por dunas',
            ),
            37 => 
            array (
                'id' => 38,
                'service_activity_type_id' => 38,
                'language_id' => 2,
                'name' => 'Circuito de aventura',
            ),
            38 => 
            array (
                'id' => 39,
                'service_activity_type_id' => 39,
                'language_id' => 2,
                'name' => 'Esquí',
            ),
            39 => 
            array (
                'id' => 40,
                'service_activity_type_id' => 40,
                'language_id' => 2,
                'name' => 'Snowboard',
            ),
            40 => 
            array (
                'id' => 41,
                'service_activity_type_id' => 41,
                'language_id' => 2,
                'name' => 'Esquí de fondo',
            ),
            41 => 
            array (
                'id' => 42,
                'service_activity_type_id' => 42,
                'language_id' => 2,
                'name' => 'Caminata con raquetas',
            ),
            42 => 
            array (
                'id' => 43,
                'service_activity_type_id' => 43,
                'language_id' => 2,
                'name' => 'Trineo',
            ),
            43 => 
            array (
                'id' => 44,
                'service_activity_type_id' => 44,
                'language_id' => 2,
                'name' => 'Trineo con perros',
            ),
            44 => 
            array (
                'id' => 45,
                'service_activity_type_id' => 45,
                'language_id' => 2,
                'name' => 'Moto de nieve',
            ),
            45 => 
            array (
                'id' => 46,
                'service_activity_type_id' => 46,
                'language_id' => 2,
                'name' => 'Patinaje sobre hielo',
            ),
            46 => 
            array (
                'id' => 47,
                'service_activity_type_id' => 47,
                'language_id' => 2,
                'name' => 'Caminata invernal',
            ),
            47 => 
            array (
                'id' => 48,
                'service_activity_type_id' => 48,
                'language_id' => 2,
                'name' => 'Visita a museo',
            ),
            48 => 
            array (
                'id' => 49,
                'service_activity_type_id' => 49,
                'language_id' => 2,
                'name' => 'Visita a sitios religiosos',
            ),
            49 => 
            array (
                'id' => 50,
                'service_activity_type_id' => 50,
                'language_id' => 2,
                'name' => 'Taller',
            ),
            50 => 
            array (
                'id' => 51,
                'service_activity_type_id' => 51,
                'language_id' => 2,
                'name' => 'Taller de artesanía',
            ),
            51 => 
            array (
                'id' => 52,
                'service_activity_type_id' => 52,
                'language_id' => 2,
                'name' => 'Taller de fotografía',
            ),
            52 => 
            array (
                'id' => 53,
                'service_activity_type_id' => 53,
                'language_id' => 2,
                'name' => 'Clase de idioma',
            ),
            53 => 
            array (
                'id' => 54,
                'service_activity_type_id' => 54,
                'language_id' => 2,
                'name' => 'Clase de arte',
            ),
            54 => 
            array (
                'id' => 55,
                'service_activity_type_id' => 55,
                'language_id' => 2,
                'name' => 'Taller educativo',
            ),
            55 => 
            array (
                'id' => 56,
                'service_activity_type_id' => 56,
                'language_id' => 2,
                'name' => 'Degustación gastronómica',
            ),
            56 => 
            array (
                'id' => 57,
                'service_activity_type_id' => 57,
                'language_id' => 2,
                'name' => 'Degustación de vinos',
            ),
            57 => 
            array (
                'id' => 58,
                'service_activity_type_id' => 58,
                'language_id' => 2,
                'name' => 'Degustación de cervezas',
            ),
            58 => 
            array (
                'id' => 59,
                'service_activity_type_id' => 59,
                'language_id' => 2,
                'name' => 'Degustación de destilados',
            ),
            59 => 
            array (
                'id' => 60,
                'service_activity_type_id' => 60,
                'language_id' => 2,
                'name' => 'Clase de cocina',
            ),
            60 => 
            array (
                'id' => 61,
                'service_activity_type_id' => 61,
                'language_id' => 2,
                'name' => 'Visita a viñedo',
            ),
            61 => 
            array (
                'id' => 62,
                'service_activity_type_id' => 62,
                'language_id' => 2,
                'name' => 'Visita a cervecería',
            ),
            62 => 
            array (
                'id' => 63,
                'service_activity_type_id' => 63,
                'language_id' => 2,
                'name' => 'Visita a destilería',
            ),
            63 => 
            array (
                'id' => 64,
                'service_activity_type_id' => 64,
                'language_id' => 2,
                'name' => 'Vuelo panorámico',
            ),
            64 => 
            array (
                'id' => 65,
                'service_activity_type_id' => 65,
                'language_id' => 2,
                'name' => 'Tour en helicóptero',
            ),
            65 => 
            array (
                'id' => 66,
                'service_activity_type_id' => 66,
                'language_id' => 2,
                'name' => 'Globo aerostático',
            ),
            66 => 
            array (
                'id' => 67,
                'service_activity_type_id' => 67,
                'language_id' => 2,
                'name' => 'Paseo en tren',
            ),
            67 => 
            array (
                'id' => 68,
                'service_activity_type_id' => 68,
                'language_id' => 2,
                'name' => 'Crucero en barco',
            ),
            68 => 
            array (
                'id' => 69,
                'service_activity_type_id' => 69,
                'language_id' => 2,
                'name' => 'Paseo en catamarán',
            ),
            69 => 
            array (
                'id' => 70,
                'service_activity_type_id' => 70,
                'language_id' => 2,
                'name' => 'Crucero al atardecer',
            ),
            70 => 
            array (
                'id' => 71,
                'service_activity_type_id' => 71,
                'language_id' => 2,
                'name' => 'Cabalgata',
            ),
            71 => 
            array (
                'id' => 72,
                'service_activity_type_id' => 72,
                'language_id' => 2,
                'name' => 'Trekking a caballo',
            ),
            72 => 
            array (
                'id' => 73,
                'service_activity_type_id' => 73,
                'language_id' => 2,
                'name' => 'Paseo en carruaje',
            ),
            73 => 
            array (
                'id' => 74,
                'service_activity_type_id' => 74,
                'language_id' => 2,
                'name' => 'Ciclismo',
            ),
            74 => 
            array (
                'id' => 75,
                'service_activity_type_id' => 75,
                'language_id' => 2,
                'name' => 'Alquiler de bicicletas',
            ),
            75 => 
            array (
                'id' => 76,
                'service_activity_type_id' => 76,
                'language_id' => 2,
                'name' => 'Alimentación de animales',
            ),
            76 => 
            array (
                'id' => 77,
                'service_activity_type_id' => 77,
                'language_id' => 2,
                'name' => 'Esquila de ovejas',
            ),
            77 => 
            array (
                'id' => 78,
                'service_activity_type_id' => 78,
                'language_id' => 2,
                'name' => 'Elaboración de quesos',
            ),
            78 => 
            array (
                'id' => 79,
                'service_activity_type_id' => 79,
                'language_id' => 2,
                'name' => 'Entrenamiento de caballos',
            ),
            79 => 
            array (
                'id' => 80,
                'service_activity_type_id' => 80,
                'language_id' => 2,
                'name' => 'Spa',
            ),
            80 => 
            array (
                'id' => 81,
                'service_activity_type_id' => 81,
                'language_id' => 2,
                'name' => 'Masajes',
            ),
            81 => 
            array (
                'id' => 82,
                'service_activity_type_id' => 82,
                'language_id' => 2,
                'name' => 'Termas',
            ),
            82 => 
            array (
                'id' => 83,
                'service_activity_type_id' => 83,
                'language_id' => 2,
                'name' => 'Yoga',
            ),
            83 => 
            array (
                'id' => 84,
                'service_activity_type_id' => 84,
                'language_id' => 2,
                'name' => 'Meditación',
            ),
            84 => 
            array (
                'id' => 85,
                'service_activity_type_id' => 85,
                'language_id' => 2,
                'name' => 'Fitness',
            ),
            85 => 
            array (
                'id' => 86,
                'service_activity_type_id' => 86,
                'language_id' => 2,
                'name' => 'Pilates',
            ),
            86 => 
            array (
                'id' => 87,
                'service_activity_type_id' => 87,
                'language_id' => 2,
                'name' => 'Golf',
            ),
            87 => 
            array (
                'id' => 88,
                'service_activity_type_id' => 88,
                'language_id' => 2,
                'name' => 'Mini golf',
            ),
            88 => 
            array (
                'id' => 89,
                'service_activity_type_id' => 89,
                'language_id' => 2,
                'name' => 'Tenis',
            ),
            89 => 
            array (
                'id' => 90,
                'service_activity_type_id' => 90,
                'language_id' => 2,
                'name' => 'Pádel',
            ),
            90 => 
            array (
                'id' => 91,
                'service_activity_type_id' => 91,
                'language_id' => 2,
                'name' => 'Tenis de mesa',
            ),
            91 => 
            array (
                'id' => 92,
                'service_activity_type_id' => 92,
                'language_id' => 2,
                'name' => 'Bowling',
            ),
            92 => 
            array (
                'id' => 93,
                'service_activity_type_id' => 93,
                'language_id' => 2,
                'name' => 'Billar',
            ),
            93 => 
            array (
                'id' => 94,
                'service_activity_type_id' => 94,
                'language_id' => 2,
                'name' => 'Tiro con arco',
            ),
            94 => 
            array (
                'id' => 95,
                'service_activity_type_id' => 95,
                'language_id' => 2,
                'name' => 'Tiro deportivo',
            ),
            95 => 
            array (
                'id' => 96,
                'service_activity_type_id' => 96,
                'language_id' => 2,
                'name' => 'Camping',
            ),
            96 => 
            array (
                'id' => 97,
                'service_activity_type_id' => 97,
                'language_id' => 2,
                'name' => 'Glamping',
            ),
            97 => 
            array (
                'id' => 98,
                'service_activity_type_id' => 98,
                'language_id' => 2,
                'name' => 'Búsqueda del tesoro',
            ),
        )));
    }

    private function normalizeMojibake(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->normalizeMojibake($item);
            }

            return $value;
        }

        if (! is_string($value)) {
            return $value;
        }

        return $value;
    }
}
