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
                'slug' => 'home',
                'icon' => NULL,
                'route' => 'home',
                'parent_id' => NULL,
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'slug' => 'network',
                'icon' => NULL,
                'route' => 'relationships',
                'parent_id' => NULL,
                'sort_order' => 2,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'slug' => 'catalog',
                'icon' => NULL,
                'route' => 'catalog',
                'parent_id' => NULL,
                'sort_order' => 3,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'slug' => 'operations',
                'icon' => NULL,
                'route' => 'operations',
                'parent_id' => NULL,
                'sort_order' => 4,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'slug' => 'finances',
                'icon' => NULL,
                'route' => 'finances',
                'parent_id' => NULL,
                'sort_order' => 8,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'slug' => 'integrations',
                'icon' => NULL,
                'route' => 'integrations',
                'parent_id' => NULL,
                'sort_order' => 9,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'slug' => 'availability',
                'icon' => NULL,
                'route' => 'availability',
                'parent_id' => NULL,
                'sort_order' => 5,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 9,
                'slug' => 'reservations',
                'icon' => NULL,
                'route' => 'reservations',
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
                'sort_order' => 1,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 12,
                'slug' => 'invite_company',
                'icon' => NULL,
                'route' => 'account.invitations.company',
                'parent_id' => 2,
                'sort_order' => 2,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 13,
                'slug' => 'invitations',
                'icon' => NULL,
                'route' => 'account.invitations.index',
                'parent_id' => 2,
                'sort_order' => 3,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 14,
                'slug' => 'website',
                'icon' => NULL,
                'route' => 'website',
                'parent_id' => NULL,
                'sort_order' => 11,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 15,
                'slug' => 'website_configuration',
                'icon' => NULL,
                'route' => 'website_configuration',
                'parent_id' => 14,
                'sort_order' => 1,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 16,
                'slug' => 'browse_website',
                'icon' => NULL,
                'route' => 'browse_website',
                'parent_id' => 14,
                'sort_order' => 2,
                'active' => 1,
            ),
        ));
        
        
    }
}