<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TodoTaskTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('todo_task_translations')->delete();
        
        \DB::table('todo_task_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'todo_task_id' => 1,
                'language_id' => 2,
                'name' => 'Completar el perfil de la empresa',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'todo_task_id' => 2,
                'language_id' => 2,
                'name' => 'Crear el primer servicio',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 4,
                'todo_task_id' => 4,
                'language_id' => 2,
                'name' => 'Agregar disponibilidad',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 5,
                'todo_task_id' => 5,
                'language_id' => 2,
                'name' => 'Establecer precios',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 6,
                'todo_task_id' => 6,
                'language_id' => 2,
                'name' => 'Cargar imágenes',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 7,
                'todo_task_id' => 3,
                'language_id' => 2,
                'name' => 'Completar el perfil de usuario',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 8,
                'todo_task_id' => 7,
                'language_id' => 2,
                'name' => 'Completar el perfil de la empresa',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 9,
                'todo_task_id' => 8,
                'language_id' => 2,
                'name' => 'Completar el perfil de usuario',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 10,
                'todo_task_id' => 9,
                'language_id' => 2,
                'name' => 'Crear el primer servicio',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 11,
                'todo_task_id' => 10,
                'language_id' => 2,
                'name' => 'Cargar imágenes',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 12,
                'todo_task_id' => 11,
                'language_id' => 2,
                'name' => 'Agregar disponibilidad',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 13,
                'todo_task_id' => 12,
                'language_id' => 2,
                'name' => 'Establecer precios',
                'description' => NULL,
            ),
        ));
        
        
    }
}