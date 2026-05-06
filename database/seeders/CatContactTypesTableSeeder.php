<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatContactTypesTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        \DB::table('cat_contact_types')->delete();

        \DB::table('cat_contact_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'code' => 'email',
                'is_unique_per_person' => 1,
                'mask' => null,
                'validation' => null,
                'active' => 1,
                'sort_order' => 9999,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'phone',
                'is_unique_per_person' => 1,
                'mask' => null,
                'validation' => null,
                'active' => 1,
                'sort_order' => 9999,
            ),
            2 =>
            array (
                'id' => 3,
                'code' => 'whatsapp',
                'is_unique_per_person' => 1,
                'mask' => null,
                'validation' => null,
                'active' => 1,
                'sort_order' => 9999,
            ),
        ));
    }
}
