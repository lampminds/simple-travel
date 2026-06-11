<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatMenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_menus')->delete();
        
        \DB::table('cat_menus')->insert(array (
            0 => 
            array (
                'id' => 1,
                'slug' => 'panel',
                'icon' => NULL,
                'route' => 'account.dashboard',
                'parent_id' => NULL,
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'slug' => 'network',
                'icon' => NULL,
                'route' => NULL,
                'parent_id' => NULL,
                'sort_order' => 3,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'slug' => 'catalog',
                'icon' => NULL,
                'route' => NULL,
                'parent_id' => NULL,
                'sort_order' => 2,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'slug' => 'operations',
                'icon' => NULL,
                'route' => 'operations',
                'parent_id' => 25,
                'sort_order' => 7,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'slug' => 'finances',
                'icon' => NULL,
                'route' => 'finances',
                'parent_id' => 25,
                'sort_order' => 10,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'slug' => 'integrations',
                'icon' => NULL,
                'route' => 'integrations',
                'parent_id' => 25,
                'sort_order' => 11,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'slug' => 'operator_service_inbox',
                'icon' => NULL,
                'route' => 'account.service-offers.index',
                'parent_id' => NULL,
                'sort_order' => 5,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 9,
                'slug' => 'reservations',
                'icon' => NULL,
                'route' => 'account.reservations.index',
                'parent_id' => NULL,
                'sort_order' => 6,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 10,
                'slug' => 'clients',
                'icon' => NULL,
                'route' => 'clients',
                'parent_id' => NULL,
                'sort_order' => 7,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 11,
                'slug' => 'invite_employee',
                'icon' => NULL,
                'route' => 'account.invitations.employee',
                'parent_id' => 2,
                'sort_order' => 11,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 12,
                'slug' => 'invite_company',
                'icon' => NULL,
                'route' => 'account.invitations.company',
                'parent_id' => 2,
                'sort_order' => 13,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 14,
                'slug' => 'website',
                'icon' => NULL,
                'route' => NULL,
                'parent_id' => NULL,
                'sort_order' => 12,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 15,
                'slug' => 'website_configuration',
                'icon' => NULL,
                'route' => 'website_configuration',
                'parent_id' => 14,
                'sort_order' => 8,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 16,
                'slug' => 'browse_website',
                'icon' => NULL,
                'route' => 'browse_website',
                'parent_id' => 14,
                'sort_order' => 10,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 17,
                'slug' => 'prices',
                'icon' => NULL,
                'route' => NULL,
                'parent_id' => NULL,
                'sort_order' => 4,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 18,
                'slug' => 'relationships',
                'icon' => NULL,
                'route' => 'account.relationships.index',
                'parent_id' => 2,
                'sort_order' => 5,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 19,
                'slug' => 'vehicles',
                'icon' => NULL,
                'route' => 'account.transfer-vehicle-types.index',
                'parent_id' => 3,
                'sort_order' => 9,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 20,
                'slug' => 'services',
                'icon' => NULL,
                'route' => 'catalog',
                'parent_id' => 3,
                'sort_order' => 2,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 22,
                'slug' => 'prices_provider',
                'icon' => NULL,
                'route' => 'account.provider-price-lists.index',
                'parent_id' => 17,
                'sort_order' => 4,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 23,
                'slug' => 'prices_operator-copy',
                'icon' => NULL,
                'route' => 'account.operator-price-lists.index',
                'parent_id' => 17,
                'sort_order' => 7,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 24,
                'slug' => 'operator_packages',
                'icon' => NULL,
                'route' => 'account.operator-packages.index',
                'parent_id' => NULL,
                'sort_order' => 6,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 25,
                'slug' => 'tbd',
                'icon' => NULL,
                'route' => NULL,
                'parent_id' => NULL,
                'sort_order' => 8,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 26,
                'slug' => 'provider_catalog_offers',
                'icon' => NULL,
                'route' => 'account.service-offers.index',
                'parent_id' => NULL,
                'sort_order' => 5,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 27,
                'slug' => 'operator_package_offers',
                'icon' => NULL,
                'route' => 'account.package-offers.index',
                'parent_id' => NULL,
                'sort_order' => 7,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 28,
                'slug' => 'agency_package_inbox',
                'icon' => NULL,
                'route' => 'account.package-offers.index',
                'parent_id' => NULL,
                'sort_order' => 5,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 29,
                'slug' => 'operator_reservations',
                'icon' => NULL,
                'route' => 'account.operator.reservations.index',
                'parent_id' => NULL,
                'sort_order' => 6,
                'active' => 1,
            ),
        ));
        
        
    }
}