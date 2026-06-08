<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatServiceExperiencesTableSeeder extends Seeder
{
    /**
     * Thematic guest-facing experiences (formerly experience categories).
     * Activity-specific domains (mountain, snow, horses, etc.) live in cat_service_activity_types.
     */
    public function run()
    {
        \DB::table('cat_service_experiences')->delete();

        \DB::table('cat_service_experiences')->insert([
            ['id' => 1, 'code' => 'nature', 'sort_order' => 1, 'active' => 1],
            ['id' => 2, 'code' => 'water', 'sort_order' => 2, 'active' => 1],
            ['id' => 3, 'code' => 'culture', 'sort_order' => 3, 'active' => 1],
            ['id' => 4, 'code' => 'gastronomics', 'sort_order' => 4, 'active' => 1],
            ['id' => 5, 'code' => 'welfare', 'sort_order' => 5, 'active' => 1],
            ['id' => 6, 'code' => 'family', 'sort_order' => 6, 'active' => 1],
            ['id' => 7, 'code' => 'farm', 'sort_order' => 7, 'active' => 1],
            ['id' => 8, 'code' => 'adventure', 'sort_order' => 8, 'active' => 1],
            ['id' => 9, 'code' => 'watching', 'sort_order' => 9, 'active' => 1],
            ['id' => 10, 'code' => 'relax', 'sort_order' => 10, 'active' => 1],
        ]);
    }
}
