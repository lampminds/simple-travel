<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassengerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'code' => 'infant',
                'sort_order' => 1,
                'names' => [
                    1 => 'Infant',
                    2 => 'Bebé',
                    3 => 'Bebê',
                ],
            ],
            [
                'code' => 'child',
                'sort_order' => 2,
                'names' => [
                    1 => 'Child',
                    2 => 'Menor',
                    3 => 'Menor',
                ],
            ],
            [
                'code' => 'adult',
                'sort_order' => 3,
                'names' => [
                    1 => 'Adult',
                    2 => 'Adulto',
                    3 => 'Adulto',
                ],
            ],
            [
                'code' => 'senior',
                'sort_order' => 4,
                'names' => [
                    1 => 'Senior',
                    2 => 'Adulto mayor',
                    3 => 'Idoso',
                ],
            ],
        ];

        foreach ($categories as $category) {
            $categoryId = DB::table('passenger_categories')
                ->where('code', $category['code'])
                ->value('id');

            if ($categoryId) {
                DB::table('passenger_categories')
                    ->where('id', $categoryId)
                    ->update([
                        'sort_order' => $category['sort_order'],
                        'updated_at' => $now,
                    ]);
            } else {
                $categoryId = DB::table('passenger_categories')->insertGetId([
                    'code' => $category['code'],
                    'sort_order' => $category['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($category['names'] as $languageId => $name) {
                $exists = DB::table('passenger_category_translations')
                    ->where('passenger_category_id', $categoryId)
                    ->where('language_id', $languageId)
                    ->exists();

                if ($exists) {
                    DB::table('passenger_category_translations')
                        ->where('passenger_category_id', $categoryId)
                        ->where('language_id', $languageId)
                        ->update([
                            'name' => $name,
                            'updated_at' => $now,
                        ]);

                    continue;
                }

                DB::table('passenger_category_translations')->insert([
                    'passenger_category_id' => $categoryId,
                    'language_id' => $languageId,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
