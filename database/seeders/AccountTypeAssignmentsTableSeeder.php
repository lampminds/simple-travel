<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountTypeAssignmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('account_type_assignments')->delete();
        
        \DB::table('account_type_assignments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_id' => 2,
                'account_type_id' => 1,
            ),
        ));
        
        
    }
}