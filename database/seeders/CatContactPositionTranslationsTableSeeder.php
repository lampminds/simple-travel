<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Translations for cat_contact_positions.
 */
class CatContactPositionTranslationsTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        \DB::table('cat_contact_position_translations')->delete();

        \DB::table('cat_contact_position_translations')->insert([
            0 => [
                'id' => 1,
                'contact_position_id' => 1,
                'language_id' => 2,
                'name' => 'Director del área',
            ],
            1 => [
                'id' => 2,
                'contact_position_id' => 2,
                'language_id' => 2,
                'name' => 'Responsable',
            ],
            2 => [
                'id' => 3,
                'contact_position_id' => 3,
                'language_id' => 2,
                'name' => 'Supervisor',
            ],
            3 => [
                'id' => 4,
                'contact_position_id' => 4,
                'language_id' => 2,
                'name' => 'Agente / Ejecutivo',
            ],
            4 => [
                'id' => 5,
                'contact_position_id' => 5,
                'language_id' => 2,
                'name' => 'Asistente',
            ],
            5 => [
                'id' => 6,
                'contact_position_id' => 6,
                'language_id' => 2,
                'name' => 'Analista',
            ],
            6 => [
                'id' => 7,
                'contact_position_id' => 7,
                'language_id' => 2,
                'name' => 'Especialista',
            ],
            7 => [
                'id' => 8,
                'contact_position_id' => 8,
                'language_id' => 2,
                'name' => 'Coordinador',
            ],
            8 => [
                'id' => 9,
                'contact_position_id' => 9,
                'language_id' => 2,
                'name' => 'Personal general',
            ],
        ]);
    }
}
