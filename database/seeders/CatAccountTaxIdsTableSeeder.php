<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatAccountTaxIdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_account_tax_ids')->delete();
        
        \DB::table('cat_account_tax_ids')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_id' => 2,
                'account_category_id' => 1,
                'value' => '30707143335',
            ),
        ));
        
        
    }
}
