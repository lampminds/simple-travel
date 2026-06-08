<?php

namespace Database\Seeders;

use Database\Seeders\Support\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatAccountTypeTranslationsTableSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $this->withoutForeignKeyChecks(function (): void {
            DB::table('cat_account_type_translations')->delete();

            DB::table('cat_account_type_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_type_id' => 1,
                'language_id' => 1,
                'name' => 'Tour operator',
                'description' => 'Builds tour products from several suppliers, may work B2B with providers and/or retail agencies.',
            ),
            1 => 
            array (
                'id' => 2,
                'account_type_id' => 1,
                'language_id' => 2,
                'name' => 'Operador turístico',
                'description' => 'Arma el paquete o cupos a partir de varios proveedores; puede vender a agencias u otros operadores.',
            ),
            2 => 
            array (
                'id' => 3,
                'account_type_id' => 1,
                'language_id' => 3,
                'name' => 'Operador turístico',
                'description' => 'Monta o pacote ou allotments com vários fornecedores; pode vender a agências ou a outros operadores.',
            ),
            3 => 
            array (
                'id' => 7,
                'account_type_id' => 3,
                'language_id' => 1,
                'name' => 'Travel Agency',
                'description' => 'Sells to travellers',
            ),
            4 => 
            array (
                'id' => 8,
                'account_type_id' => 3,
                'language_id' => 2,
                'name' => 'Agencia de turismo',
                'description' => 'Vende directo al público',
            ),
            5 => 
            array (
                'id' => 9,
                'account_type_id' => 3,
                'language_id' => 3,
                'name' => 'Agência de viagens',
                'description' => 'Vende diretamente ao público',
            ),
            6 => 
            array (
                'id' => 13,
                'account_type_id' => 2,
                'language_id' => 1,
                'name' => 'Service provider',
                'description' => 'Accommodations, transport, tours, guides, rentals, gastronomic',
            ),
            7 => 
            array (
                'id' => 14,
                'account_type_id' => 2,
                'language_id' => 2,
                'name' => 'Proveedor de servicios',
                'description' => 'Alojamientos, transporte, tours, rentals, guías, gastronómicos',
            ),
            8 => 
            array (
                'id' => 15,
                'account_type_id' => 2,
                'language_id' => 3,
                'name' => 'Prestador de serviços',
                'description' => 'Alojamentos, passeios, guias, transportes',
            ),
        ));
        });
    }
}