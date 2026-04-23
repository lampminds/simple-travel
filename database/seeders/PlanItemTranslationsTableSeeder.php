<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanItemTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('plan_feature_translations')->delete();

        \DB::table('plan_feature_translations')->insert(array (
            0 =>
            array (
                'id' => 1,
                'plan_feature_id' => 1,
                'language_id' => 2,
                'text' => 'Gestión operativa integral',
            ),
            1 =>
            array (
                'id' => 6,
                'plan_feature_id' => 2,
                'language_id' => 2,
                'text' => 'Reservas',
            ),
            2 =>
            array (
                'id' => 7,
                'plan_feature_id' => 3,
                'language_id' => 2,
                'text' => 'Servicios individuales y paquetes',
            ),
            3 =>
            array (
                'id' => 8,
                'plan_feature_id' => 4,
                'language_id' => 2,
                'text' => 'Calendario operativo',
            ),
            4 =>
            array (
                'id' => 9,
                'plan_feature_id' => 5,
                'language_id' => 2,
                'text' => 'Estados de reserva',
            ),
            5 =>
            array (
                'id' => 10,
                'plan_feature_id' => 6,
                'language_id' => 2,
                'text' => 'Gestión de clientes y prestadores',
            ),
            6 =>
            array (
                'id' => 11,
                'plan_feature_id' => 7,
                'language_id' => 2,
                'text' => 'Base de pasajeros',
            ),
            7 =>
            array (
                'id' => 12,
                'plan_feature_id' => 8,
                'language_id' => 2,
                'text' => 'Tarifarios',
            ),
            8 =>
            array (
                'id' => 13,
                'plan_feature_id' => 9,
                'language_id' => 2,
                'text' => 'Comisiones',
            ),
            9 =>
            array (
                'id' => 14,
                'plan_feature_id' => 10,
                'language_id' => 2,
                'text' => 'Historial de operaciones',
            ),
            10 =>
            array (
                'id' => 15,
                'plan_feature_id' => 11,
                'language_id' => 2,
                'text' => 'Gestión financiera básica',
            ),
            11 =>
            array (
                'id' => 16,
                'plan_feature_id' => 12,
                'language_id' => 2,
                'text' => 'Cuentas corrientes',
            ),
            12 =>
            array (
                'id' => 17,
                'plan_feature_id' => 13,
                'language_id' => 2,
                'text' => 'Liquidación a prestadores',
            ),
            13 =>
            array (
                'id' => 18,
                'plan_feature_id' => 14,
                'language_id' => 2,
                'text' => 'Control de pagos y cobranzas',
            ),
            14 =>
            array (
                'id' => 21,
                'plan_feature_id' => 17,
                'language_id' => 2,
                'text' => 'Web personalizable',
            ),
            15 =>
            array (
                'id' => 22,
                'plan_feature_id' => 18,
                'language_id' => 2,
                'text' => 'Publicación automática de productos',
            ),
            16 =>
            array (
                'id' => 27,
                'plan_feature_id' => 23,
                'language_id' => 2,
                'text' => 'Pipeline de ventas',
            ),
            17 =>
            array (
                'id' => 28,
                'plan_feature_id' => 16,
                'language_id' => 2,
                'text' => 'Website',
            ),
            18 =>
            array (
                'id' => 29,
                'plan_feature_id' => 24,
                'language_id' => 2,
                'text' => 'Motor de reservas',
            ),
            19 =>
            array (
                'id' => 30,
                'plan_feature_id' => 19,
                'language_id' => 2,
                'text' => 'Motor de reservas online',
            ),
            20 =>
            array (
                'id' => 31,
                'plan_feature_id' => 20,
                'language_id' => 2,
                'text' => 'Integración con pasarela de pago',
            ),
            21 =>
            array (
                'id' => 32,
                'plan_feature_id' => 21,
                'language_id' => 2,
                'text' => 'Conexión directa con el backoffice',
            ),
            22 =>
            array (
                'id' => 33,
                'plan_feature_id' => 25,
                'language_id' => 2,
                'text' => 'Seguimiento de leads',
            ),
            23 =>
            array (
                'id' => 34,
                'plan_feature_id' => 26,
                'language_id' => 2,
                'text' => 'Recordatorios automáticos',
            ),
            24 =>
            array (
                'id' => 35,
                'plan_feature_id' => 27,
                'language_id' => 2,
                'text' => 'Historial de propuestas',
            ),
            25 =>
            array (
                'id' => 36,
                'plan_feature_id' => 28,
                'language_id' => 2,
                'text' => 'Reportes de rentabilidad',
            ),
            26 =>
            array (
                'id' => 37,
                'plan_feature_id' => 29,
                'language_id' => 2,
                'text' => 'Margen por producto',
            ),
            27 =>
            array (
                'id' => 38,
                'plan_feature_id' => 30,
                'language_id' => 2,
                'text' => 'Proyección de flujo de caja',
            ),
            28 =>
            array (
                'id' => 39,
                'plan_feature_id' => 31,
                'language_id' => 2,
                'text' => 'Exportación contable',
            ),
            29 =>
            array (
                'id' => 40,
                'plan_feature_id' => 32,
                'language_id' => 2,
                'text' => 'Automatización',
            ),
            30 =>
            array (
                'id' => 41,
                'plan_feature_id' => 33,
                'language_id' => 2,
                'text' => 'Confirmaciones automáticas',
            ),
            31 =>
            array (
                'id' => 42,
                'plan_feature_id' => 34,
                'language_id' => 2,
                'text' => 'Envío automático de vouchers y/o documentación',
            ),
            32 =>
            array (
                'id' => 43,
                'plan_feature_id' => 35,
                'language_id' => 2,
                'text' => 'Recordatorios pre-viaje',
            ),
            33 =>
            array (
                'id' => 44,
                'plan_feature_id' => 15,
                'language_id' => 2,
                'text' => 'Globalización',
            ),
            34 =>
            array (
                'id' => 45,
                'plan_feature_id' => 36,
                'language_id' => 2,
                'text' => 'Multi-moneda',
            ),
            35 =>
            array (
                'id' => 46,
                'plan_feature_id' => 37,
                'language_id' => 2,
                'text' => 'Multi-idioma',
            ),
            36 =>
            array (
                'id' => 47,
                'plan_feature_id' => 38,
                'language_id' => 2,
                'text' => 'Acceso para clientes (agencias)',
            ),
            37 =>
            array (
                'id' => 48,
                'plan_feature_id' => 39,
                'language_id' => 2,
                'text' => 'Consulta de disponibilidad',
            ),
            38 =>
            array (
                'id' => 49,
                'plan_feature_id' => 40,
                'language_id' => 2,
                'text' => 'Descarga de vouchers',
            ),
            39 =>
            array (
                'id' => 50,
                'plan_feature_id' => 41,
                'language_id' => 2,
                'text' => 'Solicitud de reservas',
            ),
            40 =>
            array (
                'id' => 51,
                'plan_feature_id' => 42,
                'language_id' => 2,
                'text' => 'Tarifas diferenciadas',
            ),
        ));


    }
}
