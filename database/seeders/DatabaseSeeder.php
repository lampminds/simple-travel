<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ParametersTableSeeder::class);
        $this->call(UserRolesTableSeeder::class);
        $this->call(UserModelHasRolesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(ContactTypesTableSeeder::class);
        $this->call(AccountCategoriesTableSeeder::class);
        $this->call(AccountCategoryTranslationsTableSeeder::class);
    }
}
