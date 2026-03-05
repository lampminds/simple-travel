<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContactTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('contact_types')->delete();

        //pa iseed --force --exclude=created_at,updated_at,created_by,updated_by contact_types

        \DB::table('contact_types')->insert(array (
            0 =>
            array (
                'id' => 1,
                'code' => 'email',
                'active' => true,
            ),
            1 =>
            array (
                'id' => 2,
                'code' => 'phone',
                'active' => true,
            ),
        ));


    }
}
