<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialPlanTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('commercial_plan_translations')->delete();
        
        \DB::table('commercial_plan_translations')->insert(array (
            0 => 
            array (
                'id' => 8,
                'commercial_plan_id' => 1,
                'language_id' => 2,
                'name' => 'Plan inicial',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 9,
                'commercial_plan_id' => 3,
                'language_id' => 2,
                'name' => 'Business',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 10,
                'commercial_plan_id' => 2,
                'language_id' => 2,
                'name' => 'Profesional',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 11,
                'commercial_plan_id' => 4,
                'language_id' => 2,
                'name' => 'Enterprise',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 12,
                'commercial_plan_id' => 5,
                'language_id' => 2,
                'name' => 'Agencia básico',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 13,
                'commercial_plan_id' => 6,
                'language_id' => 2,
                'name' => 'Agencia avanzado',
                'description' => NULL,
            ),
        ));
        
        
    }
}