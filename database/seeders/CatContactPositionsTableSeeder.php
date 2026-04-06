<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Catalog contact positions (formerly contact roles).
 */
class CatContactPositionsTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        \DB::table('cat_contact_positions')->delete();

        \DB::table('cat_contact_positions')->insert([
            0 => [
                'id' => 1,
                'code' => 'director',
                'active' => 1,
                'sort_order' => 9999,
            ],
            1 => [
                'id' => 2,
                'code' => 'manager',
                'active' => 1,
                'sort_order' => 9999,
            ],
            2 => [
                'id' => 3,
                'code' => 'supervisor',
                'active' => 1,
                'sort_order' => 9999,
            ],
            3 => [
                'id' => 4,
                'code' => 'agent',
                'active' => 1,
                'sort_order' => 9999,
            ],
            4 => [
                'id' => 5,
                'code' => 'assistant',
                'active' => 1,
                'sort_order' => 9999,
            ],
            5 => [
                'id' => 6,
                'code' => 'analyst',
                'active' => 1,
                'sort_order' => 9999,
            ],
            6 => [
                'id' => 7,
                'code' => 'specialist',
                'active' => 1,
                'sort_order' => 9999,
            ],
            7 => [
                'id' => 8,
                'code' => 'coordinator',
                'active' => 1,
                'sort_order' => 9999,
            ],
            8 => [
                'id' => 9,
                'code' => 'staff',
                'active' => 1,
                'sort_order' => 9999,
            ],
        ]);
    }
}
