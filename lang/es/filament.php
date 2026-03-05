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
            'account_id' => 'Cuenta',
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'roles' => 'Roles',
        ],

        'user_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'roles' => 'Roles',
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
            'sort_order' => 'Orden',
        ],

        'contact_department_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'sort_order' => 'Orden',
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
        ],

        'contact_columns' => [
            'id' => 'ID',
            'account' => 'Cuenta',
            'name' => 'Nombre',
            'department' => 'Departamento',
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

        'service_plan' => 'Plan de servicio',
        'service_plans' => 'Planes de servicio',

        'service_plan_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_plan_fields' => [
            'code' => 'Código',
            'active' => 'Activo',
            'price' => 'Precio',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ],

        'service_plan_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nombre',
            'price' => 'Precio',
            'active' => 'Activo',
        ],

        'service_plan_item' => 'Item del plan',
        'service_plan_items' => 'Items del plan',

        'service_plan_item_fields' => [
            'parent_id' => 'Item padre (subitem de)',
            'sort_order' => 'Orden',
            'active' => 'Activo',
            'text' => 'Texto',
        ],

        'service_plan_item_columns' => [
            'sort_order' => 'Orden',
            'parent' => 'Padre',
            'text' => 'Texto',
            'active' => 'Activo',
        ],

        'nav_service_plans' => 'Planes y precios',

        'service_user_price' => 'Precio por rango de usuarios',
        'service_user_prices' => 'Precios por rango de usuarios',

        'service_user_price_tabs' => [
            'general' => 'General',
            'translations' => 'Traducciones',
        ],

        'service_user_price_fields' => [
            'up_to' => 'Hasta (cantidad de usuarios)',
            'up_to_help' => 'Ej.: 4 para el rango "1 a 4 usuarios", 10 para "5 a 10", etc.',
            'price' => 'Precio',
            'description' => 'Descripción',
        ],

        'service_user_price_columns' => [
            'id' => 'ID',
            'up_to' => 'Hasta usuarios',
            'up_to_format' => 'Hasta :count usuarios',
            'price' => 'Precio',
        ],

    ],

];
