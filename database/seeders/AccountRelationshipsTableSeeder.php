<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccountRelationshipsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('account_relationships')->delete();


    }
}
