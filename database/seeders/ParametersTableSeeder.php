<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ParametersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('parameters')->delete();

        //pa iseed --force --exclude=created_at,updated_at,created_by,updated_by parameters

        \DB::table('parameters')->insert(array (
            0 =>
            array (
                'id' => 1,
                'category' => 'website',
                'code' => 'meta_title',
                'type_id' => 0,
                'value' => 'SimpleTravel | Sistema de Gestión de Reservas Turísticas',
                'mode_id' => 0,
            'help' => 'The meta title of a website page by default (if none provided)',
                'comments' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'category' => 'website',
                'code' => 'meta_keywords',
                'type_id' => 0,
                'value' => 'turismo, viajes, operadores, software operadores, agentes de turismo',
                'mode_id' => 0,
            'help' => 'The meta keywords of a website page by default (if none provided)',
                'comments' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'category' => 'emails',
                'code' => 'notification_sender',
                'type_id' => 0,
                'value' => 'soporte@simple-travel.net',
                'mode_id' => 1,
                'help' => 'Sender for automatic notifications',
                'comments' => NULL,
            ),
        ));


    }
}
