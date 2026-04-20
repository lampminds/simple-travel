<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TodoCategoryTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('todo_category_translations')->delete();
        
        \DB::table('todo_category_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'todo_category_id' => 1,
                'language_id' => 2,
                'name' => 'Setup de tu cuenta',
                'description' => NULL,
            ),
        ));
        
        
    }
}