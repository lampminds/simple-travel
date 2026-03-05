<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('account_category_translations')->delete();
        
        \DB::table('account_category_translations')->insert(array (
            0 => 
            array (
                'id' => 4,
                'account_category_id' => 2,
                'language_id' => 1,
                'name' => 'DNI',
                'description' => 'Argentinian document',
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 5,
                'account_category_id' => 2,
                'language_id' => 2,
                'name' => 'DNI',
                'description' => 'Documento Nacional de Identidad',
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 6,
                'account_category_id' => 2,
                'language_id' => 3,
                'name' => 'DNI',
                'description' => 'Documento argentino',
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 7,
                'account_category_id' => 1,
                'language_id' => 1,
                'name' => 'Cuit',
                'description' => 'Argentinian tax ID',
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 8,
                'account_category_id' => 1,
                'language_id' => 2,
                'name' => 'Cuit',
                'description' => 'Clave única de identificación tributaria',
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 9,
                'account_category_id' => 1,
                'language_id' => 3,
                'name' => 'Cuit',
                'description' => 'Número de identificação fiscal argentino',
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 10,
                'account_category_id' => 3,
                'language_id' => 1,
                'name' => 'Service provider',
                'description' => 'Hotels, transport, tours, guides, rentals, gastronomic',
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 11,
                'account_category_id' => 3,
                'language_id' => 2,
                'name' => 'Prestador de servicios',
                'description' => 'Hoteles, transporte, tours, rentals, guías, gastronómicos',
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 12,
                'account_category_id' => 3,
                'language_id' => 3,
                'name' => 'Prestador de serviços',
                'description' => 'hotéis, passeios, guias, transportes',
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 13,
                'account_category_id' => 4,
                'language_id' => 1,
                'name' => 'Tour operator',
                'description' => 'The one who puts together the tour package by combining several suppliers.',
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 14,
                'account_category_id' => 4,
                'language_id' => 2,
                'name' => 'Operador turístico',
                'description' => 'Es quien arma el paquete turístico combinando varios proveedores.',
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 15,
                'account_category_id' => 4,
                'language_id' => 3,
                'name' => 'Operador de turismo',
                'description' => 'É ele que monta o pacote turístico, combinando vários fornecedores.',
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 16,
                'account_category_id' => 5,
                'language_id' => 1,
                'name' => 'Tour wholesaler',
                'description' => 'Purchase large allocations from operators or directly from suppliers. Distribute to retail agencies.',
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 17,
                'account_category_id' => 5,
                'language_id' => 2,
                'name' => 'Mayorista de turismo',
                'description' => 'Compra grandes cupos a operadores o directamente a proveedores. Distribuye a agencias minoristas',
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 18,
                'account_category_id' => 5,
                'language_id' => 3,
                'name' => 'Operador turístico',
                'description' => 'Adquira grandes quantidades a operadores ou diretamente a fornecedores. Distribua para agências de retalho.',
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 19,
                'account_category_id' => 6,
                'language_id' => 1,
                'name' => 'Travel Agency',
                'description' => 'Sells to travellers',
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 20,
                'account_category_id' => 6,
                'language_id' => 2,
                'name' => 'Agencia de turismo',
                'description' => 'Vende directo al público',
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 21,
                'account_category_id' => 6,
                'language_id' => 3,
                'name' => 'Agência de viagens',
                'description' => 'Vende diretamente ao público',
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 22,
                'account_category_id' => 7,
                'language_id' => 1,
                'name' => 'Online Travel Agency',
                'description' => 'Digital/Online travel agency',
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 23,
                'account_category_id' => 7,
                'language_id' => 2,
                'name' => 'OTA',
                'description' => 'Agencia de viajes digital/online',
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 24,
                'account_category_id' => 7,
                'language_id' => 3,
                'name' => 'OTA',
                'description' => 'Agência de viagens digital/online',
                'active' => 1,
            ),
        ));
        
        
    }
}