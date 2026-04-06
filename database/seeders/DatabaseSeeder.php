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
        $this->call(UserRolesTableSeeder::class);
        $this->call(CatLocalesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(CatContactTypesTableSeeder::class);
        $this->call(CatAccountCategoriesTableSeeder::class);
        $this->call(CatAccountCategoryTranslationsTableSeeder::class);
        $this->call(PlansTableSeeder::class);
        $this->call(PlanItemsTableSeeder::class);
        $this->call(PlanItemTranslationsTableSeeder::class);
        $this->call(PlanUserPricesTableSeeder::class);
        $this->call(PlanUserPriceTranslationsTableSeeder::class);
        $this->call(ServiceTypesTableSeeder::class);
        $this->call(ServiceTypeTranslationsTableSeeder::class);
        $this->call(AccountsTableSeeder::class);
        $this->call(AccountUserTableSeeder::class);
        $this->call(UserModelHasRolesTableSeeder::class);
        // Pivot rows need existing accounts (FK to accounts.id).
        $this->call(AccountCategoryAssignmentsTableSeeder::class);
        $this->call(CatAccountTaxIdsTableSeeder::class);
        $this->call(CatContactDepartmentsTableSeeder::class);
        $this->call(CatContactDepartmentTranslationsTableSeeder::class);
        $this->call(CatContactPositionsTableSeeder::class);
        $this->call(CatContactPositionTranslationsTableSeeder::class);
        $this->call(ServiceDetailTopicCategoriesTableSeeder::class);
        $this->call(ServiceDetailTopicCategoryTranslationsTableSeeder::class);
        $this->call(ServiceDetailTopicsTableSeeder::class);
        $this->call(ServiceDetailTopicTranslationsTableSeeder::class);
        $this->call(ServiceExcursionTypeCategoriesTableSeeder::class);
        $this->call(ServiceExcursionTypeCategoryTranslationsTableSeeder::class);
        $this->call(ServiceExcursionTypesTableSeeder::class);
        $this->call(ServiceExcursionTypeTranslationsTableSeeder::class);
        $this->call(ServiceActivityCategoriesTableSeeder::class);
        $this->call(ServiceActivityCategoryTranslationsTableSeeder::class);
        $this->call(ServiceActivitiesTableSeeder::class);
        $this->call(ServiceActivityTranslationsTableSeeder::class);
        $this->call(CatCurrenciesTableSeeder::class);
        $this->call(ServiceHotelTypeCategoriesTableSeeder::class);
        $this->call(ServiceHotelTypeCategoryTranslationsTableSeeder::class);
        $this->call(ServiceHotelTypesTableSeeder::class);
        $this->call(ServiceHotelTypeTranslationsTableSeeder::class);
        $this->call(ServiceGastronomyTypesTableSeeder::class);
        $this->call(ServiceGastronomyTypeTranslationsTableSeeder::class);
        $this->call(ServiceGastronomyVenuesTableSeeder::class);
        $this->call(ServiceGastronomyVenueTranslationsTableSeeder::class);
        $this->call(ServiceGastronomyCuisinesTableSeeder::class);
        $this->call(ServiceGastronomyCuisineTranslationsTableSeeder::class);
        $this->call(ServiceGastronomyMenuCategoriesTableSeeder::class);
        $this->call(ServiceGastronomyMenuCategoryTranslationsTableSeeder::class);
        $this->call(ServiceGastronomyMenusTableSeeder::class);
        $this->call(ServiceGastronomyMenuTranslationsTableSeeder::class);
        $this->call(ServiceFeatureCategoriesTableSeeder::class);
        $this->call(ServiceFeatureCategoryTranslationsTableSeeder::class);
        $this->call(CatServiceFeaturesTableSeeder::class);
        $this->call(CatServiceFeatureScopesTableSeeder::class);
        $this->call(CatServiceFeatureTranslationsTableSeeder::class);
    }
}
