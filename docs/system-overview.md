# Simple Travel — panorama del sistema

Resumen de lo que el producto cubre hoy. El detalle de cada proceso sigue en los documentos aislados listados al final.

Simple Travel es una plataforma web B2B para la cadena turística **prestador → operador → agencia**. Una misma persona puede pertenecer a varias cuentas y cambiar de cuenta o de rol (carril) sin salir del sistema. El panel de administración (`/smpl_adm`, Filament) mantiene catálogos y configuración de plataforma. La operación diaria ocurre en el sitio autenticado.

Idiomas de interfaz: español, inglés y portugués.

## Cadena comercial

| Rol | Qué hace hoy |
|-----|----------------|
| **Prestador** | Arma su catálogo de servicios, define disponibilidad, arma listas de precios hacia operadores, asigna cupos y envía propuestas de servicios. |
| **Operador** | Acepta o rechaza servicios de prestadores, arma paquetes comerciales, publica precios y cupos hacia agencias, y confirma o rechaza reservas. |
| **Agencia** | Administra clientes (personas y organizaciones), acepta paquetes del operador y genera reservas a partir de una oferta aceptada. |

La reserva que existe es **de agencia hacia operador**, sobre un paquete ofrecido. No hay checkout público para el viajero final ni pasarela de pago.

## Qué está cubierto

**Acceso y cuenta.** Registro, inicio de sesión, verificación de correo, recuperación de contraseña e ingreso con Google o Facebook. Invitaciones a empleados y a empresas. Perfil, datos de la empresa, ajustes de cuenta, tipos de cambio y notificaciones. Guía de inicio con tareas. El administrador puede abrir la web de un usuario mediante un enlace de un solo uso.

**Red comercial.** Relaciones entre cuentas, con invitación, aceptación y rechazo. Contactos de la cuenta. Cada rol ve su propio panel.

**Catálogo de servicios.** Asistente de ocho pasos: datos base, estado, características, variantes, imágenes, condiciones, experiencias y, en algunos tipos, un perfil avanzado. Tipos de servicio: alojamiento, traslado, excursión, gastronomía, transporte (pasajes), alquiler, evento, paquete y otros. El paso avanzado aplica a gastronomía, alojamiento, excursión, evento y traslado (en traslados: rutas y precios). También hay copiado de servicios y traducción asistida de textos.

**Precios.** Listas del prestador asignadas a operadores, y listas comerciales del operador. Vista previa del precio calculado de una variante. Monedas y tipos de cambio (oficiales de plataforma y de la cuenta).

**Ofertas.** El prestador propone servicios a un operador; el operador acepta o rechaza, puede ver un PDF y ajustar la disponibilidad vinculada. El operador propone paquetes a una agencia, con el mismo esquema de aceptación, rechazo y PDF.

**Disponibilidad y cupos.** Reglas y excepciones de disponibilidad a nivel de servicio y de variante, y lo equivalente para paquetes del operador. Cupos del prestador hacia operadores, y cupos de paquetes del operador hacia agencias.

**Paquetes del operador.** Armado del paquete, ítems, condiciones, servicios incluidos, disponibilidad e inventario, y traducción asistida.

**Reservas.** La agencia reserva a partir de una oferta de paquete aceptada. El operador ve la reserva y puede confirmarla o rechazarla. El catálogo de estados es amplio (borrador, pendiente, confirmada, cancelada, rechazada, y otros); el flujo visible hoy es el de alta, consulta, confirmación y rechazo.

**Traslados.** Tipos de vehículo por cuenta (con plantilla para importar), ubicaciones, rutas y precios. El catálogo de tipos de ubicación es global.

**Sitio público de la plataforma.** Landing comercial, página de planes y precios (alimentada por el catálogo de planes), preguntas frecuentes, acerca de, y páginas legales (privacidad, términos, política de uso). Comparativa “digitalizar operador”. Si el dominio corresponde a una cuenta, la portada muestra una bienvenida de ese sitio; no es una tienda ni un motor de reservas.

**Asistente de IA.** Chat dentro de la cuenta, base de conocimiento editable desde administración, y registro de uso. Hay ayuda de traducción en el asistente de servicios y en los paquetes.

**Administración de plataforma.** Usuarios, roles y permisos por cuenta. Catálogos de servicios (características, condiciones, experiencias, alojamiento, actividades, gastronomía, traslados). Geografía, idiomas, monedas, parámetros, documentos, preguntas frecuentes y menú del sitio. Planes comerciales y módulos. Tareas de la guía de inicio.

## Qué está modelado, pero no es operación todavía

Estos elementos existen como datos, menú o página, y no cierran un flujo usable:

- **Planes y módulos comerciales** (operación básica, sitio web, motor de reservas, clientes, finanzas, automatizaciones, B2B, facturación, globalización, reportes, integraciones, API). Alimentan la página de precios y el panel de administración. No activan ni bloquean funciones de la cuenta.
- **Menú “A desarrollar”:** operaciones, finanzas e integraciones.
- **Páginas públicas de relleno:** demo, integraciones, centro de ayuda, API y contacto.
- **Entradas de menú de sitio web** (configuración y ver sitio) que no tienen una pantalla de gestión propia.
- **Estados de reserva** ligados a pago o a un ciclo operativo largo, sin cobro ni operación detrás.
- **“Solicitar a prestadores”** en el catálogo: marcado como próximo.

## Qué no cubre

Pagos, facturación, reportes, integraciones con terceros, API pública, motor de reservas para el viajero, y aplicación móvil.

## Documentos de detalle

| Tema | Documento |
|------|-----------|
| Permisos y roles por cuenta | `docs/permissions.md` |
| Alcance de `account_id` (plataforma, catálogo, cuenta) | `docs/account-scoping.md` |
| Disponibilidad de servicios y variantes | `docs/service-availability-model.md` |
| Cálculo de precio de variante hacia el operador | `docs/operator-service-variant-price-calculation.md` |
| Características y condiciones en el asistente | `docs/website-service-features-and-details-survey.md` |
| Tipos de ubicación de traslados | `docs/transfer-location-types.md` |
| Base de conocimiento del asistente | `docs/client-knowledge-base.md` |
| Visión de producto para inversión (no es el inventario técnico) | `docs/inversor-proyecto-tecnica.md` |
