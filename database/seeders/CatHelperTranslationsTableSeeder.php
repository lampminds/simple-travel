<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatHelperTranslationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_helper_translations')->delete();
        
        \DB::table('cat_helper_translations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'helper_id' => 1,
                'language_id' => 2,
                'text' => '<p>Ejemplos de servicio gastronómico:</p><ul><li><p>Pizza napolitana</p></li><li><p>Plato de fideos</p></li><li><p>Menú turístico</p></li><li><p>Hamburguesa completa</p></li><li><p>Desayuno buffet</p></li><li><p>Cena show</p></li></ul><p>Para ver ejemplos de variantes, vea la ayuda en las variantes.</p>',
            ),
            1 => 
            array (
                'id' => 2,
                'helper_id' => 2,
                'language_id' => 2,
                'text' => '<p>Servicio: Pizza mozzarella</p><p>Variantes:</p><ul><li><p>chica</p></li><li><p>mediana</p></li><li><p>grande</p></li></ul><p>Servicio: Menú turístico</p><p>Variantes:</p><ul><li><p>solo menú</p></li><li><p>menú + bebida</p></li><li><p>menú + postre</p></li><li><p>menú completo</p></li></ul>',
            ),
            2 => 
            array (
                'id' => 3,
                'helper_id' => 3,
                'language_id' => 1,
            'text' => '<p>Optional reference price for this variant, in the currency you select below.</p><ul><li><p>If you assign a <strong>provider price list</strong> to an operator, the list line (fixed amount or percentage) defines what they see when you offer the service.</p></li><li><p>Leave empty when pricing comes only from a fixed amount on the list.</p></li><li><p>Percentage list lines need a base price here to calculate the amount.</p></li></ul>',
            ),
            3 => 
            array (
                'id' => 4,
                'helper_id' => 3,
                'language_id' => 2,
            'text' => '<p>Precio de referencia opcional para esta variante, en la moneda que elijas abajo.</p><ul><li><p>Si asignás una <strong>lista de precios de prestador</strong> a un operador, la línea de la lista (monto fijo o porcentaje) define lo que ve al ofrecer el servicio.</p></li><li><p>Dejalo vacío si el precio sale solo de un monto fijo en la lista.</p></li><li><p>Las líneas en porcentaje necesitan precio base aquí para calcular el importe.</p></li></ul>',
            ),
            4 => 
            array (
                'id' => 5,
                'helper_id' => 3,
                'language_id' => 3,
            'text' => '<p>Preço de referência opcional para esta variante, na moeda que escolher abaixo.</p><ul><li><p>Se atribuir uma <strong>lista de preços de prestador</strong> a um operador, a linha da lista (valor fixo ou percentagem) define o que ele vê ao oferecer o serviço.</p></li><li><p>Deixe vazio quando o preço vier apenas de um valor fixo na lista.</p></li><li><p>Linhas em percentagem precisam de preço base aqui para calcular o valor.</p></li></ul>',
            ),
            5 => 
            array (
                'id' => 6,
                'helper_id' => 4,
                'language_id' => 1,
            'text' => '<p>Currency for the base price above and for percentage-based list lines.</p><ul><li><p>When an operator has an assigned price list, the <strong>list currency</strong> is used for the price shown in catalog offers (even if this field shows another code).</p></li><li><p>Choose the currency that matches your usual base pricing or your list, so amounts stay consistent.</p></li></ul>',
            ),
            6 => 
            array (
                'id' => 7,
                'helper_id' => 4,
                'language_id' => 2,
            'text' => '<p>Moneda del precio base de arriba y de las líneas de lista en porcentaje.</p><ul><li><p>Cuando el operador tiene una lista asignada, prevalece la <strong>moneda de la lista</strong> en el precio que ve al ofrecer el servicio (aunque aquí figure otro código).</p></li><li><p>Elige la moneda que coincida con tu precio base habitual o con tu lista, para que los importes sean coherentes.</p></li></ul>',
            ),
            7 => 
            array (
                'id' => 8,
                'helper_id' => 4,
                'language_id' => 3,
            'text' => '<p>Moeda do preço base acima e das linhas de lista em percentagem.</p><ul><li><p>Quando o operador tem uma lista atribuída, prevalece a <strong>moeda da lista</strong> no preço mostrado ao oferecer o serviço (mesmo que aqui apareça outro código).</p></li><li><p>Escolha a moeda alinhada ao seu preço base habitual ou à lista, para manter os valores consistentes.</p></li></ul>',
            ),
            8 => 
            array (
                'id' => 9,
                'helper_id' => 5,
                'language_id' => 2,
            'text' => '<p>Si el servicio tiene un tiempo determinado de duración, indíquelo aquí (en minutos)</p>',
            ),
            9 => 
            array (
                'id' => 10,
                'helper_id' => 6,
                'language_id' => 2,
            'text' => '<p>Si puede estimar el tiempo (en horas) que necesita para procesar una confirmación de reserva, indíquelo aquí. De esta forma, Simple-travel podrá mostrar sus tareas pendientes por orden de prioridad.</p>',
            ),
            10 => 
            array (
                'id' => 12,
                'helper_id' => 8,
                'language_id' => 2,
                'text' => '<p>Destaca este servicio en listados, búsquedas y módulos promocionales donde se muestren servicios destacados.</p>',
            ),
            11 => 
            array (
                'id' => 13,
                'helper_id' => 9,
                'language_id' => 2,
            'text' => '<p>Marque esta opción para que este servicio aparezca en su sitio web (si ha contratado el módulo web)</p>',
            ),
            12 => 
            array (
                'id' => 14,
                'helper_id' => 13,
                'language_id' => 2,
            'text' => '<p>Por lo general, a menos que se disponga de diferentes modalidades de alojamiento, un prestador carga un servicio (Habitaciones estandar) y variantes (doble, triple, cuádruple). Otro servicio diferente podría ser una suite nupcial.</p><p>Otro ejemplo podría ser, si es un alojamiento en la naturaleza: servicio de dormis (variantes pax 4, pax 6), servicio de parcelas para camping.</p><p>Para ver ejemplos de variantes, vea la ayuda en las variantes.</p>',
            ),
            13 => 
            array (
                'id' => 15,
                'helper_id' => 14,
                'language_id' => 2,
            'text' => '<p>Las variantes en alojamiento son típicamente los diferentes tipos de habitación disponible (single, triple, etc) dentro de un servicio similar.</p><p>Si tu hotel tiene diferentes areas o secciones donde las habitaciones tengan comodidades diferentes (por ejemplo: habitaciones estandar o premium) entonces definelas en servicios aparte.</p>',
            ),
            14 => 
            array (
                'id' => 16,
                'helper_id' => 15,
                'language_id' => 1,
            'text' => '<p>The status controls whether this service can be offered and sold:</p><ul><li><strong>Active:</strong> the service is enabled. Only active services (with active variants) can be offered to operators and sold.</li><li><strong>On hold:</strong> the initial status while you complete the wizard. It is not offered or sold.</li><li><strong>Suspended:</strong> a temporary pause. It is not offered or sold, but you can reactivate it at any time.</li><li><strong>Discontinued:</strong> the service is no longer offered for good; it is left out of the pickers used to offer services to operators. Its history is kept.</li><li><strong>Inactive:</strong> the service is switched off, with no commercial effect. Similar to suspended, for services not currently in use.</li><li><strong>Terminated:</strong> a definitive closure. On top of not being sold, it disappears from the usual catalog list (it only shows when filtering by this status).</li></ul><p>Suspending or terminating a service does not delete it or its history.</p>',
            ),
            15 => 
            array (
                'id' => 17,
                'helper_id' => 15,
                'language_id' => 2,
            'text' => '<p>El estado define si el servicio puede ofrecerse y venderse:</p><ul><li><strong>Activo:</strong> el servicio está habilitado. Solo los servicios activos (con variantes activas) pueden ofrecerse a operadores y venderse.</li><li><strong>En espera:</strong> estado inicial mientras completas el asistente. No se ofrece ni se vende.</li><li><strong>Suspendido:</strong> pausa temporal. No se ofrece ni se vende, pero puedes reactivarlo en cualquier momento.</li><li><strong>Descontinuado:</strong> el servicio dejó de ofrecerse de forma definitiva; queda fuera de los selectores para ofrecer servicios a operadores. Se conserva el historial.</li><li><strong>Inactivo:</strong> servicio apagado, sin efecto comercial. Similar a suspendido, para servicios que no están en uso.</li><li><strong>Dado de baja:</strong> baja definitiva. Además de no venderse, desaparece del listado habitual del catálogo (solo se ve al filtrar por este estado).</li></ul><p>Suspender o dar de baja un servicio no lo borra ni elimina su historial.</p>',
            ),
            16 => 
            array (
                'id' => 18,
                'helper_id' => 15,
                'language_id' => 3,
            'text' => '<p>O status define se o serviço pode ser oferecido e vendido:</p><ul><li><strong>Ativo:</strong> o serviço está habilitado. Somente serviços ativos (com variantes ativas) podem ser oferecidos a operadores e vendidos.</li><li><strong>Em espera:</strong> status inicial enquanto você conclui o assistente. Não é oferecido nem vendido.</li><li><strong>Suspenso:</strong> pausa temporária. Não é oferecido nem vendido, mas você pode reativá-lo a qualquer momento.</li><li><strong>Descontinuado:</strong> o serviço deixou de ser oferecido de forma definitiva; fica fora dos seletores para oferecer serviços a operadores. O histórico é mantido.</li><li><strong>Inativo:</strong> serviço desligado, sem efeito comercial. Semelhante a suspenso, para serviços que não estão em uso.</li><li><strong>Encerrado:</strong> baixa definitiva. Além de não ser vendido, desaparece da lista habitual do catálogo (só aparece ao filtrar por este status).</li></ul><p>Suspender ou encerrar um serviço não o exclui nem apaga seu histórico.</p>',
            ),
        ));
        
        
    }
}