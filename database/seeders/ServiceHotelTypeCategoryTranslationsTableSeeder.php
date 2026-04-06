<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceHotelTypeCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_hotel_type_category_translations')->delete();
        
        \DB::table('cat_service_hotel_type_category_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_hotel_type_category_id' => 1,
                'language_id' => 2,
                'name' => 'Concepto y diseño',
            ),
            1 => 
            array (
                'id' => 2,
                'service_hotel_type_category_id' => 2,
                'language_id' => 2,
                'name' => 'Ubicación',
            ),
            2 => 
            array (
                'id' => 3,
                'service_hotel_type_category_id' => 3,
                'language_id' => 2,
                'name' => 'Tipo de servicio',
            ),
        ));
        
        
    }
}