<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatContactDepartmentsTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        \DB::table('cat_contact_departments')->delete();

        \DB::table('cat_contact_departments')->insert(array (
            0 =>
            array (
                'id' => 1,
                'code' => 'management',
                'active' => 1,
                'sort_order' => 9999,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'finance',
                'active' => 1,
                'sort_order' => 9999,
            ),
            2 =>
            array (
                'id' => 3,
                'code' => 'accounting',
                'active' => 1,
                'sort_order' => 9999,
            ),
            3 =>
            array (
                'id' => 4,
                'code' => 'billing',
                'active' => 1,
                'sort_order' => 9999,
            ),
            4 =>
            array (
                'id' => 5,
                'code' => 'payments',
                'active' => 1,
                'sort_order' => 9999,
            ),
            5 =>
            array (
                'id' => 6,
                'code' => 'sales',
                'active' => 1,
                'sort_order' => 9999,
            ),
            6 =>
            array (
                'id' => 7,
                'code' => 'marketing',
                'active' => 1,
                'sort_order' => 9999,
            ),
            7 =>
            array (
                'id' => 8,
                'code' => 'reservations',
                'active' => 1,
                'sort_order' => 9999,
            ),
            8 =>
            array (
                'id' => 9,
                'code' => 'operations',
                'active' => 1,
                'sort_order' => 9999,
            ),
            9 =>
            array (
                'id' => 10,
                'code' => 'contracting',
                'active' => 1,
                'sort_order' => 9999,
            ),
            10 =>
            array (
                'id' => 11,
                'code' => 'customer_service',
                'active' => 1,
                'sort_order' => 9999,
            ),
            11 =>
            array (
                'id' => 12,
                'code' => 'product',
                'active' => 1,
                'sort_order' => 9999,
            ),
            12 =>
            array (
                'id' => 13,
                'code' => 'it',
                'active' => 1,
                'sort_order' => 9999,
            ),
        ));
    }
}
