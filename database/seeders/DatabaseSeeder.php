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
        $this->call(CatLanguagesTableSeeder::class);
        $this->call(CatCurrenciesTableSeeder::class);

        $this->call(CatContactTypesTableSeeder::class);
        $this->call(CatGendersTableSeeder::class);
        $this->call(CatGenderTranslationsTableSeeder::class);

        $this->call(CatDocumentsTableSeeder::class);
        $this->call(CatDocumentTranslationsTableSeeder::class);

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

        $this->call(CatServiceDetailTopicCategoriesTableSeeder::class);
        $this->call(CatServiceDetailTopicCategoryTranslationsTableSeeder::class);
        $this->call(CatServiceDetailConditionKeysTableSeeder::class);
        $this->call(CatServiceDetailTopicsTableSeeder::class);

        $this->call(CatServiceActivityTypeCategoriesTableSeeder::class);
        $this->call(CatServiceActivityTypeCategoryTranslationsTableSeeder::class);
        $this->call(CatServiceActivityTypesTableSeeder::class);
        $this->call(CatServiceActivityTypeTranslationsTableSeeder::class);

        $this->call(CatServiceExperiencesTableSeeder::class);
        $this->call(CatServiceExperienceTranslationsTableSeeder::class);

        $this->call(CatServiceHotelTypeCategoriesTableSeeder::class);
        $this->call(CatServiceHotelTypeCategoryTranslationsTableSeeder::class);
        $this->call(CatServiceHotelTypesTableSeeder::class);
        $this->call(CatServiceHotelTypeTranslationsTableSeeder::class);

        $this->call(CatServiceGastronomyTypesTableSeeder::class);
        $this->call(CatServiceGastronomyTypeTranslationsTableSeeder::class);
        $this->call(CatServiceGastronomyVenuesTableSeeder::class);
        $this->call(CatServiceGastronomyVenueTranslationsTableSeeder::class);
        $this->call(CatServiceGastronomyCuisinesTableSeeder::class);
        $this->call(CatServiceGastronomyCuisineTranslationsTableSeeder::class);
        $this->call(CatServiceGastronomyMenuCategoriesTableSeeder::class);
        $this->call(CatServiceGastronomyMenuCategoryTranslationsTableSeeder::class);
        $this->call(CatServiceGastronomyMenusTableSeeder::class);
        $this->call(CatServiceGastronomyMenuTranslationsTableSeeder::class);

        $this->call(CatServiceFeatureCategoriesTableSeeder::class);
        $this->call(CatServiceFeatureCategoryTranslationsTableSeeder::class);
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
        $this->call(AccountRelationshipsTableSeeder::class);
        $this->call(CatContactTypeTranslationsTableSeeder::class);
        $this->call(CatAccountTypesTableSeeder::class);
        $this->call(CatAccountTypeTranslationsTableSeeder::class);
        $this->call(AccountTypeAssignmentsTableSeeder::class);
        $this->call(CatMenuAccountTypeExclusionsTableSeeder::class);
        $this->call(CatHelpersTableSeeder::class);
        $this->call(CatHelperTranslationsTableSeeder::class);
        $this->call(CatFaqsTableSeeder::class);
        $this->call(CatFaqTranslationsTableSeeder::class);
        $this->call(CurrencyRatesTableSeeder::class);
        $this->call(PersonsTableSeeder::class);
        $this->call(AccountPersonTableSeeder::class);
        $this->call(PersonContactMethodsTableSeeder::class);
        $this->call(UserPersonTableSeeder::class);
        $this->call(CatBookingStatusesTableSeeder::class);
        $this->call(CatBookingStatusTranslationsTableSeeder::class);
    }
}

