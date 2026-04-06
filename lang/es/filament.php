<?php

/**
 * Spanish translations for Filament admin (project-specific).
 * Filament's own Spanish strings come from vendor; this file is for
 * resource labels and any custom overrides. To support another language
 * later, add e.g. lang/en/filament.php and switch locale (APP_LOCALE or panel).
 */

return [

    'common' => [
        'active' => 'Activo',
        'navigation_badge_tooltip' => 'Total de registros',
        'select_option' => 'Seleccione una opción',
    ],

    'pages' => [
        'list_records_count' => 'Total: :count :label',
    ],

    'resources' => [

        'account' => 'Cuenta',
        'accounts' => 'Cuentas',

        'account_tabs' => [
            'main' => 'Datos principales',
            'tax_ids' => 'Identificaciones fiscales',
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

        'user' => 'Usuario',
        'users' => 'Usuarios',

        'user_fields' => [
            'accounts' => 'Cuentas',
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'roles' => 'Roles',
        ],

        'user_columns' => [
            'id' => 'ID',
            'accounts' => 'Cuentas',
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'roles' => 'Roles',
        ],

        'role' => 'Rol',
        'roles' => 'Roles',

        'role_fields' => [
            'name' => 'Nombre',
            'permissions' => 'Permisos',
        ],

        'role_columns' => [
            'id' => 'ID',
            'name' => 'Nombre',
            'permissions_count' => 'Permisos',
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

        'parameter' => 'Parámetro',
        'parameters' => 'Parámetros',

        'parameter_fields' => [
            'category' => 'Categoría',
            'code' => 'Código',
            'type' => 'Tipo',
            'value' => 'Valor',
            'mode' => 'Modo',
            'help' => 'Ayuda',
            'comments' => 'Comentarios',
        ],

        'parameter_columns' => [
            'id' => 'ID',
            'category' => 'Categoría',
            'code' => 'Código',
            'type' => 'Tipo',
            'value' => 'Valor',
            'mode' => 'Modo',
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

        'provider' => 'Proveedor',
        'providers' => 'Proveedores',

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
            'city_id' => 'Ciudad',
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

        'provider_category' => 'Categoría de proveedor',
        'provider_categories' => 'Categorías de proveedor',

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

        'parameter_definition' => 'Definición de parámetro',
        'parameter_definitions' => 'Definiciones de parámetros',

        'parameter_definition_fields' => [
            'category' => 'Categoría',
            'code' => 'Código',
            'type' => 'Tipo',
            'scope' => 'Alcance',
            'has_default' => 'Tiene valor por defecto',
            'ui_component' => 'Componente UI',
            'ui_options' => 'Opciones UI',
            'help' => 'Ayuda',
            'comments' => 'Comentarios',
        ],

        'parameter_definition_columns' => [
            'id' => 'ID',
            'category' => 'Categoría',
            'code' => 'Código',
            'type' => 'Tipo',
            'scope' => 'Alcance',
            'has_default' => 'Por defecto',
            'ui_component' => 'UI',
        ],

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
            'definition_help' => 'Qué parámetro configura esta fila.',
            'account_help' => 'Obligatorio si el alcance es por cuenta; no aplica al valor de sistema.',
            'value_help' => 'Valor almacenado (el formato depende del tipo de la definición).',
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
        ],

        'plan_fields' => [
            'code' => 'Código',
            'active' => 'Activo',
            'price' => 'Precio',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'price' => 'Precio',
            'active' => 'Activo',
        ],

        'plan_item' => 'Item del plan',
        'plan_items' => 'Items del plan',

        'plan_item_fields' => [
            'parent_id' => 'Item padre (subitem de)',
            'sort_order' => 'Orden',
            'active' => 'Activo',
            'text' => 'Texto',
        ],

        'plan_item_columns' => [
            'sort_order' => 'Orden',
            'parent' => 'Padre',
            'text' => 'Texto',
            'active' => 'Activo',
        ],

        'nav_contacts' => 'Contactos',
        'nav_plans' => 'Planes y precios',
        'nav_services' => 'Servicios (prestador)',
        'nav_hotels' => 'Hoteles',
        'nav_excursions' => 'Excursiones',
        'nav_gastronomy' => 'Gastronomía',
        'nav_parameters' => 'Configuración',
        'nav_users' => 'Usuarios',
        'nav_authorization' => 'Autorización',

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

        'service_gastronomy_type' => 'Tipo de gastronomía',
        'service_gastronomy_types' => 'Tipos de gastronomía',

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

        'service_gastronomy_venue' => 'Venue de gastronomía',
        'service_gastronomy_venues' => 'Venues de gastronomía',

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

        'service_gastronomy_cuisine' => 'Cocina / gastronomía',
        'service_gastronomy_cuisines' => 'Cocinas / gastronomías',

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

        'service_gastronomy_menu' => 'Menú de gastronomía',
        'service_gastronomy_menus' => 'Menús de gastronomía',

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

        'service_gastronomy_menu_category' => 'Categoría de menú de gastronomía',
        'service_gastronomy_menu_categories' => 'Categorías de menú de gastronomía',

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

        'service_gastronomy_feature_category' => 'Categoría de característica de gastronomía',
        'service_gastronomy_feature_categories' => 'Categorías de características de gastronomía',

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

        'service_gastronomy_feature' => 'Característica de gastronomía',
        'service_gastronomy_features' => 'Características de gastronomía',

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
            'navigation_label' => 'Scopes de características (por tipo)',
            'title' => 'Gestionar scopes de características por tipo de servicio',
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
                'saved' => 'Scopes de características guardados.',
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
            'sort_order' => 'Orden',
        ],

        'service_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'sort_order' => 'Orden',
        ],

        'service_activity' => 'Actividad de servicio',
        'service_activities' => 'Actividades de servicio',

        'service_activity_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_activity_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_activity_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_activity_category' => 'Categoría de actividad',
        'service_activity_categories' => 'Categorías de actividad',

        'service_activity_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_activity_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_activity_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_excursion_type' => 'Tipo de excursión',
        'service_excursion_types' => 'Tipos de excursión',

        'service_excursion_type_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_excursion_type_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_excursion_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'category' => 'Categoría',
        ],

        'service_excursion_type_category' => 'Categoría de tipo de excursión',
        'service_excursion_type_categories' => 'Categorías de tipo de excursión',

        'service_excursion_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_excursion_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_excursion_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'service_excursion' => 'Servicio de excursión',
        'service_excursions' => 'Servicios de excursión',

        'service_excursion_tabs' => [
            'general' => 'General',
            'technical' => 'Técnico',
        ],

        'service_excursion_fields' => [
            'service_id' => 'Servicio',
            'service_excursion_type_id' => 'Tipo de excursión',
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

        'service_excursion_columns' => [
            'id' => 'ID',
            'service' => 'Servicio',
            'type' => 'Tipo',
            'difficulty' => 'Dificultad',
        ],

        'service_excursion_difficulty' => [
            'easy' => 'Fácil',
            'moderate' => 'Moderada',
            'difficult' => 'Difícil',
        ],

        'service_detail_topic' => 'Tema de detalle',
        'service_detail_topics' => 'Temas de detalle',

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

        'service_detail_topic_category' => 'Categoría de tema de detalle',
        'service_detail_topic_categories' => 'Categorías de tema de detalle',

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

        'service_detail' => 'Detalle',
        'service_details' => 'Detalles',

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
            'add' => 'Añadir detalle',
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
            'translations' => 'Traducciones',
            'variants' => 'Variantes',
            'details' => 'Detalles',
            'activities' => 'Actividades',
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
            'city_id' => 'Ciudad',
            'status' => 'Estado',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'activities' => 'Actividades',
            'activities_help' => 'Seleccione las actividades que aplican a este servicio.',
        ],

        'service_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'service_type' => 'Tipo de servicio',
            'name' => 'Nombre',
            'status' => 'Estado',
        ],

        'service_status' => [
            'active' => 'Activo',
            'onhold' => 'En espera',
            'inactive' => 'Inactivo',
            'terminated' => 'Dado de baja',
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

    ],

];
