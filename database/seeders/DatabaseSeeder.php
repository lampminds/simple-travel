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
        $this->call(UserModelHasRolesTableSeeder::class);
        $this->call(UserPermissionsTableSeeder::class);
        $this->call(UserRoleHasPermissionsTableSeeder::class);


        $this->call(CatLocalesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(CatCurrenciesTableSeeder::class);

        $this->call(CatContactTypesTableSeeder::class);

        $this->call(CatAccountCategoriesTableSeeder::class);
        $this->call(CatAccountCategoryTranslationsTableSeeder::class);

        $this->call(CatServiceTypesTableSeeder::class);
        $this->call(CatServiceTypeTranslationsTableSeeder::class);

        $this->call(AccountsTableSeeder::class);
        $this->call(ServiceTransferLocationTypesCatalogSeeder::class);
        // Default transfer locations for system catalog (account_id null).
        $this->call(ServiceTransferLocationsFozDoIguacuAccount1Seeder::class); // city_id=11691
        $this->call(ServiceTransferLocationsSanMartinDeLosAndesSeeder::class); // city_id=1425
        $this->call(ServiceTransferVehicleTypesCatalogSeeder::class);
        $this->call(AccountUserTableSeeder::class);

        // Pivot rows need existing accounts (FK to accounts.id).
        $this->call(CatContactDepartmentsTableSeeder::class);
        $this->call(CatContactDepartmentTranslationsTableSeeder::class);
        $this->call(CatContactPositionsTableSeeder::class);
        $this->call(CatContactPositionTranslationsTableSeeder::class);

        $this->call(ServiceDetailTopicCategoriesTableSeeder::class);
        $this->call(ServiceDetailTopicCategoryTranslationsTableSeeder::class);
        $this->call(CatServiceDetailConditionKeysTableSeeder::class);
        $this->call(ServiceDetailTopicsTableSeeder::class);
        $this->call(ServiceDetailTopicTranslationsTableSeeder::class);

        $this->call(ServiceActivityTypeCategoriesTableSeeder::class);
        $this->call(ServiceActivityTypeCategoryTranslationsTableSeeder::class);
        $this->call(ServiceActivityTypesTableSeeder::class);
        $this->call(ServiceActivityTypeTranslationsTableSeeder::class);

        $this->call(ServiceExperienceCategoriesTableSeeder::class);
        $this->call(ServiceExperienceCategoryTranslationsTableSeeder::class);
        $this->call(ServiceExperiencesTableSeeder::class);
        $this->call(ServiceExperienceTranslationsTableSeeder::class);

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

        $this->call(CatMenusTableSeeder::class);
        $this->call(CatMenuTranslationsTableSeeder::class);

        $this->call(CatParameterDefinitionsTableSeeder::class);
        $this->call(CatParameterDefinitionTranslationsTableSeeder::class);
        $this->call(ParameterValuesTableSeeder::class);
        $this->call(CatParameterOptionsTableSeeder::class);
        $this->call(CatParameterOptionTranslationsTableSeeder::class);

        $this->call(TodoCategoriesTableSeeder::class);
        $this->call(TodoCategoryTranslationsTableSeeder::class);
        $this->call(TodoTasksTableSeeder::class);
        $this->call(TodoTaskTranslationsTableSeeder::class);

        $this->call(UserInvitationsTableSeeder::class);
        $this->call(AccountCategoryAssignmentsTableSeeder::class);
        $this->call(AccountRelationshipsTableSeeder::class);
        $this->call(CatContactTypeTranslationsTableSeeder::class);
        $this->call(CatAccountTypesTableSeeder::class);
        $this->call(CatAccountTypeTranslationsTableSeeder::class);
        $this->call(AccountTypeAssignmentsTableSeeder::class);
        $this->call(CatMenuAccountTypeExclusionsTableSeeder::class);
        $this->call(CatHelpersTableSeeder::class);
        $this->call(CatHelperTranslationsTableSeeder::class);
//        $this->call(CurrencyRatesTableSeeder::class);
        $this->call(PersonsTableSeeder::class);
        $this->call(AccountPersonTableSeeder::class);
        $this->call(PersonContactMethodsTableSeeder::class);
        $this->call(UserPersonTableSeeder::class);
    }
}

