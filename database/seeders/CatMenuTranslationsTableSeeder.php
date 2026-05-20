<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatMenuTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_menu_translations')->delete();
        
        \DB::table('cat_menu_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'menu_id' => 2,
                'language_id' => 2,
                'name' => 'Relaciones',
                'tip' => 'Encuentre aquí sus vínculos con otros usuarios de simple Travel',
            ),
            1 => 
            array (
                'id' => 2,
                'menu_id' => 1,
                'language_id' => 2,
                'name' => 'Panel',
                'tip' => 'Su panel de control principal',
            ),
            2 => 
            array (
                'id' => 3,
                'menu_id' => 3,
                'language_id' => 2,
                'name' => 'Catálogo',
                'tip' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'menu_id' => 4,
                'language_id' => 2,
                'name' => 'Operaciones',
                'tip' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'menu_id' => 5,
                'language_id' => 2,
                'name' => 'Finanzas',
                'tip' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'menu_id' => 6,
                'language_id' => 2,
                'name' => 'Integraciones',
                'tip' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'menu_id' => 7,
                'language_id' => 2,
                'name' => 'Disponibilidad',
                'tip' => NULL,
            ),
            7 => 
            array (
                'id' => 9,
                'menu_id' => 9,
                'language_id' => 2,
                'name' => 'Reservas',
                'tip' => NULL,
            ),
            8 => 
            array (
                'id' => 10,
                'menu_id' => 10,
                'language_id' => 2,
                'name' => 'Clientes',
                'tip' => NULL,
            ),
            9 => 
            array (
                'id' => 11,
                'menu_id' => 11,
                'language_id' => 2,
                'name' => 'Invitar empleados',
                'tip' => NULL,
            ),
            10 => 
            array (
                'id' => 12,
                'menu_id' => 12,
                'language_id' => 2,
                'name' => 'Invitar empresas',
                'tip' => NULL,
            ),
            11 => 
            array (
                'id' => 13,
                'menu_id' => 13,
                'language_id' => 2,
                'name' => 'Gestionar invitaciones',
                'tip' => NULL,
            ),
            12 => 
            array (
                'id' => 14,
                'menu_id' => 14,
                'language_id' => 2,
                'name' => 'Sitio web',
                'tip' => NULL,
            ),
            13 => 
            array (
                'id' => 15,
                'menu_id' => 15,
                'language_id' => 2,
                'name' => 'Configuración del sitio',
                'tip' => NULL,
            ),
            14 => 
            array (
                'id' => 16,
                'menu_id' => 16,
                'language_id' => 2,
                'name' => 'Ver sitio web',
                'tip' => NULL,
            ),
            15 => 
            array (
                'id' => 17,
                'menu_id' => 17,
                'language_id' => 2,
                'name' => 'Listas de Precios',
                'tip' => NULL,
            ),
            16 => 
            array (
                'id' => 18,
                'menu_id' => 18,
                'language_id' => 2,
                'name' => 'Relaciones comerciales',
                'tip' => NULL,
            ),
            17 => 
            array (
                'id' => 19,
                'menu_id' => 20,
                'language_id' => 1,
                'name' => 'Services',
                'tip' => NULL,
            ),
            18 => 
            array (
                'id' => 20,
                'menu_id' => 20,
                'language_id' => 2,
                'name' => 'Servicios',
                'tip' => NULL,
            ),
            19 => 
            array (
                'id' => 21,
                'menu_id' => 19,
                'language_id' => 2,
                'name' => 'Vehículos',
                'tip' => NULL,
            ),
            20 => 
            array (
                'id' => 24,
                'menu_id' => 17,
                'language_id' => 1,
                'name' => 'Pricing',
                'tip' => NULL,
            ),
            21 => 
            array (
                'id' => 25,
                'menu_id' => 17,
                'language_id' => 3,
                'name' => 'Listas de Preços',
                'tip' => NULL,
            ),
            22 => 
            array (
                'id' => 27,
                'menu_id' => 22,
                'language_id' => 1,
                'name' => 'Provider prices',
                'tip' => NULL,
            ),
            23 => 
            array (
                'id' => 28,
                'menu_id' => 22,
                'language_id' => 2,
                'name' => 'Precios a operadores',
                'tip' => NULL,
            ),
            24 => 
            array (
                'id' => 29,
                'menu_id' => 23,
                'language_id' => 1,
                'name' => 'Operator prices',
                'tip' => NULL,
            ),
            25 => 
            array (
                'id' => 30,
                'menu_id' => 23,
                'language_id' => 2,
                'name' => 'Precios comerciales',
                'tip' => NULL,
            ),
            26 => 
            array (
                'id' => 31,
                'menu_id' => 24,
                'language_id' => 1,
                'name' => 'Packages',
                'tip' => NULL,
            ),
            27 => 
            array (
                'id' => 32,
                'menu_id' => 24,
                'language_id' => 2,
                'name' => 'Paquetes',
                'tip' => NULL,
            ),
        ));
        
        
    }
}