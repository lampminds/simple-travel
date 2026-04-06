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
                'active' => 1,
                'sort_order' => 9999,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'phone',
                'active' => 1,
                'sort_order' => 9999,
            ),
            2 =>
            array (
                'id' => 3,
                'code' => 'whatsapp',
                'active' => 1,
                'sort_order' => 9999,
            ),
        ));
    }
}
