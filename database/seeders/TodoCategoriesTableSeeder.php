<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TodoCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('todo_categories')->delete();
        
        \DB::table('todo_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'account_setup',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}