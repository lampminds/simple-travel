<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeederUuid;
use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('accounts')->delete();
        
        \DB::table('accounts')->insert(SeederUuid::assign(array (
            0 => 
            array (
                'id' => 1,
                'nick' => 'system',
                'code' => 'system-967',
                'name' => 'Main System',
                'commercial_name' => NULL,
                'url' => NULL,
                'status' => 'active',
            ),
            1 => 
            array (
                'id' => 2,
                'nick' => 'eb-group-srl',
                'code' => 'eb-group-srl-879',
                'name' => 'EB Group SRL',
                'commercial_name' => 'EB Group SRL',
                'url' => NULL,
                'status' => 'active',
            ),
        )));
        
        
    }
}