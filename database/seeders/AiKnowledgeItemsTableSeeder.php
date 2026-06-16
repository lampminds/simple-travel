<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeItem;
use App\Models\AiKnowledgeTranslation;
use Illuminate\Database\Seeder;

/**
 * Seeds client-facing AI knowledge base articles (website account area).
 *
 * Development note: only Spanish (neutral Latin American) is seeded to save embedding API tokens.
 * Add EN/PT later via Filament or extend this seeder when the assistant is validated.
 *
 * Run standalone (does not call OpenAI — seed content only):
 *   php artisan db:seed --class=AiKnowledgeItemsTableSeeder
 *
 * Then generate embeddings:
 *   php artisan ai:knowledge:embed
 *
 * Safe to re-run: items matched by key; removes non-Spanish translations for seeded items.
 *
 * @see docs/client-knowledge-base.md
 */
class AiKnowledgeItemsTableSeeder extends Seeder
{
    /** cat_languages.id — Spanish content in neutral Latin American register */
    private const LANG_ES = 2;

    public function run(): void
    {
        foreach ($this->articles() as $article) {
            $item = AiKnowledgeItem::query()->updateOrCreate(
                ['key' => $article['key']],
                ['is_active' => $article['is_active'] ?? true],
            );

            AiKnowledgeTranslation::withoutEvents(function () use ($item, $article): void {
                $item->translations()->updateOrCreate(
                    ['language_id' => self::LANG_ES],
                    [
                        'title' => $article['title'],
                        'content_short' => $article['content_short'] ?? null,
                        'content' => $article['content'],
                        'url' => $article['url'] ?? null,
                        'tags' => $article['tags'] ?? null,
                    ],
                );

                $item->translations()
                    ->where('language_id', '!=', self::LANG_ES)
                    ->delete();
            });
        }
    }

    /**
     * @return list<array{key: string, title: string, content: string, is_active?: bool, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function articles(): array
    {
        return [
            ...$this->accessArticles(),
            ...$this->conceptArticles(),
            ...$this->providerArticles(),
            ...$this->operatorArticles(),
            ...$this->agencyArticles(),
            ...$this->relationshipArticles(),
        ];
    }

    /**
     * @return list<array{key: string, title: string, content: string, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function accessArticles(): array
    {
        return [
            [
                'key' => 'access_login_forgot_password',
                'title' => '¿Cómo restablezco mi contraseña?',
                'content_short' => 'Usa el enlace de recuperación en la pantalla de inicio de sesión para recibir un correo con instrucciones.',
                'content' => <<<'TXT'
Si no puedes iniciar sesión:

1. Abre la pantalla de inicio de sesión y usa el enlace para recuperar o restablecer la contraseña.
2. Ingresa el correo asociado a tu usuario.
3. Revisa tu bandeja de entrada (y la carpeta de spam) y sigue el enlace del correo.
4. Elige una contraseña nueva e inicia sesión de nuevo.

Si no llega el correo, verifica que la dirección sea la correcta o contacta al administrador de tu empresa.
TXT,
                'tags' => ['acceso', 'login', 'contraseña', 'todos'],
            ],
            [
                'key' => 'access_select_account',
                'title' => '¿Cómo elijo con qué cuenta de empresa operar?',
                'content_short' => 'Si perteneces a más de una empresa, elige la cuenta primero; después el panel (prestador, operador o agencia).',
                'content' => <<<'TXT'
Si tu usuario tiene acceso a más de una empresa:

1. Después de iniciar sesión puede aparecer una pantalla para seleccionar cuenta.
2. Elige la empresa con la que quieres operar hoy.
3. En el panel de cuenta, entra al panel que corresponda a tu rol (Prestador, Operador o Agencia).

Más adelante puedes cambiar de cuenta desde el selector de cuentas en la navegación, cuando esté disponible.
TXT,
                'url' => '/account/dashboard',
                'tags' => ['acceso', 'cuenta', 'todos'],
            ],
            [
                'key' => 'access_select_panel',
                'title' => '¿Qué diferencia hay entre el panel Prestador, Operador y Agencia?',
                'content_short' => 'Cada panel muestra menús y tareas según el rol comercial de tu empresa en la cuenta.',
                'content' => <<<'TXT'
En el panel de cuenta eliges cómo operar:

- **Panel Prestador:** para empresas que publican servicios (hoteles, traslados, actividades, etc.) y los proponen a operadores.
- **Panel Operador:** para operadores turísticos que aceptan servicios de prestadores, arman paquetes, fijan precios a agencias y gestionan reservas.
- **Panel Agencia:** para agencias que aceptan paquetes del operador y reservan para sus clientes.

Si tu empresa es prestador y operador a la vez, verás ambas opciones. Elige la que corresponda a lo que necesitas hacer ahora.
TXT,
                'url' => '/account/dashboard',
                'tags' => ['acceso', 'prestador', 'operador', 'agencia', 'todos'],
            ],
            [
                'key' => 'access_tasks_onboarding',
                'title' => '¿Qué son las tareas de la cuenta?',
                'content_short' => 'Las tareas son listas guiadas asignadas a tu cuenta para ayudarte a completar pasos clave de configuración.',
                'content' => <<<'TXT'
**Tareas** es una sección de tu cuenta con pasos recomendados, agrupados por categoría (por ejemplo: catálogo, relaciones, precios).

Úsala como guía de inicio:
- Abre **Tareas** desde el menú de la cuenta.
- Revisa cada lista y marca los pasos a medida que los completes.
- Algunos pasos incluyen enlaces directos a la pantalla correspondiente.

Si no ves tareas, puede que tu cuenta aún no tenga listas configuradas.
TXT,
                'url' => '/account/tasks',
                'tags' => ['acceso', 'tareas', 'onboarding', 'todos'],
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, content: string, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function conceptArticles(): array
    {
        return [
            [
                'key' => 'concepts_provider_operator_agency',
                'title' => '¿Qué es un prestador, un operador y una agencia?',
                'content_short' => 'El prestador ofrece servicios; el operador arma paquetes y vende a agencias; la agencia reserva para el cliente final.',
                'content' => <<<'TXT'
En la plataforma, tres roles comerciales trabajan en cadena:

**Prestador** — tiene servicios (habitaciones, traslados, excursiones, gastronomía, etc.), los mantiene en el catálogo, define listas de precios de prestador y propone servicios a operadores vinculados.

**Operador** — se vincula con prestadores, acepta sus propuestas, arma paquetes comerciales, define listas de precios de operador para agencias y confirma o gestiona reservas.

**Agencia** — se vincula con operadores, acepta propuestas de paquetes y genera reservas para viajeros.

Flujo típico: prestador publica un servicio → operador lo acepta → operador ofrece un paquete → agencia acepta y reserva.
TXT,
                'tags' => ['conceptos', 'prestador', 'operador', 'agencia', 'todos'],
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, content: string, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function providerArticles(): array
    {
        return [
            [
                'key' => 'provider_hotel_create_service',
                'title' => '¿Cómo cargo mi hotel como servicio en el sistema?',
                'content_short' => 'Como prestador, crea un servicio Hotel en Catálogo con el asistente, actívalo, vincula operadores y propón disponibilidad de catálogo.',
                'content' => <<<'TXT'
Si tienes un hotel y quieres que operadores comercialicen tus habitaciones:

1. Inicia sesión y entra al panel **Prestador** de tu empresa.
2. En el menú, abre **Catálogo**.
3. En «Crear nuevo servicio», elige **Hotel** (o el tipo de alojamiento que corresponda).
4. Completa el asistente paso a paso:
   - Datos base: nombre, ciudad y descripciones.
   - Estado: cuando esté listo, marca el servicio como activo (necesitas al menos una variante, por ejemplo tipo de habitación).
   - Características, variantes, imágenes y condiciones según cada paso.
   - Opciones avanzadas de hotel: estrellas, horarios de check-in/check-out, etc.
5. Con el servicio activo, vincula un **operador** en **Relaciones comerciales** (debe quedar **aprobada**).
6. Crea una **Lista de precios (prestador)** con tus tarifas y asígnala al operador.
7. En **Disponibilidad de catálogo**, **propón** las variantes que ese operador puede vender. El operador debe **aceptar** cada propuesta.

Hasta que el operador no acepte, tu hotel no entra en su catálogo operativo.
TXT,
                'url' => '/catalog',
                'tags' => ['prestador', 'hotel', 'catálogo', 'onboarding'],
            ],
            [
                'key' => 'provider_catalog_wizard_overview',
                'title' => '¿Cuáles son los pasos del asistente de servicios?',
                'content_short' => 'El asistente del catálogo guía datos base, estado, características, variantes, imágenes, condiciones y opciones según el tipo.',
                'content' => <<<'TXT'
Al crear o editar un servicio en **Catálogo**, un asistente de varios pasos te guía:

1. **Datos base** — nombre, ubicación, descripciones.
2. **Estado** — activo/en pausa, modo de reserva, visibilidad.
3. **Características** — atributos según el tipo de servicio.
4. **Variantes** — opciones vendibles (tipos de habitación, clases de vehículo, etc.). Algunos tipos omiten este paso.
5. **Imágenes** — foto principal y galería.
6. **Condiciones** — políticas y reglas comerciales visibles para socios.
7. **Experiencias** — cuando aplica al tipo de servicio.
8. **Opciones avanzadas** — ajustes del tipo (ej. horarios de hotel, rutas de traslado).

Puedes volver a cualquier paso desde el listado de servicios hasta completar y activar la ficha.
TXT,
                'url' => '/catalog',
                'tags' => ['prestador', 'catálogo', 'asistente'],
            ],
            [
                'key' => 'provider_activate_service',
                'title' => '¿Cómo activo un servicio en el catálogo?',
                'content_short' => 'Completa los pasos obligatorios del asistente y marca el estado como activo; suele requerirse al menos una variante.',
                'content' => <<<'TXT'
Para que un servicio pueda comercializarse con socios:

1. Abre **Catálogo** y elige tu servicio.
2. En el paso **Estado** del asistente, marca el servicio como **activo**.
3. Asegúrate de tener al menos una **variante** (cuando el tipo de servicio usa variantes).
4. Completa los datos obligatorios de pasos anteriores (nombre, ciudad, descripciones).

Los servicios nuevos suelen empezar en pausa hasta terminar la configuración. Si no puedes activar, el asistente indica qué falta (a menudo variantes).
TXT,
                'url' => '/catalog',
                'tags' => ['prestador', 'catálogo', 'estado'],
            ],
            [
                'key' => 'provider_price_list',
                'title' => '¿Cómo creo una lista de precios de prestador?',
                'content_short' => 'Las listas de precios de prestador definen tarifas por servicio o variante y se asignan a operadores vinculados.',
                'content' => <<<'TXT'
Las listas de precios de prestador indican a los operadores cuánto cobras:

1. En el panel Prestador, abre **Listas de precios → Listas de precios (prestador)**.
2. Crea una lista nueva: nombre, moneda y fechas de vigencia.
3. Agrega **ítems** — una línea por servicio completo o por variante, con modo de precio (precio base, monto fijo o porcentaje).
4. Guarda la lista.
5. En **Asignaciones** de esa lista, vincula **operadores** con relación aprobada y ajustes globales opcionales.

Los operadores usan la lista asignada cuando les propones disponibilidad de catálogo.
TXT,
                'url' => '/account/provider-price-lists',
                'tags' => ['prestador', 'precios', 'catálogo'],
            ],
            [
                'key' => 'provider_offer_to_operator',
                'title' => '¿Cómo propongo mis servicios a un operador?',
                'content_short' => 'Con relación aprobada y lista de precios, marca variantes a proponer; el operador debe aceptar cada una.',
                'content' => <<<'TXT'
Para que un operador comercialice tu catálogo:

1. Confirma una **relación comercial aprobada** con ese operador.
2. Asigna una **lista de precios de prestador** al operador.
3. Abre **Disponibilidad de catálogo** y elige el operador.
4. Marca las variantes que quieres **proponer**. Solo servicios/variantes activos permiten nuevas propuestas.
5. Guarda. Las propuestas quedan **pendientes** hasta que el operador las acepte o rechace.

Puedes anular una propuesta pendiente antes de que la acepten. Las ofertas aceptadas permanecen hasta que las modifiques.
TXT,
                'url' => '/account/service-offers',
                'tags' => ['prestador', 'catálogo', 'ofertas', 'operador'],
            ],
            [
                'key' => 'provider_variant_availability',
                'title' => '¿Cómo configuro la disponibilidad de variantes?',
                'content_short' => 'Define reglas recurrentes y excepciones por fecha; el inventario base se configura en el asistente del catálogo.',
                'content' => <<<'TXT'
La **disponibilidad** de variantes controla en qué fechas se puede vender:

1. En el panel Prestador, abre **Catálogo → Disponibilidad de variantes**.
2. Elige una variante.
3. Agrega **reglas recurrentes** — rango de fechas, días de la semana, franjas horarias si aplica.
4. Agrega **excepciones por fecha** para cerrar un día o cambiar cupo en fechas puntuales.

Sin reglas, la variante puede no ser reservable en ninguna fecha. El inventario base se define en el asistente de servicios, no en esta pantalla.
TXT,
                'url' => '/account/availability',
                'tags' => ['prestador', 'disponibilidad', 'catálogo'],
            ],
            [
                'key' => 'provider_allocations',
                'title' => '¿Cómo asigno cupos a operadores?',
                'content_short' => 'Tras aceptar ofertas de catálogo, asigna cupo hard, soft o free sale por objetivo y rango de fechas.',
                'content' => <<<'TXT'
La **asignación de cupos** limita cuánto puede vender un operador:

1. Abre **Asignación de cupos** en el panel Prestador.
2. Elige un **operador** vinculado con ofertas de catálogo aceptadas.
3. Crea una asignación por servicio o variante, tipo (hard / soft / free sale), cupo y rango de fechas opcional.
4. Guarda.

Requisitos:
- Relación comercial **aprobada**.
- El operador debe haber **aceptado** la oferta de catálogo para ese objetivo.
- Cupos hard pueden exigir inventario por defecto en la variante.

No se permiten rangos de fechas solapados para el mismo objetivo.
TXT,
                'url' => '/account/allocations',
                'tags' => ['prestador', 'cupos', 'asignación'],
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, content: string, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function operatorArticles(): array
    {
        return [
            [
                'key' => 'operator_accept_provider_services',
                'title' => '¿Cómo acepto servicios propuestos por prestadores?',
                'content_short' => 'En el panel Operador, abre Servicios de prestadores, revisa propuestas pendientes y acepta o rechaza.',
                'content' => <<<'TXT'
Cuando un prestador propone variantes de catálogo:

1. Entra al panel **Operador**.
2. Abre **Servicios de prestadores**.
3. Filtra propuestas **Pendientes**.
4. Revisa la ficha y el precio operador según la lista del prestador.
5. Usa **Aceptar** para incorporarlo a tu contexto operador, o **Rechazar** para declinar.

Los servicios aceptados puedes usarlos al armar **paquetes comerciales** y listas de precios de operador.
TXT,
                'url' => '/account/service-offers',
                'tags' => ['operador', 'catálogo', 'ofertas', 'prestador'],
            ],
            [
                'key' => 'operator_create_package',
                'title' => '¿Cómo creo un paquete comercial?',
                'content_short' => 'En Paquetes comerciales, combina servicios aceptados de prestadores en un paquete vendible con ítems y condiciones.',
                'content' => <<<'TXT'
Los operadores arman paquetes a partir de servicios aceptados:

1. Abre **Paquetes comerciales** en el panel Operador.
2. Crea un paquete con nombre, estado y traducciones.
3. Agrega **ítems** — habitualmente por día — eligiendo servicios/variantes aceptados de prestadores.
4. Configura **condiciones** a nivel paquete cuando haga falta.
5. Activa el paquete cuando esté listo.

Los paquetes entran en listas de precios de operador y pueden proponerse a agencias vinculadas.
TXT,
                'url' => '/account/operator-packages',
                'tags' => ['operador', 'paquetes', 'catálogo'],
            ],
            [
                'key' => 'operator_price_list_agency',
                'title' => '¿Cómo creo una lista de precios de operador para agencias?',
                'content_short' => 'Las listas de precios de operador fijan tarifas hacia agencias por líneas de paquete y se asignan a agencias vinculadas.',
                'content' => <<<'TXT'
Para publicar precios a agencias:

1. Abre **Listas de precios → Listas de precios (operador)**.
2. Crea una lista con moneda y vigencia.
3. Agrega líneas por **ítems de paquete** de tus paquetes comerciales (ítems incluidos con precio).
4. Asigna la lista a **agencias vinculadas** con ajustes opcionales.

Las agencias ven esos precios cuando les propones ofertas de paquetes.
TXT,
                'url' => '/account/operator-price-lists',
                'tags' => ['operador', 'precios', 'agencia'],
            ],
            [
                'key' => 'operator_offer_packages',
                'title' => '¿Cómo propongo paquetes a una agencia?',
                'content_short' => 'Con relación aprobada y lista de precios de operador, propón paquetes activos; la agencia debe aceptar.',
                'content' => <<<'TXT'
Para vender paquetes a una agencia:

1. Confirma una **relación aprobada** con la agencia.
2. Asegúrate de tener una **lista de precios de operador** que cubra las líneas del paquete.
3. Abre **Ofertas de operadores** (ofertas de paquetes) y elige la agencia.
4. Propón los paquetes que puede reservar.
5. La agencia debe **aceptar** cada propuesta antes de reservar.

Desde la misma zona puedes gestionar disponibilidad de paquetes y cupos por agencia cuando esté configurado.
TXT,
                'url' => '/account/package-offers',
                'tags' => ['operador', 'paquetes', 'ofertas', 'agencia'],
            ],
            [
                'key' => 'operator_package_availability',
                'title' => '¿Cómo configuro la disponibilidad de paquetes?',
                'content_short' => 'Define reglas de calendario y excepciones por paquete comercial para indicar cuándo puede reservarse.',
                'content' => <<<'TXT'
La disponibilidad de paquetes funciona como la de variantes, pero a nivel paquete:

1. Abre **Paquetes comerciales** y la **Disponibilidad de paquetes** del paquete (o el menú dedicado bajo paquetes).
2. Configura **inventario** por defecto si el paquete usa stock limitado.
3. Agrega **reglas recurrentes** (días abiertos/rangos de fechas).
4. Agrega **excepciones** para cierres o cupo especial en fechas puntuales.

Las agencias no pueden reservar en fechas cerradas o sin regla aplicable.
TXT,
                'url' => '/account/package-availability',
                'tags' => ['operador', 'paquetes', 'disponibilidad'],
            ],
            [
                'key' => 'operator_package_allocations',
                'title' => '¿Cómo asigno cupos de paquetes a agencias?',
                'content_short' => 'Tras aceptar una oferta de paquete, asigna cuántas unidades puede vender la agencia por rango de fechas.',
                'content' => <<<'TXT'
La **asignación de cupos de paquetes** limita las ventas de la agencia:

1. Abre **Asignación de cupos** en el panel Operador (o desde ofertas de paquetes para una agencia).
2. Elige la agencia con oferta de paquete aceptada.
3. Crea asignaciones por paquete: tipo, cupo y fechas de vigencia opcionales.
4. Guarda.

La agencia solo puede reservar dentro del cupo asignado y las reglas de disponibilidad del paquete.
TXT,
                'url' => '/account/package-allocations',
                'tags' => ['operador', 'paquetes', 'cupos', 'agencia'],
            ],
            [
                'key' => 'operator_reservations_page',
                'title' => '¿Cómo gestiono las reservas que envían las agencias?',
                'content_short' => 'En Reservas del panel Operador ves las solicitudes de agencias vinculadas y confirmás o rechazás las pendientes.',
                'content' => <<<'TXT'
La pantalla **Reservas** del panel **Operador** centraliza las reservas que las agencias vinculadas envían sobre tus paquetes aceptados.

**Listado**
1. Abre **Reservas** en el panel Operador.
2. Usa el filtro **Estado** para ver pendientes, confirmadas, rechazadas o todas.
3. En la tabla revisás: código de reserva, agencia, paquete, fechas de viaje, pasajeros, estado y precio total.
4. Usa **Ver** para abrir el detalle de una reserva.

**Detalle y decisión**
En el detalle ves agencia, paquete, fechas, pasajeros, estado, desglose de precio y observaciones del cliente si las hubo.

Si la reserva está **pendiente de validación**:
- **Confirmar reserva** la aprueba.
- **Rechazar reserva** la declina; debés indicar el motivo del rechazo.

Cuando una agencia crea una reserva, el operador suele recibir una notificación para revisarla en esta pantalla.

Las agencias solo pueden reservar paquetes que hayas propuesto y que ellas hayan aceptado, respetando disponibilidad y cupos configurados.
TXT,
                'url' => '/account/reservations',
                'tags' => ['operador', 'reservas', 'agencia', 'confirmar', 'rechazar'],
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, content: string, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function agencyArticles(): array
    {
        return [
            [
                'key' => 'agency_accept_packages',
                'title' => '¿Cómo acepto ofertas de paquetes de un operador?',
                'content_short' => 'En el panel Agencia, revisa propuestas pendientes de paquetes y acepta las que quieras comercializar.',
                'content' => <<<'TXT'
Cuando un operador propone paquetes:

1. Entra al panel **Agencia**.
2. Abre **Ofertas de operadores** (bandeja de paquetes).
3. Revisa propuestas **pendientes** con precios de la lista del operador.
4. **Acepta** para habilitar reservas, o rechaza si no vas a vender ese paquete.

Necesitas una relación comercial aprobada con el operador para ver propuestas.
TXT,
                'url' => '/account/package-offers',
                'tags' => ['agencia', 'paquetes', 'ofertas', 'operador'],
            ],
            [
                'key' => 'agency_make_reservation',
                'title' => '¿Cómo reservo un paquete para mi cliente?',
                'content_short' => 'Desde Reservas, elige un paquete aceptado, fechas y pasajeros; el operador confirma según las reglas del paquete.',
                'content' => <<<'TXT'
Para crear una reserva como agencia:

1. Abre **Reservas** en el panel Agencia.
2. Inicia una reserva nueva sobre un **paquete aceptado** de un operador vinculado.
3. Elige fechas de viaje y datos de pasajeros.
4. Envía la reserva.

El sistema valida disponibilidad del paquete, tu cupo con el operador y capacidad. El operador puede confirmar al instante o tras revisión, según la configuración.

Puedes seguir el estado desde el detalle de la reserva.
TXT,
                'url' => '/account/reservations',
                'tags' => ['agencia', 'reservas', 'booking'],
            ],
        ];
    }

    /**
     * @return list<array{key: string, title: string, content: string, content_short?: string, url?: string|null, tags?: list<string>}>
     */
    private function relationshipArticles(): array
    {
        return [
            [
                'key' => 'provider_link_operator',
                'title' => '¿Cómo vinculo mi empresa con un operador?',
                'content_short' => 'Usa Relaciones comerciales para invitar un operador o aceptar su invitación; el vínculo debe quedar aprobado antes de catálogo y precios.',
                'content' => <<<'TXT'
Prestadores y operadores deben estar vinculados comercialmente:

1. Abre **Relaciones → Relaciones comerciales** en el menú.
2. **Invita una empresa** y elige el operador, o espera una invitación del operador y **acéptala**.
3. Espera a que el estado de la relación sea **Aprobada**.

Solo entonces puedes asignar listas de precios, proponer variantes de catálogo y definir cupos.

Si tu empresa es prestador y operador, elige si ves vínculos **como prestador** o **como operador**.
TXT,
                'url' => '/account/relationships',
                'tags' => ['prestador', 'relaciones', 'operador', 'onboarding'],
            ],
            [
                'key' => 'relationships_invite_company',
                'title' => '¿Cómo invito a otra empresa?',
                'content_short' => 'Desde Relaciones, envía una invitación indicando el tipo de vínculo comercial que quieres establecer.',
                'content' => <<<'TXT'
Para conectar con una empresa socia:

1. Ve a **Relaciones → Invitar empresas** en el menú.
2. Completa el contacto de la empresa invitada y el tipo de relación (prestador/operador/agencia según corresponda).
3. Envía la invitación.

La empresa invitada debe aceptar para que el vínculo comercial quede activo. Sigue el estado en **Relaciones comerciales**.
TXT,
                'url' => '/account/invitations/company',
                'tags' => ['relaciones', 'invitar', 'todos'],
            ],
            [
                'key' => 'relationships_pending_invitations',
                'title' => '¿Cómo acepto o rechazo una invitación pendiente?',
                'content_short' => 'Las invitaciones pendientes aparecen en Relaciones comerciales; acepta para empezar a trabajar juntos.',
                'content' => <<<'TXT'
Si otra empresa invitó a la tuya:

1. Abre **Relaciones comerciales**.
2. Busca la sección **Invitaciones pendientes**.
3. Revisa quién invitó y la fecha de vencimiento si se muestra.
4. Usa **Aceptar** para crear el vínculo, o **Rechazar** si no quieres conectar.

Después de aceptar, la relación puede necesitar estado **Aprobada** según el flujo de tu cuenta.
TXT,
                'url' => '/account/relationships',
                'tags' => ['relaciones', 'invitar', 'todos'],
            ],
        ];
    }
}
