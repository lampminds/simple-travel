<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatParameterDefinitionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_parameter_definitions')->delete();
        
        \DB::table('cat_parameter_definitions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'category' => 'accounts',
                'subcategory' => 'invitations',
                'code' => 'invitation_expiration_days',
                'type' => 'integer',
                'scope' => 'tenant',
                'has_default' => 1,
                'ui_component' => 'input',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => '7',
                'validation_rules' => '[]',
                'comments' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'category' => 'accounts',
                'subcategory' => 'invitations',
                'code' => 'max_invitations_retries',
                'type' => 'integer',
                'scope' => 'tenant',
                'has_default' => 1,
                'ui_component' => 'input',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => '3',
                'validation_rules' => '[]',
                'comments' => NULL,
            ),
        ));
        
        
    }
}