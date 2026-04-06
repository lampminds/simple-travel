<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatContactDepartmentTranslationsTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        \DB::table('cat_contact_department_translations')->delete();

        \DB::table('cat_contact_department_translations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'contact_department_id' => 1,
                'language_id' => 2,
                'name' => 'Dirección / Management',
            ),
            1 =>
            array (
                'id' => 2,
                'contact_department_id' => 2,
                'language_id' => 2,
                'name' => 'Finanzas',
            ),
            2 =>
            array (
                'id' => 3,
                'contact_department_id' => 3,
                'language_id' => 2,
                'name' => 'Contabilidad',
            ),
            3 =>
            array (
                'id' => 5,
                'contact_department_id' => 4,
                'language_id' => 2,
                'name' => 'Facturación / Cobranzas',
            ),
            4 =>
            array (
                'id' => 6,
                'contact_department_id' => 5,
                'language_id' => 2,
                'name' => 'Pagos',
            ),
            5 =>
            array (
                'id' => 7,
                'contact_department_id' => 6,
                'language_id' => 2,
                'name' => 'Ventas',
            ),
            6 =>
            array (
                'id' => 8,
                'contact_department_id' => 7,
                'language_id' => 2,
                'name' => 'Marketing',
            ),
            7 =>
            array (
                'id' => 9,
                'contact_department_id' => 8,
                'language_id' => 2,
                'name' => 'Reservas',
            ),
            8 =>
            array (
                'id' => 10,
                'contact_department_id' => 9,
                'language_id' => 2,
                'name' => 'Coordinación operativa',
            ),
            9 =>
            array (
                'id' => 11,
                'contact_department_id' => 10,
                'language_id' => 2,
                'name' => 'Contratos con prestadores',
            ),
            10 =>
            array (
                'id' => 12,
                'contact_department_id' => 11,
                'language_id' => 2,
                'name' => 'Atención al cliente',
            ),
            11 =>
            array (
                'id' => 13,
                'contact_department_id' => 12,
                'language_id' => 2,
                'name' => 'Diseño de experiencias',
            ),
            12 =>
            array (
                'id' => 14,
                'contact_department_id' => 13,
                'language_id' => 2,
                'name' => 'Sistemas / Tecnología',
            ),
        ));
    }
}
