<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatServiceExperienceTranslationsTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('cat_service_experience_translations')->delete();

        \DB::table('cat_service_experience_translations')->insert([
            ['id' => 1, 'service_experience_id' => 1, 'language_id' => 2, 'name' => 'Naturaleza'],
            ['id' => 2, 'service_experience_id' => 2, 'language_id' => 2, 'name' => 'Actividades acuáticas'],
            ['id' => 3, 'service_experience_id' => 3, 'language_id' => 2, 'name' => 'Actividades culturales'],
            ['id' => 4, 'service_experience_id' => 4, 'language_id' => 2, 'name' => 'Gastronomía'],
            ['id' => 5, 'service_experience_id' => 5, 'language_id' => 2, 'name' => 'Bienestar'],
            ['id' => 6, 'service_experience_id' => 6, 'language_id' => 2, 'name' => 'Actividades familiares'],
            ['id' => 7, 'service_experience_id' => 7, 'language_id' => 2, 'name' => 'Experiencias rurales'],
            ['id' => 8, 'service_experience_id' => 8, 'language_id' => 2, 'name' => 'Aventura'],
            ['id' => 9, 'service_experience_id' => 9, 'language_id' => 2, 'name' => 'Observación'],
            ['id' => 10, 'service_experience_id' => 10, 'language_id' => 2, 'name' => 'Relax'],
        ]);
    }
}
