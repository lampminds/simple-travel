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
            2 => 
            array (
                'id' => 3,
                'category' => 'invitations',
                'subcategory' => NULL,
                'code' => 'default_invitation_status',
                'type' => 'string',
                'scope' => 'tenant',
                'has_default' => 1,
                'ui_component' => 'select',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => 'all',
                'validation_rules' => '[]',
                'comments' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'category' => 'prices',
                'subcategory' => 'format',
                'code' => 'price_decimals',
                'type' => 'integer',
                'scope' => 'tenant',
                'has_default' => 1,
                'ui_component' => 'select',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => '2',
                'validation_rules' => '[]',
                'comments' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'category' => 'prices',
                'subcategory' => 'format',
                'code' => 'price_thousands_separator',
                'type' => 'string',
                'scope' => 'tenant',
                'has_default' => 0,
                'ui_component' => 'select',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => NULL,
                'validation_rules' => '[]',
                'comments' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'category' => 'system',
                'subcategory' => 'plans',
                'code' => 'rounding_price_decimals',
                'type' => 'integer',
                'scope' => 'system',
                'has_default' => 0,
                'ui_component' => 'input',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => '-3',
                'validation_rules' => '[]',
                'comments' => '0: rounds $35.123,10 to $35.123
-2: rounds $35.123,10 to $35.100
-3: rounds $35.123,10 to $35.000',
            ),
            6 => 
            array (
                'id' => 7,
                'category' => 'system',
                'subcategory' => 'dates',
                'code' => 'date_format',
                'type' => 'string',
                'scope' => 'tenant',
                'has_default' => 0,
                'ui_component' => 'select',
                'ui_options' => '[]',
                'sort_order' => 0,
                'default_value' => NULL,
                'validation_rules' => '[]',
                'comments' => NULL,
            ),
        ));
        
        
    }
}