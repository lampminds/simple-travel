<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatContactTypeTranslationsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('cat_contact_type_translations')->delete();

        \DB::table('cat_contact_type_translations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'contact_type_id' => 1,
                'language_id' => 1,
                'name' => 'E-mail',
            ),
            1 =>
            array (
                'id' => 2,
                'contact_type_id' => 1,
                'language_id' => 2,
                'name' => 'E-mail',
            ),
            2 =>
            array (
                'id' => 3,
                'contact_type_id' => 1,
                'language_id' => 3,
                'name' => 'E-mail',
            ),
            3 =>
            array (
                'id' => 4,
                'contact_type_id' => 2,
                'language_id' => 1,
                'name' => 'Phone',
            ),
            4 =>
            array (
                'id' => 5,
                'contact_type_id' => 2,
                'language_id' => 2,
                'name' => 'Teléfono',
            ),
            5 =>
            array (
                'id' => 6,
                'contact_type_id' => 2,
                'language_id' => 3,
                'name' => 'Teléfono',
            ),
            6 =>
            array (
                'id' => 7,
                'contact_type_id' => 3,
                'language_id' => 1,
                'name' => 'WhatsApp',
            ),
            7 =>
            array (
                'id' => 8,
                'contact_type_id' => 3,
                'language_id' => 2,
                'name' => 'WhatsApp',
            ),
            8 =>
            array (
                'id' => 9,
                'contact_type_id' => 3,
                'language_id' => 3,
                'name' => 'WhatsApp',
            ),
        ));
    }
}
