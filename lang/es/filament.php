<?php

/**
 * Spanish translations for Filament admin (project-specific).
 * Filament's own Spanish strings come from vendor; this file is for
 * resource labels and any custom overrides. To support another language
 * later, add e.g. lang/en/filament.php and switch locale (APP_LOCALE or panel).
 */

return [

    'clusters' => [
        'accounts' => 'Cuentas',
        'catalog' => 'Catálogo',
        'gastronomy' => 'Gastronomía',
        'hospitality' => 'Alojamiento',
        'experiences' => 'Experiencias',
        'crm' => 'CRM',
        'commercial' => 'Comercial',
        'administration' => 'Administración',
        'transport' => 'Transporte',
    ],

    'panel' => [
        'cluster_subnav_hide' => 'Ocultar menú del módulo',
        'cluster_subnav_show' => 'Mostrar menú del módulo',
    ],

    'common' => [
        'active' => 'Activo',
        'view' => 'Ver',
        'close' => 'Cerrar',
        'copy' => 'Copiar',
        'code_copied' => 'Código copiado al portapapeles',
        'code_copy_failed' => 'No se pudo copiar (navegador bloqueó o no compatible).',
        'click_to_copy_code' => 'Clic para copiar este código',
        'navigation_badge_tooltip' => 'Total de registros',
        'select_option' => 'Seleccione una opción',
    ],

    'pages' => [
        'list_records_count' => 'Total: :count :label',
        'website_menu_editor' => [
            'nav_label' => 'Menú web',
            'title' => 'Editor del menú web',
            'header_action' => 'Editor visual',
            'section_heading' => 'Árbol',
            'hint' => 'Use las flechas para reordenar ítems entre hermanos. Abra un ítem para editar textos, rutas, visibilidad y qué tipos de cuenta lo ven.',
            'move_up' => 'Subir',
            'move_down' => 'Bajar',
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ],
    ],

    'resources' => [

        'account' => 'Cuenta',
        'accounts' => 'Cuentas',

        'account_tabs' => [
            'main' => 'Datos principales',
            'tax_ids' => 'Identificaciones fiscales',
            'business_types' => 'Tipos de cuenta',
        ],

        'account_type_category_fields' => [
            'label' => 'Tipos de cuenta',
            'help' => 'Uno o más tipos de negocio (prestador, agencia, etc.). Se administran en Tipos de cuenta (parámetros).',
        ],

        'account_fields' => [
            'nick' => 'Alias',
            'code' => 'Código',
            'name' => 'Nombre',
            'commercial_name' => 'Razón social',
            'email' => 'Correo electrónico',
            'phone' => 'Teléfono',
            'address_line1' => 'Dirección (línea 1)',
            'address_line2' => 'Dirección (línea 2)',
            'city_id' => 'Ciudad',
            'state_id' => 'Estado',
            'country_id' => 'País',
            'postal_code' => 'Código postal',
            'code_help' => 'Se genera automáticamente al crear.',
        ],

        'account_columns' => [
            'id' => 'ID',
            'nick' => 'Alias',
            'code' => 'Código',
            'name' => 'Nombre',
            'commercial_name' => 'Razón social',
            'email' => 'Correo electrónico',
            'account_category' => 'Categoría de cuenta',
            'account_type' => 'Tipo de cuenta',
        ],

        'user_actions' => [
            'open_website_impersonation' => 'Enlace de acceso al sitio',
            'open_website_impersonation_tooltip' => 'Genera un enlace de un solo uso para abrir el sitio en otro navegador como este usuario.',
            'impersonation_modal_heading' => 'Enlace de acceso al sitio (un solo uso)',
            'impersonation_modal_help' => 'Copiá el enlace y abrilo en otro navegador (o ventana privada). Vence en unos minutos y solo funciona una vez.',
            'impersonation_forbidden' => 'No tenés permiso para generar este enlace.',
            'impersonation_invalid_target' => 'Este usuario no puede usarse para este enlace.',
            'impersonation_link_aria' => 'Enlace de acceso de un solo uso',
            'impersonation_link_label' => 'Enlace',
            'impersonation_copy_button' => 'Copiar',
            'impersonation_copied' => 'Copiado',
            'impersonation_copy_failed' => 'No se pudo copiar',
            'impersonation_copy_hint' => 'Tip: también podés hacer triple clic en el recuadro y Ctrl+C (Cmd+C en Mac).',
        ],

        'account_category' => 'Categoría de cuenta',
        'account_categories' => 'Categorías de cuenta',

        'account_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'account_category_fields' => [
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'sort_order' => 'Orden',
            'language' => 'Idioma',
        ],

        'account_category_columns' => [
            'id' => 'ID',
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'sort_order' => 'Orden',
        ],

        'account_type' => 'Tipo de cuenta',
        'account_types' => 'Tipos de cuenta',

        'account_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'account_type_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'account_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'user' => 'Usuario',
        'users' => 'Usuarios',

        'user_tabs' => [
            'general' => 'Perfil',
            'accounts_roles' => 'Cuentas y roles',
        ],

        'user_fields' => [
            'accounts' => 'Cuentas',
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'roles' => 'Roles',
            'memberships_heading' => 'Cuentas y permisos',
            'memberships_help' => 'Una fila por cuenta. Primero elige la cuenta y luego los roles para ese equipo (Spatie usa account_id como equipo).',
            'account' => 'Cuenta',
            'add_membership' => 'Añadir cuenta',
        ],

        'user_columns' => [
            'id' => 'ID',
            'accounts' => 'Cuentas',
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'roles' => 'Roles',
        ],
        'user_filters' => [
            'account' => 'Empresa',
        ],

        'user_invitation' => 'Invitación',
        'user_invitations' => 'Invitaciones',

        'user_invitation_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'account_inviting' => 'Cuenta que invita',
            'email' => 'Correo electrónico',
            'name' => 'Nombre del invitado',
            'role' => 'Rol',
            'type' => 'Tipo',
            'status' => 'Estado',
            'expires_at' => 'Expira',
            'invited_by' => 'Invitado por',
        ],

        'user_invitation_fields' => [
            'account_id' => 'Cuenta',
            'account_inviting' => 'Cuenta que invita',
            'account_inviting_helper' => 'Cuenta que generó la invitación (p. ej. operador). Si se deja vacío al crear, se usa la misma que Cuenta.',
            'email' => 'Correo electrónico',
            'name' => 'Nombre del invitado',
            'role_id' => 'Rol',
            'role_external_owner' => 'owner (empresa nueva)',
            'role_id_external_helper' => 'Las invitaciones externas usan siempre el rol owner en la empresa nueva creada al registrarse.',
            'type' => 'Tipo',
            'status' => 'Estado',
            'expires_at' => 'Expira',
            'invited_by' => 'Invitado por',
            'token' => 'Token',
            'accepted_at' => 'Aceptada en',
            'declined_at' => 'Rechazada en',
        ],

        'user_invitation_filters' => [
            'type' => 'Tipo',
            'status' => 'Estado',
        ],

        'account_relationship' => 'Relación comercial',
        'account_relationships' => 'Relaciones comerciales',

        'account_relationship_columns' => [
            'id' => 'ID',
            'operator_account' => 'Operador',
            'provider_account' => 'Prestador',
            'status' => 'Estado',
            'created_via' => 'Origen',
            'approved_at' => 'Aprobada en',
        ],

        'account_relationship_fields' => [
            'operator_account_id' => 'Cuenta operador',
            'provider_account_id' => 'Cuenta prestador',
            'status' => 'Estado',
            'created_via' => 'Origen',
            'source_invitation_id' => 'Invitación de origen',
            'approved_by_user_id' => 'Aprobada por',
            'approved_at' => 'Aprobada en',
            'suspended_at' => 'Suspendida en',
            'terminated_at' => 'Terminada en',
        ],

        'account_relationship_filters' => [
            'status' => 'Estado',
            'created_via' => 'Origen',
        ],

        'account_relationship_status' => [
            'approved' => 'Aprobada',
            'suspended' => 'Suspendida',
            'terminated' => 'Terminada',
        ],

        'account_relationship_created_via' => [
            'invitation' => 'Invitación',
            'manual' => 'Manual',
            'system' => 'Sistema',
        ],

        'role' => 'Rol',
        'roles' => 'Roles',

        'role_fields' => [
            'account_id' => 'Cuenta',
            'name' => 'Nombre',
            'permissions' => 'Permisos',
        ],

        'role_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'name' => 'Nombre',
            'permissions_count' => 'Permisos',
        ],

        'role_filters' => [
            'account_id' => 'Cuenta',
        ],

        'permission' => 'Permiso',
        'permissions' => 'Permisos',

        'permission_fields' => [
            'name' => 'Nombre del permiso',
            'name_help' => 'Identificador estable (p. ej. manage_services). El guard es web.',
        ],

        'permission_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'roles_count' => 'Roles',
        ],

        'account_tax_id' => 'Identificación fiscal',
        'account_tax_ids' => 'Identificaciones fiscales',

        'account_tax_id_fields' => [
            'account_id' => 'Cuenta',
            'account_category_id' => 'Tipo / Categoría',
            'value' => 'Valor',
            'add' => 'Agregar identificación fiscal',
        ],

        'account_tax_id_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'category' => 'Categoría',
            'value' => 'Valor',
        ],

        'contact_department' => 'Departamento de contacto',
        'contact_departments' => 'Departamentos de contacto',

        'contact_department_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'sort_order' => 'Orden',
        ],

        'contact_department_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'sort_order' => 'Orden',
        ],

        'contact_position' => 'Cargo de contacto',
        'contact_positions' => 'Cargos de contacto',

        'contact_position_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'contact_position_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'contact_position_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'contact_type' => 'Tipo de contacto',
        'contact_types' => 'Tipos de contacto',

        'contact_type_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'is_unique_per_person' => 'Único por persona',
            'mask' => 'Máscara',
            'mask_help' => 'Máscara para formatear el valor (ej. teléfono, documento).',
            'validation' => 'Validación',
            'validation_help' => 'Regla o patrón de validación para el valor.',
        ],

        'contact_type_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'code' => 'Código',
            'mask' => 'Máscara',
            'validation' => 'Validación',
        ],

        'contact' => 'Contacto',
        'contacts' => 'Contactos',

        'contact_fields' => [
            'account_id' => 'Cuenta',
            'name' => 'Nombre',
            'contact_department_id' => 'Departamento',
            'contact_position_id' => 'Cargo',
        ],

        'contact_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'name' => 'Nombre',
            'department' => 'Departamento',
            'position' => 'Cargo',
        ],

        'person' => 'Persona',
        'persons' => 'Personas',

        'person_tabs' => [
            'general' => 'General',
            'users' => 'Usuarios vinculados',
            'account_memberships' => 'Cuentas vinculadas',
            'contact_methods' => 'Medios de contacto',
            'contact_links' => 'Vínculos entre cuentas',
        ],

        'person_fields' => [
            'name' => 'Nombre',
            'user_id' => 'Usuario',
            'add_user_link' => 'Vincular usuario',
            'account_id' => 'Cuenta',
            'contact_department_id' => 'Departamento',
            'contact_position_id' => 'Cargo',
            'is_primary' => 'Contacto principal de la cuenta',
            'is_public_contact' => 'Contacto público',
            'is_preferred_contact_mode' => 'Modo de contacto preferido',
            'add_account_membership' => 'Añadir cuenta vinculada',
            'contact_type_id' => 'Tipo de canal',
            'contact_method_value' => 'Valor',
            'contact_method_is_primary' => 'Principal para este canal',
            'is_verified' => 'Verificado',
            'add_contact_method' => 'Añadir medio de contacto',
            'link_account_id' => 'Cuenta (dueña del vínculo)',
            'link_source_account_id' => 'Cuenta origen del contacto',
            'is_favorite' => 'Favorito',
            'visibility' => 'Visibilidad',
            'add_contact_link' => 'Añadir vínculo entre cuentas',
        ],

        'person_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'users_count' => 'Usuarios',
            'account_memberships_count' => 'Cuentas vinculadas',
            'contact_methods_count' => 'Medios',
            'contact_links_count' => 'Vínculos',
        ],

        'person_visibility' => [
            'private' => 'Privado',
            'shared' => 'Compartido',
        ],

        'provider' => 'Prestador',
        'providers' => 'Prestadores',

        'provider_tabs' => [
            'main' => 'Datos principales',
            'categories' => 'Categorías',
        ],

        'provider_fields' => [
            'name' => 'Nombre',
            'commercial_name' => 'Razón social',
            'email' => 'Correo electrónico',
            'phone' => 'Teléfono',
            'address_line1' => 'Dirección (línea 1)',
            'address_line2' => 'Dirección (línea 2)',
            'city_id' => 'C�digo de ciudad',
            'postal_code' => 'Código postal',
            'status' => 'Estado',
            'inviting_id' => 'Cuenta que invita',
            'categories' => 'Categorías',
        ],

        'provider_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'commercial_name' => 'Razón social',
            'email' => 'Correo electrónico',
            'status' => 'Estado',
            'inviting' => 'Cuenta que invita',
        ],

        'provider_status' => [
            'active' => 'Activo',
            'onhold' => 'En espera',
            'inactive' => 'Inactivo',
            'terminated' => 'Dado de baja',
        ],

        'provider_category' => 'Categoría de prestador',
        'provider_categories' => 'Categorías de prestador',

        'provider_category_fields' => [
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'sort_order' => 'Orden',
        ],

        'provider_category_columns' => [
            'id' => 'ID',
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'language' => 'Idioma',
        'languages' => 'Idiomas',

        'language_fields' => [
            'language' => 'Idioma',
        ],

        'language_columns' => [
            'id' => 'ID',
            'language' => 'Idioma',
            'code' => 'Código',
            'list_order' => 'Orden',
        ],

        'currency' => 'Moneda',
        'currencies' => 'Monedas',

        'currency_fields' => [
            'currency' => 'Moneda',
        ],

        'currency_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'symbol' => 'Símbolo',
            'name' => 'Nombre',
        ],

        'lmp_country' => 'País',
        'lmp_countries' => 'Países',

        'lmp_country_fields' => [
            'name' => 'Nombre',
            'iso_2' => 'ISO 2',
            'iso_3' => 'ISO 3',
            'phonecode' => 'Código telefónico',
            'capital' => 'Capital',
            'currency_id' => 'Moneda',
            'tld' => 'Dominio de nivel superior',
            'emoji' => 'Emoji',
        ],

        'lmp_country_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'iso_2' => 'ISO 2',
            'iso_3' => 'ISO 3',
            'capital' => 'Capital',
            'currency' => 'Moneda',
        ],

        'lmp_state' => 'Estado / provincia',
        'lmp_states' => 'Estados / provincias',

        'lmp_state_fields' => [
            'name' => 'Nombre',
            'country_id' => 'País',
            'level' => 'Nivel',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
            'timezone_id' => 'ID de zona horaria',
            'parent_id' => 'Estado padre',
        ],

        'lmp_state_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'country' => 'País',
            'parent' => 'Estado padre',
            'level' => 'Nivel',
        ],

        'lmp_state_filters' => [
            'country_id' => 'País',
        ],

        'lmp_city' => 'Ciudad',
        'lmp_cities' => 'Ciudades',

        'lmp_city_fields' => [
            'name' => 'Nombre',
            'state_id' => 'Estado / provincia',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
            'timezone_id' => 'ID de zona horaria',
        ],

        'lmp_city_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'state' => 'Estado / provincia',
            'country' => 'País',
            'system_transfer_locations' => 'POI traslados (sistema)',
        ],

        'lmp_city_filters' => [
            'country_id' => 'País',
            'state_id' => 'Estado / provincia',
        ],

        'lmp_city_actions' => [
            'generate_transfer_locations' => 'Generar ubicaciones de traslado',
            'generate_transfer_locations_heading' => 'Generar catálogo de traslados (sistema)',
            'generate_transfer_locations_description' => 'Usa OpenAI para proponer puntos de recogida/entrega de esta ciudad (account_id null). Con traducción: una llamada para la lista y una o dos llamadas en lote para traducir todos los nombres (sin MyMemory/Google por cada punto).',
            'replace_existing' => 'Reemplazar catálogo existente de esta ciudad',
            'replace_existing_help' => 'Elimina todas las ubicaciones de sistema vinculadas a esta ciudad antes de insertar las nuevas.',
            'translate_to_other_languages' => 'Traducir a los demás idiomas activos',
            'translate_to_other_languages_help' => 'Segunda solicitud a OpenAI: traduce todos los nombres a los demás idiomas activos en una o dos llamadas por lote.',
            'source_language' => 'Idioma origen para etiquetas de la IA',
            'max_suggestions' => 'Máximo de sugerencias',
            'additional_context' => 'Contexto adicional (opcional)',
            'generate_failed_title' => 'No se pudieron generar ubicaciones',
            'generate_none_title' => 'No se crearon ubicaciones',
            'generate_none_body' => 'La IA no generó filas nuevas (duplicados omitidos: :skipped).',
            'generate_success_title' => 'Catálogo de traslados actualizado',
            'generate_success_body' => 'Se crearon :created ubicación(es). La IA sugirió :ai. Omitidas :skipped duplicada(s). Eliminadas :removed fila(s) previa(s). Llamadas OpenAI: :openai_calls.',
            'generate_translation_fallbacks' => ':count nombre(s) quedaron en el idioma origen porque falló la traducción.',
            'openai_rate_limit' => 'OpenAI alcanzó el límite de solicitudes (request limit exceeded). Esperá unos minutos y volvé a intentar, o revisá el uso en platform.openai.com. Los embeddings y esta acción comparten la misma API key.',
            'openai_quota' => 'Se agotó la cuota o el crédito de OpenAI. Cargá saldo o revisá el plan en platform.openai.com — no se guardó nada.',
            'openai_invalid_key' => 'OpenAI rechazó la API key. Revisá OPENAI_API_KEY en el .env.',
            'openai_model' => 'El modelo de chat ":model" no está disponible para esta clave. Configurá OPENAI_CHAT_MODEL en .env (ej. gpt-4o-mini).',
            'openai_generic' => 'Falló la solicitud a OpenAI: :detail',
        ],

        'currency_cat_catalog_label' => 'Moneda #:id (ref #:ref)',

        'currency_rate' => 'Tipo de cambio',
        'currency_rates' => 'Tipos de cambio',

        'currency_rate_fields' => [
            'account_id' => 'Cuenta',
            'account_id_help' => 'Dejar vacío para tipos oficiales del sistema. Indicar cuenta para un override del cliente.',
            'currency_id' => 'Moneda',
            'source' => 'Fuente',
            'source_help' => 'Reservado para uso futuro (distintas fuentes de cotización).',
            'units_per_usd_buy' => 'Compra (unidades por 1 USD)',
            'units_per_usd_sell' => 'Venta (unidades por 1 USD)',
            'units_per_usd_help' => 'Cuántas unidades de esta moneda equivalen a 1 dólar. Para USD ambos valen 1.',
            'starting_at' => 'Vigente desde',
            'starting_at_help' => 'Aplica desde esta fecha (inicio del día) hasta un registro más reciente del mismo ámbito.',
            'is_active' => 'Activo',
        ],

        'currency_rate_columns' => [
            'id' => 'ID',
            'account' => 'Ámbito',
            'currency' => 'Moneda',
            'units_per_usd_buy' => 'Compra / USD',
            'units_per_usd_sell' => 'Venta / USD',
            'starting_at' => 'Vigente desde',
            'is_active' => 'Activo',
        ],

        'currency_rate_scope' => [
            'system' => 'Sistema',
        ],

        'currency_rate_filters' => [
            'all_active_states' => 'Todos',
            'active_only' => 'Solo activos',
            'inactive_only' => 'Solo inactivos',
            'scope' => 'Ámbito',
            'all_scopes' => 'Todos los ámbitos',
            'system_only' => 'Solo sistema',
            'tenant_only' => 'Solo overrides de cuenta',
        ],

        'currency_rate_validation' => [
            'duplicate_starting_at' => 'Ya existe un tipo para esta moneda, ámbito y fecha de vigencia.',
            'units_must_be_positive' => 'El valor debe ser mayor que cero.',
        ],

        'menu' => 'Ítem de menú',
        'menus' => 'Menús web',

        'menu_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
            'account_types' => 'Visibilidad por tipo',
        ],

        'menu_fields' => [
            'slug' => 'Slug',
            'slug_help' => 'Clave interna (única). Se usa en código; no tiene por qué mostrarse en el sitio público.',
            'parent_id' => 'Padre',
            'icon' => 'Icono',
            'route' => 'Nombre de ruta',
            'translation_name' => 'Etiqueta',
            'translation_tip' => 'Tooltip',
            'excluded_account_types' => 'Oculto para tipos de cuenta',
            'excluded_account_types_help' => 'Déjelo vacío para mostrar el ítem a todos los tipos. Marque los tipos que no deben ver este menú.',
        ],

        'menu_columns' => [
            'id' => 'ID',
            'label' => 'Etiqueta',
            'route' => 'Ruta',
            'parent' => 'Padre',
            'excluded_account_types' => 'Oculto para tipos',
            'excluded_account_types_none' => 'Todos los tipos',
        ],

        'menu_duplicate' => 'Copiar',

        'menu_filter' => [
            'scope' => 'Ámbito',
            'all_levels' => 'Todos los niveles',
            'root_only' => 'Solo raíz',
            'children_of' => 'Hijos de: :label',
            'account_type' => 'Tipo de cuenta',
            'account_type_placeholder' => 'Todos los tipos',
            'active_status' => 'Activo',
            'active_all' => 'Todos',
            'active_only' => 'Solo activos',
            'inactive_only' => 'Solo inactivos',
        ],

        'menu_validation' => [
            'parent_cycle' => 'Ese padre crearía un ciclo en la jerarquía.',
        ],

        'parameter_definition' => 'Definición de parámetro',
        'parameter_definitions' => 'Definiciones de parámetros',

        'parameter_definition_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
            'options' => 'Opciones',
            'values' => 'Valores',
        ],

        'parameter_definition_fields' => [
            'category' => 'Categoría',
            'subcategory' => 'Subcategoría',
            'code' => 'Código',
            'type' => 'Tipo',
            'scope' => 'Alcance',
            'has_default' => 'Tiene valor por defecto',
            'ui_component' => 'Componente UI',
            'ui_options' => 'Opciones UI',
            'sort_order' => 'Orden',
            'default_value' => 'Valor por defecto',
            'validation_rules' => 'Reglas de validación',
            'translation_name' => 'Nombre',
            'translation_description' => 'Descripción',
            'translation_help' => 'Ayuda',
            'comments' => 'Comentarios',
        ],

        'parameter_definition_columns' => [
            'id' => 'ID',
            'category' => 'Categoría',
            'subcategory' => 'Subcategoría',
            'code' => 'Código',
            'name' => 'Nombre',
            'value' => 'Valor',
            'type' => 'Tipo',
            'scope' => 'Alcance',
            'has_default' => 'Por defecto',
            'ui_component' => 'UI',
        ],

        'parameter_option_fields' => [
            'value' => 'Valor almacenado',
            'sort_order' => 'Orden',
            'label' => 'Etiqueta',
            'labels' => 'Etiquetas por idioma',
            'add' => 'Añadir opción',
        ],

        'parameter_definition_options_help' => 'Para select, radio, checkbox e interruptor debes definir al menos dos opciones (p. ej. dos valores explícitos para sí/no). El resto de componentes UI pueden dejar la lista vacía y usar texto libre.',
        'parameter_definition_options_min_two' => 'Este componente UI exige al menos dos opciones con valor almacenado.',
        'parameter_definition_values_tab_help' => 'Alcance sistema: como máximo una fila (la cuenta no aplica). Por cuenta: la cuenta es opcional — dejá vacío para un valor predeterminado para todas las cuentas, o indicá una cuenta por fila de anulación.',
        'parameter_definition_values_duplicate_account' => 'Cuenta duplicada en la lista de valores.',

        'parameter_definition_ui_components' => [
            'input' => 'Campo de texto',
            'select' => 'Lista desplegable',
            'checkbox' => 'Casilla',
            'radio' => 'Opción única',
            'switch' => 'Interruptor',
            'textarea' => 'Área de texto',
            'editor' => 'Editor enriquecido',
            'date' => 'Fecha',
            'datetime' => 'Fecha y hora',
            'time' => 'Hora',
        ],

        'parameter_value' => 'Valor de parámetro',
        'parameter_values' => 'Valores de parámetros',

        'parameter_value_fields' => [
            'parameter_definition_id' => 'Definición',
            'account_id' => 'Cuenta',
            'value' => 'Valor',
            'add_row' => 'Añadir valor',
            'definition_help' => 'Qué parámetro configura esta fila.',
            'account_placeholder' => 'Predeterminado (todas las cuentas)',
            'account_help_system' => 'Las definiciones de alcance sistema no guardan cuenta; siempre queda vacío.',
            'account_help' => 'Opcional. Dejá vacío para un valor predeterminado para todas las cuentas, o elegí una cuenta para un valor por cuenta.',
            'value_help' => 'Valor almacenado (texto libre, o una de las opciones predefinidas si la definición usa lista).',
        ],

        'parameter_value_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'value' => 'Valor',
        ],

        'parameter_value_duplicate' => 'Ya existe un valor para esta definición y cuenta (o para el valor de sistema).',
        'parameter_value_account_system' => 'Sistema',

        'plan' => 'Plan',
        'plans' => 'Planes',

        'plan_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
            'items' => 'Items del plan',
        ],

        'plan_fields' => [
            'code' => 'Código',
            'active' => 'Activo',
            'usd_price' => 'Precio USD',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'usd_price' => 'Precio USD',
            'active' => 'Activo',
        ],

        'plan_item' => 'Item del plan',
        'plan_items' => 'Items del plan',
        'plan_items_standalone' => 'Items de planes',

        'plan_item_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'plan_item_standalone_columns' => [
            'id' => 'ID',
            'plan' => 'Plan',
            'parent' => 'Padre (nivel superior)',
            'text' => 'Texto',
        ],

        'plan_item_standalone_filter_parent_with_children' => 'Ítem padre (con subítems)',

        'plan_item_fields' => [
            'plan_id' => 'Plan',
            'parent_id' => 'Item padre (subitem de)',
            'parent_root' => '— Nivel superior (sin padre) —',
            'untitled_row' => 'Item sin título',
            'add_row' => 'Añadir item del plan',
            'sort_order' => 'Orden',
            'active' => 'Activo',
            'text' => 'Texto',
        ],

        'plan_items_repeater_help' => 'Añade primero los items de nivel superior, luego los subitems y elige un padre de nivel superior. Arrastra las filas para cambiar el orden.',

        'plan_item_columns' => [
            'sort_order' => 'Orden',
            'parent' => 'Padre',
            'text' => 'Texto',
            'active' => 'Activo',
        ],

        'module' => 'Módulo',
        'modules' => 'Módulos',

        'module_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'module_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'module_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'active' => 'Activo',
        ],

        'nav_contacts' => 'Contactos',
        'nav_catalog_conditions' => 'Condiciones',
        'nav_catalog_experiences' => 'Experiencias de servicio',
        'nav_catalog_features' => 'Características',
        'nav_accounts_price_lists' => 'Listas de precios',
        'nav_plans' => 'Planes y precios',
        'nav_services' => 'Servicios',
        'nav_accounts_transfer' => 'Transporte',
        'nav_hotels' => 'Hoteles',
        'nav_activities' => 'Actividades',
        'nav_gastronomy' => 'Gastronomía',
        'nav_parameters' => 'Configuración',
        'nav_users' => 'Usuarios',
        'nav_authorization' => 'Autorización',
        'nav_onboarding' => 'Guía de inicio',
        'nav_ai' => 'Asistente IA',

        'ai_knowledge_item' => 'Artículo de conocimiento',
        'ai_knowledge_items' => 'Base de conocimiento (IA)',

        'ai_knowledge_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'ai_knowledge_fields' => [
            'key' => 'Clave estable',
            'key_help' => 'Solo letras, números y guiones bajos (p. ej. editar_imagen_servicio).',
            'title' => 'Título',
            'content_short' => 'Resumen breve',
            'content' => 'Cuerpo',
            'url' => 'URL relacionada',
            'tags' => 'Etiquetas',
            'tags_help' => 'Palabras clave separadas por comas para filtrar.',
        ],

        'ai_knowledge_columns' => [
            'id' => 'ID',
            'key' => 'Clave',
            'title_preview' => 'Título (primer idioma)',
            'translations_count' => 'Idiomas',
        ],

        'cat_helper' => 'Ayuda contextual',
        'cat_helpers' => 'Textos de ayuda',
        'cat_helper_duplicate' => 'Copiar',

        'cat_helper_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'cat_helper_fields' => [
            'screen_code' => 'Pantalla',
            'screen_code_help' => 'Pantalla o flujo lógico al que pertenece esta ayuda (letras, números, guiones y guiones bajos).',
            'code' => 'Clave de ayuda',
            'code_help' => 'Identificador estable para Blade / front-end (letras, números, guiones y guiones bajos).',
            'account_type' => 'Tipo de cuenta (opcional)',
            'service_type' => 'Tipo de servicio (opcional)',
            'notes' => 'Notas internas',
            'text' => 'Contenido HTML',
            'text_help' => 'Usá la barra para dar formato y adjuntar imágenes; los archivos van a almacenamiento público (sin colección de medios aparte).',
        ],

        'cat_helper_columns' => [
            'id' => 'ID',
            'screen_and_code' => 'Pantalla y clave',
            'screen_code' => 'Pantalla',
            'code' => 'Clave de ayuda',
            'account_type' => 'Tipo de cuenta',
            'service_type' => 'Tipo de servicio',
            'text_preview' => 'Texto de ayuda',
            'translations_count' => 'Idiomas',
        ],

        'todo_category' => 'Categoría de tareas',
        'todo_categories' => 'Categorías de tareas',

        'todo_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'todo_category_fields' => [
            'code' => 'Código',
            'sort_order' => 'Orden',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'todo_category_columns' => [
            'id' => 'ID',
            'sort_order' => 'Orden',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'todo_category_actions' => [
            'copy_to_account' => 'Copiar a cuenta',
            'copy_to_account_heading' => 'Copiar tareas a una cuenta',
            'copy_to_account_description' => 'Crea una copia de cada tarea de esta categoría (de todas las cuentas), todas asignadas a la cuenta que elijas. Se duplican filas en todo_tasks y todo_task_translations.',
            'copy_destination_account' => 'Cuenta',
            'copy_failed_title' => 'No se pudieron copiar las tareas',
            'copy_invalid_account' => 'Seleccioná una cuenta válida.',
            'copy_none_title' => 'No hay tareas para copiar',
            'copy_none_body' => 'Esta categoría aún no tiene tareas.',
            'copy_all_skipped_title' => 'No se crearon tareas nuevas',
            'copy_all_skipped_body' => 'Las :skipped tarea(s) ya existían en la cuenta elegida (mismo código).',
            'copy_success_title' => 'Tareas copiadas',
            'copy_success_body' => 'Se crearon :created tarea(s). Se omitieron :skipped (mismo código en la cuenta).',
        ],

        'todo_task' => 'Tarea (plantilla)',
        'todo_tasks' => 'Tareas (plantillas)',

        'todo_task_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'todo_task_fields' => [
            'account_id' => 'Cuenta',
            'code' => 'Código',
            'todo_category_id' => 'Categoría',
            'original_task_id' => 'Basada en tarea (opcional)',
            'action_type' => 'Tipo de acción',
            'action_url' => 'URL',
            'action_url_help' => 'URL completa (incluyendo https://).',
            'route_name' => 'Ruta',
            'route_name_help' => 'Rutas GET con nombre de la aplicación (se ocultan Filament, Livewire y rutas internas similares).',
            'verification_type' => 'Tipo de verificación',
            'verification_url' => 'URL de verificación',
            'sort_order' => 'Orden',
            'name' => 'Título',
            'description' => 'Descripción',
        ],

        'todo_task_action_types' => [
            'none' => 'Ninguna',
            'route' => 'Ruta',
            'url' => 'URL',
            'external' => 'Externo',
        ],

        'todo_task_verification_types' => [
            'none' => 'Ninguna',
            'api-check' => 'Comprobación API',
        ],

        'todo_task_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'sort_order' => 'Orden',
            'code' => 'Código',
            'category' => 'Categoría',
            'name' => 'Título',
            'action_type' => 'Acción',
            'verification_type' => 'Verificación',
        ],

        'todo_task_filters' => [
            'account_id' => 'Cuenta',
        ],

        'todo_category_filters' => [
            'account_id' => 'Con tareas de la cuenta',
        ],

        'service_hotel_type' => 'Tipo de hotel',
        'service_hotel_types' => 'Tipos de hotel',

        'service_hotel_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_hotel_type_fields' => [
            'code' => 'Código',
            'category' => 'Categoría',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'service_hotel_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoría',
            'name' => 'Nombre',
        ],

        'service_hotel_type_category' => 'Categoría de tipo de hotel',
        'service_hotel_type_categories' => 'Categorías de tipos de hotel',

        'service_hotel_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_hotel_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_hotel_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_type' => 'Tipo',
        'service_gastronomy_types' => 'Tipos',

        'service_gastronomy_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_type_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'service_gastronomy_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_type_assignment' => 'Asignación de tipo gastronómico',
        'service_gastronomy_type_assignments' => 'Asignaciones de tipos gastronómicos',

        'service_gastronomy_type_assignment_tabs' => [
            'general' => 'General',
        ],

        'service_gastronomy_type_assignment_fields' => [
            'service_gastronomy_id' => 'Perfil de gastronomía',
            'service_gastronomy_type_id' => 'Tipo de gastronomía',
        ],

        'service_gastronomy_type_assignment_columns' => [
            'id' => 'ID',
            'service' => 'Servicio',
            'type' => 'Tipo',
        ],

        'service_gastronomy_venue' => 'Venue',
        'service_gastronomy_venues' => 'Venues',

        'service_gastronomy_venue_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_venue_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_venue_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_cuisine' => 'Cocina',
        'service_gastronomy_cuisines' => 'Cocinas',

        'service_gastronomy_cuisine_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_cuisine_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_cuisine_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_menu' => 'Menú',
        'service_gastronomy_menus' => 'Menús',

        'service_gastronomy_menu_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_menu_fields' => [
            'code' => 'Código',
            'is_default' => 'Menú por defecto',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'service_gastronomy_menu_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'is_default' => 'Por defecto',
            'name' => 'Nombre',
        ],

        'service_gastronomy_menu_category' => 'Categoría de menú',
        'service_gastronomy_menu_categories' => 'Categorías de menús',

        'service_gastronomy_menu_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_menu_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_menu_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_feature_category' => 'Categoría de característica',
        'service_gastronomy_feature_categories' => 'Categorías de características',

        'service_gastronomy_feature_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_feature_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_feature_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_feature' => 'Característica',
        'service_gastronomy_features' => 'Características',

        'service_gastronomy_feature_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_gastronomy_feature_fields' => [
            'category' => 'Categoría',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_gastronomy_feature_columns' => [
            'id' => 'ID',
            'category' => 'Categoría',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_feature_category' => 'Categoría de característica',
        'service_feature_categories' => 'Categorías de características',

        'service_feature_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_feature_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_feature_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_feature' => 'Característica',
        'service_features' => 'Características',

        'service_feature_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
            'scopes' => 'Scopes',
        ],

        'service_feature_fields' => [
            'category' => 'Categoría',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'scopes' => 'Scopes',
            'is_selectable' => 'Seleccionable',
            'parent_id' => 'Padre',
        ],

        'service_feature_columns' => [
            'id' => 'ID',
            'category' => 'Categoría',
            'code' => 'Código',
            'name' => 'Nombre',
            'parent' => 'Padre',
        ],
        
        'service_feature_parent_none' => 'Sin padre',
        'service_feature_set_parent' => 'Establecer padre',
        'service_feature_set_parent_failure_title' => 'Asignación de padre inválida',
        'service_feature_set_parent_success_title' => 'Padre actualizado',
        'service_feature_set_parent_failure_body_self' => 'No puedes asignar una feature como padre de sí misma.',
        'service_feature_set_parent_failure_body_cycle' => 'No puedes asignar un padre que cree un bucle en la jerarquía (recursión).',

        'service_feature_scope' => 'Scope de característica',
        'service_feature_scopes' => 'Scopes de características',

        'service_feature_scope_fields' => [
            'type' => 'Tipo',
            'feature' => 'Característica',
        ],

        'service_feature_scope_columns' => [
            'type' => 'Tipo',
            'feature' => 'Característica',
        ],

        'service_feature_scope_filters' => [
            'type' => 'Tipo',
            'feature' => 'Característica',
        ],

        'service_feature_scope_validation' => [
            'unique_pair' => 'Esta combinacion tipo-caracteristica ya existe.',
        ],

        'manage_service_feature_scopes' => [
            'navigation_label' => 'Scopes por tipo de servicio',
            'title' => 'Gestionar scopes por tipo de servicio',
            'service_type' => 'Tipo de servicio',
            'section_categories' => 'Categorías de características',
            'help_categories' => 'Solo se listan abajo las características de las categorías marcadas. Usa el interruptor masivo en la cabecera de la lista para marcar todas o ninguna.',
            'feature_categories' => 'Categorías a incluir',
            'section_in_scope' => 'En el scope de este tipo',
            'section_available' => 'Disponibles para añadir',
            'help_in_scope' => 'Desmarca características para quitarlas del scope de este tipo. Los cambios se aplican al guardar.',
            'help_available' => 'Marca características para incluirlas en el scope de este tipo al guardar.',
            'in_scope' => 'Características en el scope',
            'available' => 'Características fuera del scope',
            'actions' => [
                'save' => 'Guardar scopes',
            ],
            'notifications' => [
                'saved' => 'Scopes guardados.',
            ],
        ],

        'service_type' => 'Tipo de servicio',
        'service_types' => 'Tipos de servicio',

        'service_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_type_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'sort_order' => 'Orden',
        ],

        'service_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'sort_order' => 'Orden',
        ],

        'service_experience' => 'Experiencia de servicio',
        'service_experiences' => 'Experiencias de servicio',

        'service_experience_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_experience_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_experience_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_experience_category' => 'Categoría de experiencia de servicio',
        'service_experience_categories' => 'Categorías de experiencia de servicio',

        'service_experience_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_experience_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_experience_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_activity_type' => 'Tipo de actividad',
        'service_activity_types' => 'Tipos de actividad',

        'service_activity_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_activity_type_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_activity_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_activity_type_category' => 'Categoría de tipo de actividad',
        'service_activity_type_categories' => 'Categorías de tipo de actividad',

        'service_activity_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_activity_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_activity_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_activity' => 'Servicio de actividad',
        'service_activities' => 'Servicios de actividad',

        'service_activity_tabs' => [
            'general' => 'General',
            'technical' => 'Técnico',
        ],

        'service_activity_fields' => [
            'service_id' => 'Servicio',
            'service_activity_type_id' => 'Tipo de actividad (obsoleto)',
            'activity_types' => 'Tipos de actividad',
            'activity_types_help' => 'Marcá uno o más tipos del catálogo que describan este servicio.',
            'difficulty_level' => 'Dificultad',
            'min_age' => 'Edad mínima',
            'max_age' => 'Edad máxima',
            'guide_included' => 'Guía incluido',
            'transport_included' => 'Transporte incluido',
            'outdoor_activity' => 'Actividad al aire libre',
            'requires_good_weather' => 'Requiere buen tiempo',
            'max_altitude_m' => 'Altitud máxima (m)',
            'distance_km' => 'Distancia (km)',
        ],

        'service_activity_columns' => [
            'id' => 'ID',
            'service' => 'Servicio',
            'type' => 'Tipo',
            'difficulty' => 'Dificultad',
        ],

        'service_activity_difficulty' => [
            'easy' => 'Fácil',
            'moderate' => 'Moderada',
            'difficult' => 'Difícil',
        ],

        'service_detail_topic' => 'Tema de condiciones',
        'service_detail_topics' => 'Temas de condiciones',

        'service_detail_topic_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_detail_topic_fields' => [
            'code' => 'Código',
            'category' => 'Categoría',
            'visibility' => 'Visibilidad',
            'active' => 'Activo',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'service_detail_topic_visibility' => [
            'public' => 'Público',
            'operator' => 'Operador',
            'internal' => 'Interno',
        ],

        'service_detail_topic_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoría',
            'name' => 'Nombre',
            'visibility' => 'Visibilidad',
            'active' => 'Activo',
        ],

        'service_detail_condition_key' => 'Clave de condición',
        'service_detail_condition_keys' => 'Claves de condición',

        'service_detail_condition_key_categories' => [
            'payment' => 'Pago',
            'operation' => 'Operación',
            'transport' => 'Transporte',
            'accommodation' => 'Alojamiento',
            'safety' => 'Seguridad',
            'legal' => 'Legal',
            'inclusions' => 'Inclusiones',
            'traveler' => 'Viajero',
            'service' => 'Servicio',
        ],

        'service_detail_condition_key_fields' => [
            'category' => 'Categoría',
            'code' => 'Código',
            'code_help' => 'Identificador corto en inglés (ej. cancellation_policy).',
            'description' => 'Descripción',
        ],

        'service_detail_condition_key_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoría',
            'description' => 'Descripción',
        ],

        'service_detail_topic_category' => 'Categoría de condiciones',
        'service_detail_topic_categories' => 'Categorías de condiciones',

        'service_detail_topic_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_detail_topic_category_fields' => [
            'code' => 'Código',
            'active' => 'Activo',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'service_detail_topic_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'active' => 'Activo',
        ],

        'service_detail' => 'Condición',
        'service_details' => 'Condiciones',

        'service_detail_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_detail_fields' => [
            'service_id' => 'Servicio',
            'service_detail_topic_id' => 'Tema',
            'language_id' => 'Idioma',
            'description' => 'Descripción',
            'active' => 'Activo',
            'sort_order' => 'Orden',
            'add' => 'Añadir condición',
        ],

        'service_detail_columns' => [
            'id' => 'ID',
            'service' => 'Servicio',
            'topic' => 'Tema',
            'language' => 'Idioma',
            'description' => 'Descripción',
            'active' => 'Activo',
        ],

        'service' => 'Servicio',
        'services' => 'Servicios',

        'service_tabs' => [
            'general' => 'General',
            'translations' => 'Descripción del servicio',
            'variants' => 'Variantes',
            'details' => 'Condiciones',
            'experiences' => 'Experiencias',
            'media' => 'Imágenes',
        ],

        'service_media' => [
            'main_image' => 'Imagen principal',
            'gallery' => 'Galería (slider)',
            'gallery_help' => 'Imágenes opcionales para el carrusel. Arrastre para ordenar.',
            'max_image_size_hint' => 'Máximo 3 MB por imagen.',
        ],

        'service_variant_fields' => [
            'add' => 'Añadir variante',
            'sku' => 'SKU / Código',
            'status' => 'Estado',
            'capacity_min' => 'Capacidad mín.',
            'capacity_max' => 'Capacidad máx.',
            'duration_minutes' => 'Duración (minutos)',
            'pricing_type' => 'Tipo de precio',
            'base_price' => 'Precio base',
            'currency' => 'Moneda',
            'inventory_type' => 'Tipo de inventario',
            'inventory_total' => 'Inventario total',
            'booking_type' => 'Tipo de reserva',
            'min_advance_booking_hours' => 'Antelación mín. (horas)',
            'max_advance_booking_days' => 'Antelación máx. (días)',
            'start_time' => 'Hora inicio',
            'end_time' => 'Hora fin',
            'sort_order' => 'Orden',
        ],

        'service_variant_status' => [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'hidden' => 'Oculto',
            'suspended' => 'Suspendido',
            'discontinued' => 'Descatalogado',
        ],

        'service_variant_pricing_type' => [
            'per_person' => 'Por persona',
            'per_unit' => 'Por unidad',
            'per_room' => 'Por habitación',
            'per_vehicle' => 'Por vehículo',
        ],

        'service_variant_inventory_type' => [
            'unlimited' => 'Ilimitado',
            'fixed' => 'Fijo',
            'request' => 'Bajo petición',
        ],

        'service_variant_booking_type' => [
            'instant' => 'Inmediata',
            'request' => 'Bajo petición',
        ],

        'service_fields' => [
            'account_id' => 'Cuenta',
            'service_type_id' => 'Tipo de servicio',
            'city_id' => 'C�digo de ciudad',
            'status' => 'Estado',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'experiences' => 'Experiencias',
            'experiences_help' => 'Seleccione las experiencias que aplican a este servicio.',
        ],

        'service_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'service_type' => 'Tipo de servicio',
            'name' => 'Nombre',
            'status' => 'Estado',
        ],

        'service_delete_cascade' => [
            'modal_heading' => 'Eliminar servicio',
            'modal_intro' => 'Se eliminará de forma permanente el servicio y todos los registros relacionados indicados a continuación.',
            'modal_confirm' => 'Eliminar servicio y datos relacionados',
            'grand_total' => 'Total de filas a eliminar (incluido este servicio): :count',
            'labels' => [
                'translations' => 'Traducciones del servicio',
                'experience_assignments' => 'Asignaciones de experiencias',
                'details' => 'Filas de condición del servicio',
                'feature_links' => 'Vínculos a características',
                'variants' => 'Variantes',
                'variant_translations' => 'Traducciones de variantes',
                'variant_availability_rules' => 'Reglas de disponibilidad de variantes',
                'variant_availability_overrides' => 'Excepciones de disponibilidad',
                'price_list_items' => 'Ítems de listas de precios',
                'allocations' => 'Asignaciones de cupos',
                'service_offers' => 'Ofertas entre prestador y operador',
                'operator_package_items' => 'Ítems de paquetes del operador',
                'media_files' => 'Archivos en la biblioteca de medios',
                'hotel_type_assignments' => 'Asignaciones de tipos de hotel',
                'service_hotels' => 'Filas de perfil hotelero',
                'service_activity' => 'Filas de perfil de actividad',
                'gastronomy_menu_assignments' => 'Asignaciones de formato de menú',
                'gastronomy_venue_assignments' => 'Asignaciones de venue',
                'gastronomy_experiences' => 'Experiencias',
                'gastronomy_schedules' => 'Horarios',
                'gastronomy_capacities' => 'Capacidades',
                'cuisine_gastronomy_assignments' => 'Asignaciones de cocina',
                'gastronomy_type_assignments' => 'Asignaciones de tipos gastronómicos',
                'service_gastronomies' => 'Filas de perfil',
                'transfer_routes' => 'Rutas de traslado',
                'transfer_vehicles' => 'Vehículos de traslado',
                'transfer_prices' => 'Precios de traslado',
                'service_transfers' => 'Filas de perfil de traslado',
            ],
        ],

        'service_status' => [
            'active' => 'Activo',
            'onhold' => 'En espera',
            'suspended' => 'Suspendido',
            'discontinued' => 'Descontinuado',
            'inactive' => 'Inactivo',
            'terminated' => 'Dado de baja',
        ],

        'price_list' => 'Lista de precios',
        'price_lists' => 'Listas de precios',

        'price_list_owner_type' => [
            'account' => 'Cuenta',
            'user' => 'Usuario',
        ],

        'price_list_fields' => [
            'owner_type' => 'Tipo de propietario',
            'owner_id' => 'Propietario',
            'name' => 'Nombre',
            'currency_id' => 'Moneda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida hasta',
            'is_active' => 'Activa',
            'assignments' => 'Asignaciones',
        ],

        'price_list_tabs' => [
            'general' => 'General',
            'assignments' => 'Asignaciones',
        ],

        'price_list_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'owner' => 'Propietario',
            'currency' => 'Moneda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida hasta',
            'is_active' => 'Activa',
            'items_count' => 'Filas',
        ],

        'price_list_item' => 'Ítem de lista',
        'price_list_items' => 'Ítems de listas',

        'price_list_item_fields' => [
            'price_list_id' => 'Lista',
            'service_id' => 'Servicio (todas las variantes)',
            'service_variant_id' => 'Variante de servicio',
            'price' => 'Precio',
            'pricing_mode' => 'Modo de precio',
        ],

        'price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Lista',
            'target' => 'Objetivo',
            'service_all_variants' => 'Todas las variantes: :label',
            'service_variant' => 'Variante',
            'price' => 'Precio',
            'pricing_mode' => 'Modo',
        ],

        'price_list_item_filters' => [
            'price_list_id' => 'Lista',
        ],

        'price_list_item_pricing_mode' => [
            'fixed' => 'Fijo',
            'percentage' => 'Porcentaje',
        ],

        'price_list_assignment_fields' => [
            'operator_id' => 'Operador (cuenta)',
            'assigned_to_id' => 'Asignado a (cuenta)',
            'adjustment_type' => 'Tipo de ajuste',
            'adjustment_value' => 'Valor de ajuste',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido hasta',
            'is_active' => 'Activa',
            'add' => 'Agregar asignación',
        ],

        'price_list_assignment_adjustment_type' => [
            'none' => 'Sin ajuste',
            'percentage' => 'Porcentaje',
            'fixed' => 'Monto fijo',
        ],

        'provider_price_list' => 'Lista de precios (proveedor)',
        'provider_price_lists' => 'Listas de precios (proveedor)',

        'provider_price_list_tabs' => [
            'general' => 'General',
            'assignments' => 'Asignaciones a operadores',
        ],

        'provider_price_list_fields' => [
            'provider_id' => 'Proveedor (cuenta)',
            'name' => 'Nombre',
            'currency_id' => 'Moneda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida hasta',
            'is_active' => 'Lista activa',
            'assignments' => 'Asignaciones',
        ],

        'provider_price_list_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'provider' => 'Proveedor',
            'currency' => 'Moneda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida hasta',
            'is_active' => 'Activa',
            'items_count' => 'Filas',
        ],

        'provider_price_list_assignment_fields' => [
            'operator_id' => 'Operador (cuenta)',
            'adjustment_type' => 'Tipo de ajuste',
            'adjustment_value' => 'Valor de ajuste',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido hasta',
            'is_active' => 'Activa',
            'add' => 'Agregar asignación',
        ],

        'provider_price_list_assignment_adjustment_type' => [
            'none' => 'Sin ajuste',
            'percentage' => 'Porcentaje',
            'fixed' => 'Monto fijo',
        ],

        'provider_price_list_item' => 'Ítem de lista (proveedor)',
        'provider_price_list_items' => 'Ítems de listas (proveedor)',

        'provider_price_list_item_fields' => [
            'price_list_id' => 'Lista de precios (proveedor)',
            'service_id' => 'Servicio (todas las variantes)',
            'service_variant_id' => 'Variante de servicio',
            'price' => 'Precio',
            'pricing_mode' => 'Modo de precio',
        ],

        'provider_price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Lista',
            'target' => 'Objetivo',
            'service_all_variants' => 'Todas las variantes: :label',
            'price' => 'Precio',
            'pricing_mode' => 'Modo',
        ],

        'provider_price_list_item_filters' => [
            'price_list_id' => 'Lista',
        ],

        'provider_price_list_item_pricing_mode' => [
            'fixed' => 'Fijo',
            'percentage' => 'Porcentaje',
        ],

        'operator_price_list' => 'Lista de precios (operador)',
        'operator_price_lists' => 'Listas de precios (operador)',

        'operator_price_list_tabs' => [
            'general' => 'General',
            'assignments' => 'Asignaciones a agencias',
        ],

        'operator_price_list_fields' => [
            'operator_id' => 'Operador (cuenta)',
            'name' => 'Nombre',
            'currency_id' => 'Moneda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida hasta',
            'is_active' => 'Lista activa',
            'assignments' => 'Asignaciones',
        ],

        'operator_price_list_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'operator' => 'Operador',
            'currency' => 'Moneda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida hasta',
            'is_active' => 'Activa',
            'items_count' => 'Filas',
        ],

        'operator_price_list_assignment_fields' => [
            'agency_id' => 'Agencia (cuenta)',
            'adjustment_type' => 'Tipo de ajuste',
            'adjustment_value' => 'Valor de ajuste',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido hasta',
            'is_active' => 'Activa',
            'add' => 'Agregar asignación',
        ],

        'operator_price_list_assignment_adjustment_type' => [
            'none' => 'Sin ajuste',
            'percentage' => 'Porcentaje',
            'fixed' => 'Monto fijo',
        ],

        'operator_price_list_item' => 'Ítem de lista (operador)',
        'operator_price_list_items' => 'Ítems de listas (operador)',

        'operator_price_list_item_fields' => [
            'price_list_id' => 'Lista de precios (operador)',
            'catalog_entry_id' => 'Entrada de catálogo',
            'price' => 'Precio',
            'pricing_mode' => 'Modo de precio',
        ],

        'operator_price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Lista',
            'catalog_entry' => 'Catálogo',
            'price' => 'Precio',
            'pricing_mode' => 'Modo',
        ],

        'operator_price_list_item_filters' => [
            'price_list_id' => 'Lista',
        ],

        'operator_price_list_item_pricing_mode' => [
            'fixed' => 'Fijo',
            'percentage' => 'Porcentaje',
        ],

        'plan_user_price' => 'Precio por rango de usuarios',
        'plan_user_prices' => 'Precios por rango de usuarios',

        'plan_user_price_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'plan_user_price_fields' => [
            'up_to' => 'Hasta (cantidad de usuarios)',
            'up_to_help' => 'Ej.: 4 para el rango "1 a 4 usuarios", 10 para "5 a 10", etc.',
            'price' => 'Precio',
            'description' => 'Descripción',
        ],

        'plan_user_price_columns' => [
            'id' => 'ID',
            'up_to' => 'Hasta usuarios',
            'up_to_format' => 'Hasta :count usuarios',
            'price' => 'Precio',
        ],

        'nav_transport' => 'Traslados',

        'service_transfer_location_type_category' => 'Categoría de tipo de ubicación de traslado',
        'service_transfer_location_type_categories' => 'Categorías de tipo de ubicación de traslado',
        'service_transfer_location_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],
        'service_transfer_location_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],
        'service_transfer_location_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_transfer_location_type' => 'Tipo de ubicación de traslado',
        'service_transfer_location_types' => 'Tipos de ubicación de traslado',
        'service_transfer_location_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],
        'service_transfer_location_type_fields' => [
            'code' => 'Código',
            'category' => 'Categoría',
            'sort_order' => 'Orden',
        ],
        'service_transfer_location_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoría',
            'name' => 'Nombre',
        ],

        'service_transfer_location' => 'Ubicación de traslado',
        'service_transfer_locations' => 'Ubicaciones de traslado',
        'service_transfer_location_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],
        'service_transfer_location_fields' => [
            'service_transfer_location_type_id' => 'Tipo de ubicación',
            'address' => 'Dirección',
            'city_id' => 'Ciudad',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
            'airport_code' => 'Código IATA/aeropuerto',
            'is_active' => 'Activa',
        ],
        'service_transfer_location_columns' => [
            'id' => 'ID',
            'type' => 'Tipo',
            'name' => 'Nombre',
            'airport_code' => 'Aeropuerto',
            'city' => 'Ciudad',
        ],

        'service_transfer_vehicle_type_category' => 'Categoría de tipo de vehículo de traslado',
        'service_transfer_vehicle_type_categories' => 'Categorías de tipo de vehículo de traslado',
        'service_transfer_vehicle_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],
        'service_transfer_vehicle_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],
        'service_transfer_vehicle_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],
        'service_transfer_vehicle_type_category_relation' => [
            'vehicle_types_tab' => 'Tipos de vehículo',
        ],

        'service_transfer_vehicle_type' => 'Tipo de vehículo de traslado',
        'service_transfer_vehicle_types' => 'Tipos de vehículo de traslado',
        'service_transfer_vehicle_type_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_vehicle_type_fields' => [
            'account_id' => 'Cuenta',
            'category' => 'Categoría',
            'code' => 'Código',
            'name' => 'Nombre',
            'max_passengers' => 'Máx. pasajeros',
            'max_luggage' => 'Máx. equipaje',
        ],
        'service_transfer_vehicle_type_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'category' => 'Categoría',
            'code' => 'Código',
            'name' => 'Nombre',
            'max_passengers' => 'Máx. pax',
            'max_luggage' => 'Máx. equipaje',
        ],

        'service_transfer' => 'Perfil de traslado del servicio',
        'service_transfers' => 'Perfiles de traslado',
        'service_transfer_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_fields' => [
            'service_id' => 'Servicio',
            'transfer_type' => 'Tipo de trayecto',
            'modality' => 'Modalidad',
            'allows_multiple_stops' => 'Permite varias paradas',
            'max_passengers' => 'Máx. pasajeros',
            'max_luggage' => 'Máx. equipaje',
            'default_duration_minutes' => 'Duración por defecto (min)',
            'requires_flight_info' => 'Requiere datos de vuelo',
            'requires_pickup_time' => 'Requiere hora de recogida',
            'requires_dropoff_time' => 'Requiere hora de entrega',
        ],
        'service_transfer_columns' => [
            'id' => 'ID',
            'service' => 'Servicio',
            'transfer_type' => 'Tipo',
            'modality' => 'Modalidad',
        ],
        'service_transfer_transfer_type' => [
            'one_way' => 'Solo ida',
            'round_trip' => 'Ida y vuelta',
        ],
        'service_transfer_modality' => [
            'private' => 'Privado',
            'shared' => 'Compartido',
        ],

        'service_transfer_route' => 'Ruta de traslado',
        'service_transfer_routes' => 'Rutas de traslado',
        'service_transfer_route_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_route_fields' => [
            'service_transfer_id' => 'Perfil de traslado',
            'origin_location_id' => 'Origen',
            'destination_location_id' => 'Destino',
            'is_active' => 'Activa',
            'distance_km' => 'Distancia (km)',
            'duration_minutes' => 'Duración (min)',
        ],
        'service_transfer_route_columns' => [
            'id' => 'ID',
            'transfer' => 'Perfil',
            'origin' => 'Origen',
            'destination' => 'Destino',
        ],
        'service_transfer_route_validation' => [
            'different_endpoints' => 'Origen y destino deben ser distintos.',
        ],

        'service_transfer_vehicle' => 'Asignación de vehículo',
        'service_transfer_vehicles' => 'Asignaciones de vehículo',
        'service_transfer_vehicle_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_vehicle_fields' => [
            'service_transfer_id' => 'Perfil de traslado',
            'service_transfer_vehicle_type_id' => 'Tipo de vehículo',
            'is_default' => 'Por defecto en este perfil',
        ],
        'service_transfer_vehicle_columns' => [
            'id' => 'ID',
            'transfer' => 'Perfil',
            'vehicle_type' => 'Tipo de vehículo',
        ],

        'service_transfer_price' => 'Precio de traslado',
        'service_transfer_prices' => 'Precios de traslado',
        'service_transfer_price_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_price_fields' => [
            'service_transfer_id' => 'Perfil de traslado',
            'route_id' => 'Ruta (opcional)',
            'service_transfer_vehicle_type_id' => 'Tipo de vehículo (opcional)',
            'pricing_type' => 'Tipo de precio',
            'currency_id' => 'Moneda',
            'base_price' => 'Precio base',
            'price_per_person' => 'Precio por persona',
            'price_per_extra_passenger' => 'Precio por pasajero extra',
            'min_passengers' => 'Mín. pasajeros',
            'max_passengers' => 'Máx. pasajeros',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido hasta',
        ],
        'service_transfer_price_columns' => [
            'id' => 'ID',
            'transfer' => 'Perfil',
            'route' => 'Ruta',
            'vehicle_type' => 'Tipo vehículo',
            'pricing_type' => 'Precio',
            'currency' => 'Moneda',
            'base_price' => 'Base',
        ],
        'service_transfer_price_pricing_type' => [
            'per_vehicle' => 'Por vehículo',
            'per_person' => 'Por persona',
        ],
        'service_transfer_price_validation' => [
            'route_belongs_to_transfer' => 'La ruta debe pertenecer al perfil de traslado seleccionado.',
        ],

    ],

];

