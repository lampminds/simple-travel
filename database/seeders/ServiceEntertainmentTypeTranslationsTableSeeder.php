<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceEntertainmentTypeTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_entertainment_type_translations')->delete();
        
        \DB::table('cat_service_entertainment_type_translations')->insert($this->normalizeMojibake(array (
            0 => 
            array (
                'id' => 1,
                'service_entertainment_type_id' => 1,
                'language_id' => 2,
                'name' => 'Tour por la ciudad',
            ),
            1 => 
            array (
                'id' => 2,
                'service_entertainment_type_id' => 2,
                'language_id' => 2,
                'name' => 'Tour a pie',
            ),
            2 => 
            array (
                'id' => 3,
                'service_entertainment_type_id' => 3,
                'language_id' => 2,
                'name' => 'Tour guiado',
            ),
            3 => 
            array (
                'id' => 4,
                'service_entertainment_type_id' => 4,
                'language_id' => 2,
                'name' => 'Tour autoguiado',
            ),
            4 => 
            array (
                'id' => 5,
                'service_entertainment_type_id' => 5,
                'language_id' => 2,
                'name' => 'Tour panorámico',
            ),
            5 => 
            array (
                'id' => 6,
                'service_entertainment_type_id' => 6,
                'language_id' => 2,
                'name' => 'Excursión de día completo',
            ),
            6 => 
            array (
                'id' => 7,
                'service_entertainment_type_id' => 7,
                'language_id' => 2,
                'name' => 'Excursión de medio día',
            ),
            7 => 
            array (
                'id' => 8,
                'service_entertainment_type_id' => 8,
                'language_id' => 2,
                'name' => 'Tour de varios días',
            ),
            8 => 
            array (
                'id' => 9,
                'service_entertainment_type_id' => 9,
                'language_id' => 2,
                'name' => 'Tour cultural',
            ),
            9 => 
            array (
                'id' => 10,
                'service_entertainment_type_id' => 10,
                'language_id' => 2,
                'name' => 'Tour histórico',
            ),
            10 => 
            array (
                'id' => 11,
                'service_entertainment_type_id' => 11,
                'language_id' => 2,
                'name' => 'Tour patrimonial',
            ),
            11 => 
            array (
                'id' => 12,
                'service_entertainment_type_id' => 12,
                'language_id' => 2,
                'name' => 'Tour de museos',
            ),
            12 => 
            array (
                'id' => 13,
                'service_entertainment_type_id' => 13,
                'language_id' => 2,
                'name' => 'Tour de arquitectura',
            ),
            13 => 
            array (
                'id' => 14,
                'service_entertainment_type_id' => 14,
                'language_id' => 2,
                'name' => 'Tour religioso',
            ),
            14 => 
            array (
                'id' => 15,
                'service_entertainment_type_id' => 15,
                'language_id' => 2,
                'name' => 'Tour arqueológico',
            ),
            15 => 
            array (
                'id' => 16,
                'service_entertainment_type_id' => 16,
                'language_id' => 2,
                'name' => 'Experiencia local',
            ),
            16 => 
            array (
                'id' => 17,
                'service_entertainment_type_id' => 17,
                'language_id' => 2,
                'name' => 'Tour de naturaleza',
            ),
            17 => 
            array (
                'id' => 18,
                'service_entertainment_type_id' => 18,
                'language_id' => 2,
                'name' => 'Tour de fauna',
            ),
            18 => 
            array (
                'id' => 19,
                'service_entertainment_type_id' => 19,
                'language_id' => 2,
                'name' => 'Tour de observación de aves',
            ),
            19 => 
            array (
                'id' => 20,
                'service_entertainment_type_id' => 20,
                'language_id' => 2,
                'name' => 'Tour de parque nacional',
            ),
            20 => 
            array (
                'id' => 21,
                'service_entertainment_type_id' => 21,
                'language_id' => 2,
                'name' => 'Tour escénico',
            ),
            21 => 
            array (
                'id' => 22,
                'service_entertainment_type_id' => 22,
                'language_id' => 2,
                'name' => 'Tour de paisajes',
            ),
            22 => 
            array (
                'id' => 23,
                'service_entertainment_type_id' => 23,
                'language_id' => 2,
                'name' => 'Tour de aventura',
            ),
            23 => 
            array (
                'id' => 24,
                'service_entertainment_type_id' => 24,
                'language_id' => 2,
                'name' => 'Tour de trekking',
            ),
            24 => 
            array (
                'id' => 25,
                'service_entertainment_type_id' => 25,
                'language_id' => 2,
                'name' => 'Tour de montaña',
            ),
            25 => 
            array (
                'id' => 26,
                'service_entertainment_type_id' => 26,
                'language_id' => 2,
                'name' => 'Tour off-road',
            ),
            26 => 
            array (
                'id' => 27,
                'service_entertainment_type_id' => 27,
                'language_id' => 2,
                'name' => 'Aventura extrema',
            ),
            27 => 
            array (
                'id' => 28,
                'service_entertainment_type_id' => 28,
                'language_id' => 2,
                'name' => 'Paseo en barco',
            ),
            28 => 
            array (
                'id' => 29,
                'service_entertainment_type_id' => 29,
                'language_id' => 2,
                'name' => 'Tour por lago',
            ),
            29 => 
            array (
                'id' => 30,
                'service_entertainment_type_id' => 30,
                'language_id' => 2,
                'name' => 'Tour por río',
            ),
            30 => 
            array (
                'id' => 31,
                'service_entertainment_type_id' => 31,
                'language_id' => 2,
                'name' => 'Crucero',
            ),
            31 => 
            array (
                'id' => 32,
                'service_entertainment_type_id' => 32,
                'language_id' => 2,
                'name' => 'Tour en catamarán',
            ),
            32 => 
            array (
                'id' => 33,
                'service_entertainment_type_id' => 33,
                'language_id' => 2,
                'name' => 'Tour en kayak',
            ),
            33 => 
            array (
                'id' => 34,
                'service_entertainment_type_id' => 34,
                'language_id' => 2,
                'name' => 'Excursión de rafting',
            ),
            34 => 
            array (
                'id' => 35,
                'service_entertainment_type_id' => 35,
                'language_id' => 2,
                'name' => 'Salida de pesca',
            ),
            35 => 
            array (
                'id' => 36,
                'service_entertainment_type_id' => 36,
                'language_id' => 2,
                'name' => 'Tour gastronómico',
            ),
            36 => 
            array (
                'id' => 37,
                'service_entertainment_type_id' => 37,
                'language_id' => 2,
                'name' => 'Tour del vino',
            ),
            37 => 
            array (
                'id' => 38,
                'service_entertainment_type_id' => 38,
                'language_id' => 2,
                'name' => 'Tour de cervecerías',
            ),
            38 => 
            array (
                'id' => 39,
                'service_entertainment_type_id' => 39,
                'language_id' => 2,
                'name' => 'Tour de destilerías',
            ),
            39 => 
            array (
                'id' => 40,
                'service_entertainment_type_id' => 40,
                'language_id' => 2,
                'name' => 'Tour culinario',
            ),
            40 => 
            array (
                'id' => 41,
                'service_entertainment_type_id' => 41,
                'language_id' => 2,
                'name' => 'Visita a granja',
            ),
            41 => 
            array (
                'id' => 42,
                'service_entertainment_type_id' => 42,
                'language_id' => 2,
                'name' => 'Experiencia en estancia',
            ),
            42 => 
            array (
                'id' => 43,
                'service_entertainment_type_id' => 43,
                'language_id' => 2,
                'name' => 'Agroturismo',
            ),
            43 => 
            array (
                'id' => 44,
                'service_entertainment_type_id' => 44,
                'language_id' => 2,
                'name' => 'Experiencia rural',
            ),
            44 => 
            array (
                'id' => 45,
                'service_entertainment_type_id' => 45,
                'language_id' => 2,
                'name' => 'Tour de esquí',
            ),
            45 => 
            array (
                'id' => 46,
                'service_entertainment_type_id' => 46,
                'language_id' => 2,
                'name' => 'Excursión invernal',
            ),
            46 => 
            array (
                'id' => 47,
                'service_entertainment_type_id' => 47,
                'language_id' => 2,
                'name' => 'Tour en moto de nieve',
            ),
            47 => 
            array (
                'id' => 48,
                'service_entertainment_type_id' => 48,
                'language_id' => 2,
                'name' => 'Tour en trineo de perros',
            ),
            48 => 
            array (
                'id' => 49,
                'service_entertainment_type_id' => 49,
                'language_id' => 2,
                'name' => 'Tour en helicóptero',
            ),
            49 => 
            array (
                'id' => 50,
                'service_entertainment_type_id' => 50,
                'language_id' => 2,
                'name' => 'Vuelo panorámico',
            ),
            50 => 
            array (
                'id' => 51,
                'service_entertainment_type_id' => 51,
                'language_id' => 2,
                'name' => 'Paseo en globo aerostático',
            ),
            51 => 
            array (
                'id' => 52,
                'service_entertainment_type_id' => 52,
                'language_id' => 2,
                'name' => 'Tour en tren',
            ),
            52 => 
            array (
                'id' => 53,
                'service_entertainment_type_id' => 53,
                'language_id' => 2,
                'name' => 'Tour en bicicleta',
            ),
            53 => 
            array (
                'id' => 54,
                'service_entertainment_type_id' => 54,
                'language_id' => 2,
                'name' => 'Tour a caballo',
            ),
            54 => 
            array (
                'id' => 55,
                'service_entertainment_type_id' => 55,
                'language_id' => 2,
                'name' => 'Tour en cuatriciclo',
            ),
            55 => 
            array (
                'id' => 56,
                'service_entertainment_type_id' => 56,
                'language_id' => 2,
                'name' => 'Tour fotográfico',
            ),
            56 => 
            array (
                'id' => 57,
                'service_entertainment_type_id' => 57,
                'language_id' => 2,
                'name' => 'Tour al atardecer',
            ),
            57 => 
            array (
                'id' => 58,
                'service_entertainment_type_id' => 58,
                'language_id' => 2,
                'name' => 'Tour al amanecer',
            ),
            58 => 
            array (
                'id' => 59,
                'service_entertainment_type_id' => 59,
                'language_id' => 2,
                'name' => 'Tour nocturno',
            ),
            59 => 
            array (
                'id' => 60,
                'service_entertainment_type_id' => 60,
                'language_id' => 2,
                'name' => 'Tour de observación de estrellas',
            ),
            60 => 
            array (
                'id' => 61,
                'service_entertainment_type_id' => 61,
                'language_id' => 2,
                'name' => 'Tour de festivales',
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
