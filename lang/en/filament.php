<?php

/**
 * English translations for Filament admin (project-specific).
 * Used when APP_LOCALE=en or when switching language to English.
 */

return [

    'common' => [
        'active' => 'Active',
    ],

    'pages' => [
        'list_records_count' => 'Total: :count :label',
    ],

    'resources' => [

        'account' => 'Account',
        'accounts' => 'Accounts',

        'account_tabs' => [
            'main' => 'Main data',
            'tax_ids' => 'Tax IDs',
        ],

        'account_fields' => [
            'nick' => 'Nick',
            'code' => 'Code',
            'name' => 'Name',
            'commercial_name' => 'Commercial name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address_line1' => 'Address (line 1)',
            'address_line2' => 'Address (line 2)',
            'postal_code' => 'Postal code',
            'code_help' => 'Auto-generated on create.',
        ],

        'account_columns' => [
            'id' => 'ID',
            'nick' => 'Nick',
            'code' => 'Code',
            'name' => 'Name',
            'commercial_name' => 'Commercial name',
            'email' => 'Email',
        ],

        'account_category' => 'Account category',
        'account_categories' => 'Account categories',

        'account_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'account_category_fields' => [
            'group' => 'Group',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'sort_order' => 'Sort order',
            'language' => 'Language',
        ],

        'account_category_columns' => [
            'id' => 'ID',
            'group' => 'Group',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'sort_order' => 'Sort order',
        ],

        'user' => 'User',
        'users' => 'Users',

        'user_fields' => [
            'account_id' => 'Account',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'roles' => 'Roles',
        ],

        'user_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'name' => 'Name',
            'email' => 'Email',
            'roles' => 'Roles',
        ],

        'parameter' => 'Parameter',
        'parameters' => 'Parameters',

        'parameter_fields' => [
            'category' => 'Category',
            'code' => 'Code',
            'type' => 'Type',
            'value' => 'Value',
            'mode' => 'Mode',
            'help' => 'Help',
            'comments' => 'Comments',
        ],

        'parameter_columns' => [
            'id' => 'ID',
            'category' => 'Category',
            'code' => 'Code',
            'type' => 'Type',
            'value' => 'Value',
            'mode' => 'Mode',
        ],

        'account_tax_id' => 'Tax ID',
        'account_tax_ids' => 'Tax IDs',

        'account_tax_id_fields' => [
            'account_id' => 'Account',
            'account_category_id' => 'Type / Category',
            'value' => 'Value',
            'add' => 'Add Tax ID',
        ],

        'account_tax_id_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'category' => 'Category',
            'value' => 'Value',
        ],

        'contact_department' => 'Contact department',
        'contact_departments' => 'Contact departments',

        'contact_department_fields' => [
            'code' => 'Code',
            'sort_order' => 'Sort order',
        ],

        'contact_department_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'sort_order' => 'Sort order',
        ],

        'contact_type' => 'Contact type',
        'contact_types' => 'Contact types',

        'contact_type_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'mask' => 'Mask',
            'mask_help' => 'Mask for formatting the value (e.g. phone, document).',
            'validation' => 'Validation',
            'validation_help' => 'Validation rule or pattern for the value.',
        ],

        'contact_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'mask' => 'Mask',
            'validation' => 'Validation',
        ],

        'contact' => 'Contact',
        'contacts' => 'Contacts',

        'contact_fields' => [
            'account_id' => 'Account',
            'name' => 'Name',
            'contact_department_id' => 'Department',
        ],

        'contact_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'name' => 'Name',
            'department' => 'Department',
        ],

        'provider' => 'Provider',
        'providers' => 'Providers',

        'provider_tabs' => [
            'main' => 'Main data',
            'categories' => 'Categories',
        ],

        'provider_fields' => [
            'name' => 'Name',
            'commercial_name' => 'Commercial name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address_line1' => 'Address (line 1)',
            'address_line2' => 'Address (line 2)',
            'city_id' => 'City',
            'postal_code' => 'Postal code',
            'status' => 'Status',
            'inviting_id' => 'Inviting account',
            'categories' => 'Categories',
        ],

        'provider_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'commercial_name' => 'Commercial name',
            'email' => 'Email',
            'status' => 'Status',
            'inviting' => 'Inviting account',
        ],

        'provider_status' => [
            'active' => 'Active',
            'onhold' => 'On hold',
            'inactive' => 'Inactive',
            'terminated' => 'Terminated',
        ],

        'provider_category' => 'Provider category',
        'provider_categories' => 'Provider categories',

        'provider_category_fields' => [
            'group' => 'Group',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'sort_order' => 'Sort order',
        ],

        'provider_category_columns' => [
            'id' => 'ID',
            'group' => 'Group',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'language' => 'Language',
        'languages' => 'Languages',

        'language_fields' => [
            'language' => 'Language',
        ],

        'language_columns' => [
            'id' => 'ID',
            'language' => 'Language',
            'code' => 'Code',
        ],

        'service_plan' => 'Service plan',
        'service_plans' => 'Service plans',

        'service_plan_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_plan_fields' => [
            'code' => 'Code',
            'active' => 'Active',
            'price' => 'Price',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_plan_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'price' => 'Price',
            'active' => 'Active',
        ],

        'service_plan_item' => 'Plan item',
        'service_plan_items' => 'Plan items',

        'service_plan_item_fields' => [
            'parent_id' => 'Parent item',
            'sort_order' => 'Sort order',
            'active' => 'Active',
            'text' => 'Text',
        ],

        'service_plan_item_columns' => [
            'sort_order' => 'Sort order',
            'parent' => 'Parent',
            'text' => 'Text',
            'active' => 'Active',
        ],

        'nav_service_plans' => 'Plans and pricing',

        'service_user_price' => 'User range price',
        'service_user_prices' => 'User range prices',

        'service_user_price_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_user_price_fields' => [
            'up_to' => 'Up to (number of users)',
            'up_to_help' => 'E.g. 4 for "1 to 4 users", 10 for "5 to 10", etc.',
            'price' => 'Price',
            'description' => 'Description',
        ],

        'service_user_price_columns' => [
            'id' => 'ID',
            'up_to' => 'Up to users',
            'up_to_format' => 'Up to :count users',
            'price' => 'Price',
        ],

    ],

];
