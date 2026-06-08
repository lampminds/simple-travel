<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatDocumentTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_document_translations')->delete();
        
        \DB::table('cat_document_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'document_id' => 2,
                'language_id' => 1,
                'name' => 'DNI',
                'description' => 'Argentinian document',
            ),
            1 => 
            array (
                'id' => 2,
                'document_id' => 2,
                'language_id' => 2,
                'name' => 'DNI',
                'description' => 'Documento Nacional de Identidad',
            ),
            2 => 
            array (
                'id' => 3,
                'document_id' => 2,
                'language_id' => 3,
                'name' => 'DNI',
                'description' => 'Documento argentino',
            ),
            3 => 
            array (
                'id' => 4,
                'document_id' => 1,
                'language_id' => 2,
                'name' => 'Cuit',
                'description' => 'Clave única de identificación tributaria',
            ),
            4 => 
            array (
                'id' => 5,
                'document_id' => 1,
                'language_id' => 3,
                'name' => 'Cuit',
                'description' => 'Número de identificação fiscal argentino',
            ),
        ));
        
        
    }
}