<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TodoTasksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('todo_tasks')->delete();
        
        \DB::table('todo_tasks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_id' => 1,
                'code' => 'complete_profile',
                'todo_category_id' => 1,
                'original_task_id' => NULL,
                'action_type' => 'route',
                'action_url' => 'account.company.edit',
                'verification_type' => 'none',
                'verification_url' => NULL,
                'sort_order' => 1,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'account_id' => 1,
                'code' => 'create_first_service',
                'todo_category_id' => 1,
                'original_task_id' => NULL,
                'action_type' => 'route',
                'action_url' => 'catalog',
                'verification_type' => 'none',
                'verification_url' => NULL,
                'sort_order' => 3,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'account_id' => 1,
                'code' => 'complete_user_profile',
                'todo_category_id' => 1,
                'original_task_id' => NULL,
                'action_type' => 'route',
                'action_url' => 'account.profile.edit',
                'verification_type' => 'none',
                'verification_url' => NULL,
                'sort_order' => 2,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'account_id' => 1,
                'code' => 'add_availability',
                'todo_category_id' => 1,
                'original_task_id' => NULL,
                'action_type' => 'none',
                'action_url' => NULL,
                'verification_type' => 'none',
                'verification_url' => NULL,
                'sort_order' => 5,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'account_id' => 1,
                'code' => 'set_pricing',
                'todo_category_id' => 1,
                'original_task_id' => NULL,
                'action_type' => 'none',
                'action_url' => NULL,
                'verification_type' => 'none',
                'verification_url' => NULL,
                'sort_order' => 6,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'account_id' => 1,
                'code' => 'upload_images',
                'todo_category_id' => 1,
                'original_task_id' => NULL,
                'action_type' => 'none',
                'action_url' => NULL,
                'verification_type' => 'none',
                'verification_url' => NULL,
                'sort_order' => 4,
                'active' => 1,
            ),
        ));
        
        
    }
}