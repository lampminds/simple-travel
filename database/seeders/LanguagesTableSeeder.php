<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('cat_languages')->delete();

        \DB::table('cat_languages')->insert(array (
            0 =>
            array (
                'id' => 1,
                'language_id' => 1,
            ),
            1 =>
            array (
                'id' => 2,
                'language_id' => 2,
            ),
            2 =>
            array (
                'id' => 3,
                'language_id' => 3,
            ),
        ));


    }
}
