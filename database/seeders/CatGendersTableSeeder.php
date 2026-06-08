<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatGendersTableSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('cat_genders')->delete();

        \DB::table('cat_genders')->insert([
            [
                'id' => 1,
                'code' => 'female',
                'active' => 1,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'code' => 'male',
                'active' => 1,
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'code' => 'non_binary',
                'active' => 1,
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'code' => 'prefer_not_to_say',
                'active' => 1,
                'sort_order' => 4,
            ],
        ]);
    }
}
