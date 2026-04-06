<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatLocalesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_locales')->delete();
        
        \DB::table('cat_locales')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tag' => 'en-US',
            'name_en' => 'English (United States)',
                'active' => 1,
                'sort_order' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'tag' => 'es-AR',
            'name_en' => 'Spanish (Argentina)',
                'active' => 1,
                'sort_order' => 2,
            ),
            2 => 
            array (
                'id' => 3,
                'tag' => 'pt-BR',
            'name_en' => 'Portuguese (Brazil)',
                'active' => 1,
                'sort_order' => 3,
            ),
            3 => 
            array (
                'id' => 4,
                'tag' => 'en-GB',
            'name_en' => 'English (United Kingdom)',
                'active' => 1,
                'sort_order' => 4,
            ),
            4 => 
            array (
                'id' => 5,
                'tag' => 'es-ES',
            'name_en' => 'Spanish (Spain)',
                'active' => 1,
                'sort_order' => 5,
            ),
            5 => 
            array (
                'id' => 6,
                'tag' => 'es-MX',
            'name_en' => 'Spanish (Mexico)',
                'active' => 1,
                'sort_order' => 6,
            ),
            6 => 
            array (
                'id' => 7,
                'tag' => 'fr-FR',
            'name_en' => 'French (France)',
                'active' => 1,
                'sort_order' => 7,
            ),
            7 => 
            array (
                'id' => 8,
                'tag' => 'de-DE',
            'name_en' => 'German (Germany)',
                'active' => 1,
                'sort_order' => 8,
            ),
            8 => 
            array (
                'id' => 9,
                'tag' => 'it-IT',
            'name_en' => 'Italian (Italy)',
                'active' => 1,
                'sort_order' => 9,
            ),
        ));
        
        
    }
}