<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatGenderTranslationsTableSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('cat_gender_translations')->delete();

        \DB::table('cat_gender_translations')->insert([
            // English (language_id 1)
            ['id' => 1, 'gender_id' => 1, 'language_id' => 1, 'name' => 'Female'],
            ['id' => 2, 'gender_id' => 2, 'language_id' => 1, 'name' => 'Male'],
            ['id' => 3, 'gender_id' => 3, 'language_id' => 1, 'name' => 'Non-binary'],
            ['id' => 4, 'gender_id' => 4, 'language_id' => 1, 'name' => 'Prefer not to say'],
            // Spanish (language_id 2)
            ['id' => 5, 'gender_id' => 1, 'language_id' => 2, 'name' => 'Femenino'],
            ['id' => 6, 'gender_id' => 2, 'language_id' => 2, 'name' => 'Masculino'],
            ['id' => 7, 'gender_id' => 3, 'language_id' => 2, 'name' => 'No binario'],
            ['id' => 8, 'gender_id' => 4, 'language_id' => 2, 'name' => 'Prefiero no decirlo'],
        ]);
    }
}
