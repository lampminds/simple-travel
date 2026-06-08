<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatHelpersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_helpers')->delete();
        
        \DB::table('cat_helpers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'screen_code' => 'service_wizard_step1',
                'code' => 'catalog_service_description',
                'account_type_id' => NULL,
                'service_type_id' => 4,
                'active' => 1,
                'notes' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'screen_code' => 'service_wizard_step4_variants',
                'code' => 'catalog_variant_description',
                'account_type_id' => NULL,
                'service_type_id' => 4,
                'active' => 1,
                'notes' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'screen_code' => 'service_wizard_step4_variants',
                'code' => 'catalog_variant_base_price',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'screen_code' => 'service_wizard_step4_variants',
                'code' => 'catalog_variant_currency',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'screen_code' => 'service_wizard_step2',
                'code' => 'duration_minutes',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'screen_code' => 'service_wizard_step2',
                'code' => 'confirmation_time_hours',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            6 => 
            array (
                'id' => 8,
                'screen_code' => 'service_wizard_step2',
                'code' => 'is_featured',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            7 => 
            array (
                'id' => 9,
                'screen_code' => 'service_wizard_step2',
                'code' => 'is_public',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            8 => 
            array (
                'id' => 10,
                'screen_code' => 'service_wizard_step6',
                'code' => 'public',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            9 => 
            array (
                'id' => 11,
                'screen_code' => 'service_wizard_step6',
                'code' => 'operator',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            10 => 
            array (
                'id' => 12,
                'screen_code' => 'service_wizard_step6',
                'code' => 'internal',
                'account_type_id' => NULL,
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
            11 => 
            array (
                'id' => 13,
                'screen_code' => 'service_wizard_step1',
                'code' => 'catalog_service_description',
                'account_type_id' => NULL,
                'service_type_id' => 1,
                'active' => 1,
                'notes' => NULL,
            ),
            12 => 
            array (
                'id' => 14,
                'screen_code' => 'service_wizard_step4_variants',
                'code' => 'catalog_variant_description',
                'account_type_id' => NULL,
                'service_type_id' => 1,
                'active' => 1,
                'notes' => NULL,
            ),
        ));
        
        
    }
}