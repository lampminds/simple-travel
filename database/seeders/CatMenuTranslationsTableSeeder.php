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
                'language_id' => 1,
                'name' => 'Provider services',
                'tip' => 'Review incoming service proposals from linked providers.',
            ),
            7 => 
            array (
                'id' => 9,
                'menu_id' => 9,
                'language_id' => 1,
                'name' => 'Reservations',
                'tip' => 'Book packages enabled by linked operators.',
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
                'id' => 14,
                'menu_id' => 14,
                'language_id' => 2,
                'name' => 'Sitio web',
                'tip' => NULL,
            ),
            12 => 
            array (
                'id' => 15,
                'menu_id' => 15,
                'language_id' => 2,
                'name' => 'Configuración del sitio',
                'tip' => NULL,
            ),
            13 => 
            array (
                'id' => 16,
                'menu_id' => 16,
                'language_id' => 2,
                'name' => 'Ver sitio web',
                'tip' => NULL,
            ),
            14 => 
            array (
                'id' => 17,
                'menu_id' => 17,
                'language_id' => 2,
                'name' => 'Listas de Precios',
                'tip' => NULL,
            ),
            15 => 
            array (
                'id' => 18,
                'menu_id' => 18,
                'language_id' => 2,
                'name' => 'Relaciones comerciales',
                'tip' => NULL,
            ),
            16 => 
            array (
                'id' => 19,
                'menu_id' => 20,
                'language_id' => 1,
                'name' => 'Services',
                'tip' => NULL,
            ),
            17 => 
            array (
                'id' => 20,
                'menu_id' => 20,
                'language_id' => 2,
                'name' => 'Servicios',
                'tip' => NULL,
            ),
            18 => 
            array (
                'id' => 21,
                'menu_id' => 19,
                'language_id' => 2,
                'name' => 'Vehículos',
                'tip' => NULL,
            ),
            19 => 
            array (
                'id' => 24,
                'menu_id' => 17,
                'language_id' => 1,
                'name' => 'Pricing',
                'tip' => NULL,
            ),
            20 => 
            array (
                'id' => 25,
                'menu_id' => 17,
                'language_id' => 3,
                'name' => 'Listas de Preços',
                'tip' => NULL,
            ),
            21 => 
            array (
                'id' => 27,
                'menu_id' => 22,
                'language_id' => 1,
                'name' => 'Provider prices',
                'tip' => NULL,
            ),
            22 => 
            array (
                'id' => 28,
                'menu_id' => 22,
                'language_id' => 2,
                'name' => 'Precios a operadores',
                'tip' => NULL,
            ),
            23 => 
            array (
                'id' => 29,
                'menu_id' => 23,
                'language_id' => 1,
                'name' => 'Operator prices',
                'tip' => NULL,
            ),
            24 => 
            array (
                'id' => 30,
                'menu_id' => 23,
                'language_id' => 2,
                'name' => 'Precios comerciales',
                'tip' => NULL,
            ),
            25 => 
            array (
                'id' => 31,
                'menu_id' => 24,
                'language_id' => 1,
                'name' => 'Commercial packages',
                'tip' => 'Compose packages from accepted provider services.',
            ),
            26 => 
            array (
                'id' => 32,
                'menu_id' => 24,
                'language_id' => 2,
                'name' => 'Paquetes comerciales',
                'tip' => 'Armá paquetes a partir de los servicios de prestadores que aceptaste.',
            ),
            27 => 
            array (
                'id' => 33,
                'menu_id' => 25,
                'language_id' => 1,
                'name' => 'To Be Developed...',
                'tip' => NULL,
            ),
            28 => 
            array (
                'id' => 34,
                'menu_id' => 25,
                'language_id' => 2,
                'name' => 'A desarrollar...',
                'tip' => NULL,
            ),
            29 => 
            array (
                'id' => 35,
                'menu_id' => 25,
                'language_id' => 3,
                'name' => 'A desarrollar...',
                'tip' => NULL,
            ),
            30 => 
            array (
                'id' => 36,
                'menu_id' => 7,
                'language_id' => 2,
                'name' => 'Servicios de prestadores',
                'tip' => 'Revisá propuestas pendientes de servicios de prestadores vinculados.',
            ),
            31 => 
            array (
                'id' => 37,
                'menu_id' => 9,
                'language_id' => 2,
                'name' => 'Reservas',
                'tip' => 'Reservá paquetes que ya aceptaste de operadores vinculados.',
            ),
            32 => 
            array (
                'id' => 38,
                'menu_id' => 26,
                'language_id' => 1,
                'name' => 'Offers to operators',
                'tip' => 'Propose catalog variants to linked operators.',
            ),
            33 => 
            array (
                'id' => 39,
                'menu_id' => 26,
                'language_id' => 2,
                'name' => 'Ofertas a operadores',
                'tip' => 'Proponé variantes de tu catálogo a operadores vinculados.',
            ),
            34 => 
            array (
                'id' => 40,
                'menu_id' => 27,
                'language_id' => 1,
                'name' => 'Offers to agencies',
                'tip' => 'Propose commercial packages to linked agencies.',
            ),
            35 => 
            array (
                'id' => 41,
                'menu_id' => 27,
                'language_id' => 2,
                'name' => 'Ofertas a agencias',
                'tip' => 'Proponé paquetes comerciales a agencias vinculadas.',
            ),
            36 => 
            array (
                'id' => 42,
                'menu_id' => 28,
                'language_id' => 1,
                'name' => 'Operator offers',
                'tip' => 'Review pending package proposals from linked operators.',
            ),
            37 => 
            array (
                'id' => 43,
                'menu_id' => 28,
                'language_id' => 2,
                'name' => 'Ofertas de operadores',
                'tip' => 'Revisá propuestas pendientes de paquetes de operadores vinculados.',
            ),
            38 => 
            array (
                'id' => 44,
                'menu_id' => 29,
                'language_id' => 1,
                'name' => 'Reservations',
                'tip' => 'Review and confirm bookings submitted by linked agencies.',
            ),
            39 => 
            array (
                'id' => 45,
                'menu_id' => 29,
                'language_id' => 2,
                'name' => 'Reservas',
                'tip' => 'Revisá y confirmá reservas enviadas por agencias vinculadas.',
            ),
            40 => 
            array (
                'id' => 46,
                'menu_id' => 30,
                'language_id' => 1,
                'name' => 'Capacity allocations',
                'tip' => 'Assign inventory caps to linked operators for accepted catalog variants.',
            ),
            41 => 
            array (
                'id' => 47,
                'menu_id' => 30,
                'language_id' => 2,
                'name' => 'Asignación de cupos',
                'tip' => 'Asigná cupos a operadores vinculados para variantes de catálogo ya aceptadas.',
            ),
            42 => 
            array (
                'id' => 48,
                'menu_id' => 31,
                'language_id' => 1,
                'name' => 'Variant availability',
                'tip' => 'Recurring rules and date exceptions for catalog variant inventory.',
            ),
            43 => 
            array (
                'id' => 49,
                'menu_id' => 31,
                'language_id' => 2,
                'name' => 'Disponibilidad de variantes',
                'tip' => 'Reglas recurrentes y excepciones por fecha para el inventario de variantes.',
            ),
            44 => 
            array (
                'id' => 50,
                'menu_id' => 32,
                'language_id' => 1,
                'name' => 'Package availability',
                'tip' => 'Recurring rules and date exceptions for operator package inventory.',
            ),
            45 => 
            array (
                'id' => 51,
                'menu_id' => 32,
                'language_id' => 2,
                'name' => 'Disponibilidad de paquetes',
                'tip' => 'Reglas recurrentes y excepciones por fecha para el inventario de paquetes.',
            ),
            46 => 
            array (
                'id' => 52,
                'menu_id' => 33,
                'language_id' => 1,
                'name' => 'Package capacity allocations',
                'tip' => 'Assign inventory caps to linked agencies for accepted operator packages.',
            ),
            47 => 
            array (
                'id' => 53,
                'menu_id' => 33,
                'language_id' => 2,
                'name' => 'Asignación de cupos de paquetes',
                'tip' => 'Asigná cupos a agencias vinculadas para paquetes del operador ya aceptados.',
            ),
            48 => 
            array (
                'id' => 54,
                'menu_id' => 34,
                'language_id' => 1,
                'name' => 'Services by package',
                'tip' => 'See which commercial packages include each accepted provider service.',
            ),
            49 => 
            array (
                'id' => 55,
                'menu_id' => 34,
                'language_id' => 2,
                'name' => 'Servicios por paquete',
                'tip' => 'Consultá en qué paquetes comerciales está incluido cada servicio de prestador aceptado.',
            ),
        ));
        
        
    }
}