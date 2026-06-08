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
                'id' => 4,
                'todo_task_id' => 4,
                'language_id' => 2,
                'name' => 'Agregar disponibilidad',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 5,
                'todo_task_id' => 5,
                'language_id' => 2,
                'name' => 'Establecer precios',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 6,
                'todo_task_id' => 6,
                'language_id' => 2,
                'name' => 'Cargar imágenes',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 15,
                'todo_task_id' => 1,
                'language_id' => 2,
                'name' => 'Completar el perfil de la empresa',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 16,
                'todo_task_id' => 3,
                'language_id' => 2,
                'name' => 'Completar el perfil de usuario',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 19,
                'todo_task_id' => 2,
                'language_id' => 2,
                'name' => 'Crear el primer servicio',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 20,
                'todo_task_id' => 7,
                'language_id' => 2,
                'name' => 'Completar el perfil de la empresa',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 21,
                'todo_task_id' => 8,
                'language_id' => 2,
                'name' => 'Completar el perfil de usuario',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 22,
                'todo_task_id' => 9,
                'language_id' => 2,
                'name' => 'Crear el primer servicio',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 23,
                'todo_task_id' => 10,
                'language_id' => 2,
                'name' => 'Cargar imágenes',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 24,
                'todo_task_id' => 11,
                'language_id' => 2,
                'name' => 'Agregar disponibilidad',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 25,
                'todo_task_id' => 12,
                'language_id' => 2,
                'name' => 'Establecer precios',
                'description' => NULL,
            ),
        ));
        
        
    }
}