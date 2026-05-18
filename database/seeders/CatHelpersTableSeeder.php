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
                'service_type_id' => NULL,
                'active' => 1,
                'notes' => NULL,
            ),
        ));
        
        
    }
}