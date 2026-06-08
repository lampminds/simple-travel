<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatBookingStatusTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_booking_status_translations')->delete();
        
        \DB::table('cat_booking_status_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'status_id' => 1,
                'language_id' => 2,
                'name' => 'Pendiente de validar',
                'help_tip' => 'La reserva fue creada y debe validarse antes de iniciar verificaciones de disponibilidad, precios y reglas comerciales.',
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'status_id' => 2,
                'language_id' => 2,
                'name' => 'Verificando disponibilidad',
                'help_tip' => 'Se está verificando la disponibilidad de los componentes de la reserva',
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'status_id' => 3,
                'language_id' => 2,
                'name' => 'Aguardando el pago',
                'help_tip' => 'La reserva no puede continuar hasta cumplirse las condiciones comerciales requeridas.',
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'status_id' => 4,
                'language_id' => 2,
                'name' => 'Aguardando confirmación',
            'help_tip' => 'El operador (y/o prestador) debe confirmar la reserva',
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'status_id' => 5,
                'language_id' => 2,
                'name' => 'Confirmación parcial',
                'help_tip' => 'Algún componente de la reserva aún falta ser confirmado',
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'status_id' => 6,
                'language_id' => 2,
                'name' => 'Confirmado',
                'help_tip' => 'La reserva ha sido confirmada en su totalidad',
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'status_id' => 7,
                'language_id' => 2,
                'name' => 'En progreso',
                'help_tip' => 'El viaje o servicio ya comenzó y se encuentra en ejecución.',
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'status_id' => 8,
                'language_id' => 2,
                'name' => 'Completada',
                'help_tip' => 'La reserva ha sido completada',
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'status_id' => 9,
                'language_id' => 2,
                'name' => 'Anulada',
                'help_tip' => 'La reserva ha sido anulada',
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'status_id' => 10,
                'language_id' => 2,
                'name' => 'Expirada',
                'help_tip' => 'La reserva no ha sido completada a tiempo y ha expirado',
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'status_id' => 11,
                'language_id' => 2,
                'name' => 'Rechazada',
                'help_tip' => 'La reserva fue rechazada por imposibilidad operativa o comercial.',
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'status_id' => 12,
                'language_id' => 2,
                'name' => 'Borrador',
                'help_tip' => 'Item de reserva en desarrollo',
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'status_id' => 13,
                'language_id' => 2,
                'name' => 'Pendiente',
                'help_tip' => 'El item está pendiente de ser procesado',
                'description' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'status_id' => 14,
                'language_id' => 2,
                'name' => 'Solicitado',
                'help_tip' => 'Se envió la solicitud al operador/prestador',
                'description' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'status_id' => 15,
                'language_id' => 2,
                'name' => 'Retenido',
                'help_tip' => 'El operador/prestador mantiene el item en revisión o pendiente de resolución',
                'description' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'status_id' => 16,
                'language_id' => 2,
                'name' => 'Confirmado',
                'help_tip' => 'El item ha sido confirmado por el operador/prestador',
                'description' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'status_id' => 17,
                'language_id' => 2,
                'name' => 'Reconfirmado',
                'help_tip' => 'El proveedor volvió a confirmar la disponibilidad o prestación del servicio.',
                'description' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'status_id' => 18,
                'language_id' => 2,
                'name' => 'Cancelado',
                'help_tip' => 'El item ha sido cancelado y ya no será prestado.',
                'description' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'status_id' => 19,
                'language_id' => 2,
                'name' => 'Rechazado',
                'help_tip' => 'El item ha sido rechazado por el operador/prestador',
                'description' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'status_id' => 20,
                'language_id' => 2,
                'name' => 'Fallido',
                'help_tip' => 'Se intentó procesar el item pero ocurrió un error que impidió su confirmación.',
                'description' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'status_id' => 21,
                'language_id' => 2,
                'name' => 'Expirado',
                'help_tip' => 'El item ha expirado',
                'description' => NULL,
            ),
        ));
        
        
    }
}