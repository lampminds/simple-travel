<?php

/**
 * English translations for Filament admin (project-specific).
 * Used when APP_LOCALE=en or when switching language to English.
 */

return [

    'common' => [
        'active' => 'Active',
        'navigation_badge_tooltip' => 'Total records',
        'select_option' => 'Select an option',
    ],

    'pages' => [
        'list_records_count' => 'Total: :count :label',
        'website_menu_editor' => [
            'nav_label' => 'Website menu',
            'title' => 'Website menu editor',
            'header_action' => 'Visual editor',
            'section_heading' => 'Tree',
            'hint' => 'Use the arrows to reorder items among siblings. Open a row to edit labels, route names, visibility, and which account types see it.',
            'move_up' => 'Move up',
            'move_down' => 'Move down',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

    'resources' => [

        'account' => 'Account',
        'accounts' => 'Accounts',

        'account_tabs' => [
            'main' => 'Main data',
            'tax_ids' => 'Tax IDs',
            'categories' => 'Categories',
        ],

        'account_type_category_fields' => [
            'label' => 'Account types',
            'help' => 'One or more business types (e.g. provider, agency). Only categories with group “type” are listed.',
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
            'city_id' => 'City',
            'state_id' => 'State',
            'country_id' => 'Country',
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
            'account_category' => 'Account category',
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

        'user_tabs' => [
            'general' => 'Profile',
            'accounts_roles' => 'Accounts & roles',
        ],

        'user_fields' => [
            'accounts' => 'Accounts',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'roles' => 'Roles',
            'memberships_heading' => 'Account memberships',
            'memberships_help' => 'Add one row per account. Choose the account first, then the roles for that team (Spatie permission teams use account_id).',
            'account' => 'Account',
            'add_membership' => 'Add account',
        ],

        'user_columns' => [
            'id' => 'ID',
            'accounts' => 'Accounts',
            'name' => 'Name',
            'email' => 'Email',
            'roles' => 'Roles',
        ],

        'user_invitation' => 'Invitation',
        'user_invitations' => 'Invitations',

        'user_invitation_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'email' => 'Email',
            'type' => 'Type',
            'status' => 'Status',
            'expires_at' => 'Expires at',
            'invited_by' => 'Invited by',
        ],

        'user_invitation_fields' => [
            'account_id' => 'Account',
            'email' => 'Email',
            'type' => 'Type',
            'status' => 'Status',
            'expires_at' => 'Expires at',
            'invited_by' => 'Invited by',
            'token' => 'Token',
            'accepted_at' => 'Accepted at',
            'declined_at' => 'Declined at',
        ],

        'user_invitation_filters' => [
            'type' => 'Type',
            'status' => 'Status',
        ],

        'role' => 'Role',
        'roles' => 'Roles',

        'role_fields' => [
            'name' => 'Name',
            'permissions' => 'Permissions',
        ],

        'role_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'permissions_count' => 'Permissions',
        ],

        'permission' => 'Permission',
        'permissions' => 'Permissions',

        'permission_fields' => [
            'name' => 'Permission name',
            'name_help' => 'Use a stable identifier (e.g. manage_services). Guard is web.',
        ],

        'permission_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'roles_count' => 'Roles',
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
            'name' => 'Name',
            'sort_order' => 'Sort order',
        ],

        'contact_department_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'sort_order' => 'Sort order',
        ],

        'contact_position' => 'Contact position',
        'contact_positions' => 'Contact positions',

        'contact_position_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'contact_position_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'contact_position_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
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
            'name' => 'Name',
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
            'contact_position_id' => 'Position',
        ],

        'contact_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'name' => 'Name',
            'department' => 'Department',
            'position' => 'Position',
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
            'city_id' => 'City code',
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

        'currency' => 'Currency',
        'currencies' => 'Currencies',

        'currency_fields' => [
            'currency' => 'Currency',
        ],

        'currency_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'symbol' => 'Symbol',
            'name' => 'Name',
        ],

        'menu' => 'Menu item',
        'menus' => 'Website menus',

        'menu_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
            'account_types' => 'Account types',
        ],

        'menu_fields' => [
            'slug' => 'Slug',
            'slug_help' => 'Internal key (unique). Used in code, not necessarily shown on the public site.',
            'parent_id' => 'Parent',
            'icon' => 'Icon',
            'route' => 'Route name',
            'translation_name' => 'Label',
            'translation_tip' => 'Tooltip',
            'account_types' => 'Visible for account types',
            'account_types_help' => 'If none are selected, this item is hidden for every account type.',
        ],

        'menu_columns' => [
            'id' => 'ID',
            'label' => 'Label',
            'route' => 'Route',
            'parent' => 'Parent',
            'account_types' => 'Account types',
            'account_types_none' => 'None',
        ],

        'menu_duplicate' => 'Duplicate',

        'menu_filter' => [
            'scope' => 'Scope',
            'all_levels' => 'All levels',
            'root_only' => 'Root items only',
            'children_of' => 'Children of: :label',
            'account_type' => 'Account type',
            'account_type_placeholder' => 'All types',
            'active_status' => 'Status',
            'active_all' => 'All',
            'active_only' => 'Active only',
            'inactive_only' => 'Inactive only',
        ],

        'menu_validation' => [
            'parent_cycle' => 'That parent would create a circular hierarchy.',
        ],

        'parameter_definition' => 'Parameter definition',
        'parameter_definitions' => 'Parameter definitions',

        'parameter_definition_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
            'options' => 'Options',
            'values' => 'Values',
        ],

        'parameter_definition_fields' => [
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'code' => 'Code',
            'type' => 'Type',
            'scope' => 'Scope',
            'has_default' => 'Has default',
            'ui_component' => 'UI component',
            'ui_options' => 'UI options',
            'sort_order' => 'Sort order',
            'default_value' => 'Default value',
            'validation_rules' => 'Validation rules',
            'translation_name' => 'Name',
            'translation_description' => 'Description',
            'translation_help' => 'Help',
            'comments' => 'Comments',
        ],

        'parameter_definition_columns' => [
            'id' => 'ID',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'code' => 'Code',
            'name' => 'Name',
            'type' => 'Type',
            'scope' => 'Scope',
            'has_default' => 'Has default',
            'ui_component' => 'UI component',
        ],

        'parameter_option_fields' => [
            'value' => 'Stored value',
            'sort_order' => 'Sort order',
            'label' => 'Label',
            'labels' => 'Labels by language',
            'add' => 'Add option',
        ],

        'parameter_definition_options_help' => 'For select, radio, checkbox, and switch components you must define at least two options (e.g. two explicit values for yes/no). Other UI types can leave this list empty and store free-text values.',
        'parameter_definition_options_min_two' => 'This UI component requires at least two options with a stored value.',
        'parameter_definition_values_tab_help' => 'System-scoped definitions: at most one row (account is ignored). Tenant-scoped: optional account — leave empty for one default for all accounts, or set an account per override row.',
        'parameter_definition_values_duplicate_account' => 'Duplicate account in the values list.',

        'parameter_definition_ui_components' => [
            'input' => 'Text input',
            'select' => 'Select',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio',
            'switch' => 'Switch',
            'textarea' => 'Textarea',
            'editor' => 'Rich editor',
            'date' => 'Date',
            'datetime' => 'Date & time',
            'time' => 'Time',
        ],

        'parameter_value' => 'Parameter value',
        'parameter_values' => 'Parameter values',

        'parameter_value_fields' => [
            'parameter_definition_id' => 'Definition',
            'account_id' => 'Account',
            'value' => 'Value',
            'add_row' => 'Add value',
            'definition_help' => 'Which parameter this row configures.',
            'account_placeholder' => 'Default (all accounts)',
            'account_help_system' => 'System-scoped definitions do not store an account; this is always empty.',
            'account_help' => 'Optional. Leave empty for one default row for all accounts, or choose an account for a per-account override.',
            'value_help' => 'Stored value (free text, or one of the predefined options when the definition uses a list).',
        ],

        'parameter_value_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'value' => 'Value',
        ],

        'parameter_value_duplicate' => 'A value already exists for this definition and account (or for the system default).',
        'parameter_value_account_system' => 'System',

        'plan' => 'Plan',
        'plans' => 'Plans',

        'plan_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
            'items' => 'Plan items',
        ],

        'plan_fields' => [
            'code' => 'Code',
            'active' => 'Active',
            'price' => 'Price',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'price' => 'Price',
            'active' => 'Active',
        ],

        'plan_item' => 'Plan item',
        'plan_items' => 'Plan items',
        'plan_items_standalone' => 'Plan items',

        'plan_item_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'plan_item_standalone_columns' => [
            'id' => 'ID',
            'plan' => 'Plan',
            'parent' => 'Parent (top level)',
            'text' => 'Text',
        ],

        'plan_item_standalone_filter_parent_with_children' => 'Parent item (has sub-items)',

        'plan_item_fields' => [
            'plan_id' => 'Plan',
            'parent_id' => 'Parent item',
            'parent_root' => '— Top level (no parent) —',
            'untitled_row' => 'Untitled item',
            'add_row' => 'Add plan item',
            'sort_order' => 'Sort order',
            'active' => 'Active',
            'text' => 'Text',
        ],

        'plan_items_repeater_help' => 'Add root items first, then sub-items and choose a top-level parent. Drag rows to change order.',

        'plan_item_columns' => [
            'sort_order' => 'Sort order',
            'parent' => 'Parent',
            'text' => 'Text',
            'active' => 'Active',
        ],

        'nav_contacts' => 'Contacts',
        'nav_plans' => 'Plans and pricing',
        'nav_services' => 'Services (provider)',
        'nav_hotels' => 'Hotels',
        'nav_excursions' => 'Excursions',
        'nav_gastronomy' => 'Gastronomy',
        'nav_parameters' => 'Settings',
        'nav_users' => 'Users',
        'nav_authorization' => 'Authorization',
        'nav_onboarding' => 'Getting started',

        'todo_category' => 'To-do category',
        'todo_categories' => 'To-do categories',

        'todo_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'todo_category_fields' => [
            'code' => 'Code',
            'sort_order' => 'Sort order',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'todo_category_columns' => [
            'id' => 'ID',
            'sort_order' => 'Sort order',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'todo_task' => 'To-do task (template)',
        'todo_tasks' => 'To-do tasks (templates)',

        'todo_task_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'todo_task_fields' => [
            'account_id' => 'Account (system)',
            'code' => 'Code',
            'todo_category_id' => 'Category',
            'original_task_id' => 'Based on task (optional)',
            'action_type' => 'Action type',
            'action_url' => 'URL (for link)',
            'sort_order' => 'Sort order',
            'name' => 'Title',
            'description' => 'Description',
        ],

        'todo_task_action_types' => [
            'link' => 'Open link',
            'api_check' => 'API check',
            'manual' => 'Manual',
        ],

        'todo_task_columns' => [
            'id' => 'ID',
            'sort_order' => 'Sort order',
            'code' => 'Code',
            'category' => 'Category',
            'name' => 'Title',
            'action_type' => 'Action',
        ],

        'service_hotel_type' => 'Hotel type',
        'service_hotel_types' => 'Hotel types',

        'service_hotel_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_hotel_type_fields' => [
            'code' => 'Code',
            'category' => 'Category',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_hotel_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'category' => 'Category',
            'name' => 'Name',
        ],

        'service_hotel_type_category' => 'Hotel type category',
        'service_hotel_type_categories' => 'Hotel type categories',

        'service_hotel_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_hotel_type_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_hotel_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_type' => 'Gastronomy type',
        'service_gastronomy_types' => 'Gastronomy types',

        'service_gastronomy_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_type_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_gastronomy_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_venue' => 'Gastronomy venue',
        'service_gastronomy_venues' => 'Gastronomy venues',

        'service_gastronomy_venue_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_venue_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_venue_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_cuisine' => 'Cuisine',
        'service_gastronomy_cuisines' => 'Cuisines',

        'service_gastronomy_cuisine_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_cuisine_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_cuisine_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_menu' => 'Gastronomy menu',
        'service_gastronomy_menus' => 'Gastronomy menus',

        'service_gastronomy_menu_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_menu_fields' => [
            'code' => 'Code',
            'is_default' => 'Default menu',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_gastronomy_menu_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'is_default' => 'Default',
            'name' => 'Name',
        ],

        'service_gastronomy_menu_category' => 'Gastronomy menu category',
        'service_gastronomy_menu_categories' => 'Gastronomy menu categories',

        'service_gastronomy_menu_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_menu_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_menu_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_feature_category' => 'Gastronomy feature category',
        'service_gastronomy_feature_categories' => 'Gastronomy feature categories',

        'service_gastronomy_feature_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_feature_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_feature_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_feature' => 'Gastronomy feature',
        'service_gastronomy_features' => 'Gastronomy features',

        'service_gastronomy_feature_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_gastronomy_feature_fields' => [
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_gastronomy_feature_columns' => [
            'id' => 'ID',
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_feature_category' => 'Feature category',
        'service_feature_categories' => 'Feature categories',

        'service_feature_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_feature_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_feature_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_feature' => 'Feature',
        'service_features' => 'Features',

        'service_feature_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
            'scopes' => 'Scopes',
        ],

        'service_feature_fields' => [
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'scopes' => 'Scopes',
            'is_selectable' => 'Selectable',
            'parent_id' => 'Parent',
        ],

        'service_feature_columns' => [
            'id' => 'ID',
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
            'parent' => 'Parent',
        ],
        
        'service_feature_parent_none' => 'No parent',
        'service_feature_set_parent' => 'Set parent',
        'service_feature_set_parent_failure_title' => 'Invalid parent assignment',
        'service_feature_set_parent_success_title' => 'Parent updated',
        'service_feature_set_parent_failure_body_self' => 'You cannot assign a feature as a parent of itself.',
        'service_feature_set_parent_failure_body_cycle' => 'You cannot assign a parent that would create a hierarchy loop (recursion).',

        'service_feature_scope' => 'Feature scope',
        'service_feature_scopes' => 'Feature scopes',

        'service_feature_scope_fields' => [
            'type' => 'Type',
            'feature' => 'Feature',
        ],

        'service_feature_scope_columns' => [
            'type' => 'Type',
            'feature' => 'Feature',
        ],

        'service_feature_scope_filters' => [
            'type' => 'Type',
            'feature' => 'Feature',
        ],

        'service_feature_scope_validation' => [
            'unique_pair' => 'This type-feature combination already exists.',
        ],

        'manage_service_feature_scopes' => [
            'navigation_label' => 'Feature scopes (by type)',
            'title' => 'Manage feature scopes by service type',
            'service_type' => 'Service type',
            'section_categories' => 'Feature categories',
            'help_categories' => 'Only features in the checked categories are listed below. Use the bulk toggle in the list header to select all or none.',
            'feature_categories' => 'Categories to include',
            'section_in_scope' => 'In scope for this type',
            'section_available' => 'Available to add',
            'help_in_scope' => 'Uncheck features to remove them from this type’s scope. Changes apply when you save.',
            'help_available' => 'Check features to include them in this type’s scope when you save.',
            'in_scope' => 'Features in scope',
            'available' => 'Features not in scope',
            'actions' => [
                'save' => 'Save scopes',
            ],
            'notifications' => [
                'saved' => 'Feature scopes saved.',
            ],
        ],

        'service_type' => 'Service type',
        'service_types' => 'Service types',

        'service_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_type_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'sort_order' => 'Sort order',
        ],

        'service_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'sort_order' => 'Sort order',
        ],

        'service_activity' => 'Service activity',
        'service_activities' => 'Service activities',

        'service_activity_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_activity_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_activity_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_activity_category' => 'Service activity category',
        'service_activity_categories' => 'Service activity categories',

        'service_activity_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_activity_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_activity_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_excursion_type' => 'Excursion type',
        'service_excursion_types' => 'Excursion types',

        'service_excursion_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_excursion_type_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_excursion_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_excursion_type_category' => 'Excursion type category',
        'service_excursion_type_categories' => 'Excursion type categories',

        'service_excursion_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_excursion_type_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_excursion_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_excursion' => 'Excursion service',
        'service_excursions' => 'Excursion services',

        'service_excursion_tabs' => [
            'general' => 'General',
            'technical' => 'Technical',
        ],

        'service_excursion_fields' => [
            'service_id' => 'Service',
            'service_excursion_type_id' => 'Excursion type',
            'difficulty_level' => 'Difficulty',
            'min_age' => 'Min age',
            'max_age' => 'Max age',
            'guide_included' => 'Guide included',
            'transport_included' => 'Transport included',
            'outdoor_activity' => 'Outdoor activity',
            'requires_good_weather' => 'Requires good weather',
            'max_altitude_m' => 'Max altitude (m)',
            'distance_km' => 'Distance (km)',
        ],

        'service_excursion_columns' => [
            'id' => 'ID',
            'service' => 'Service',
            'type' => 'Type',
            'difficulty' => 'Difficulty',
        ],

        'service_excursion_difficulty' => [
            'easy' => 'Easy',
            'moderate' => 'Moderate',
            'difficult' => 'Difficult',
        ],

        'service_detail_topic' => 'Detail topic',
        'service_detail_topics' => 'Detail topics',

        'service_detail_topic_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_detail_topic_fields' => [
            'code' => 'Code',
            'category' => 'Category',
            'visibility' => 'Visibility',
            'active' => 'Active',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_detail_topic_visibility' => [
            'public' => 'Public',
            'operator' => 'Operator',
            'internal' => 'Internal',
        ],

        'service_detail_topic_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'category' => 'Category',
            'name' => 'Name',
            'visibility' => 'Visibility',
            'active' => 'Active',
        ],

        'service_detail_topic_category' => 'Detail topic category',
        'service_detail_topic_categories' => 'Detail topic categories',

        'service_detail_topic_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_detail_topic_category_fields' => [
            'code' => 'Code',
            'active' => 'Active',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_detail_topic_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'active' => 'Active',
        ],

        'service_detail' => 'Detail',
        'service_details' => 'Details',

        'service_detail_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_detail_fields' => [
            'service_id' => 'Service',
            'service_detail_topic_id' => 'Topic',
            'language_id' => 'Language',
            'description' => 'Description',
            'active' => 'Active',
            'sort_order' => 'Sort order',
            'add' => 'Add detail',
        ],

        'service_detail_columns' => [
            'id' => 'ID',
            'service' => 'Service',
            'topic' => 'Topic',
            'language' => 'Language',
            'description' => 'Description',
            'active' => 'Active',
        ],

        'service' => 'Service',
        'services' => 'Services',

        'service_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
            'variants' => 'Variants',
            'details' => 'Details',
            'activities' => 'Activities',
            'media' => 'Images',
        ],

        'service_media' => [
            'main_image' => 'Main image',
            'gallery' => 'Gallery (slider)',
            'gallery_help' => 'Optional images for the slider. Drag to reorder.',
            'max_image_size_hint' => 'Maximum 3 MB per image.',
        ],

        'service_variant_fields' => [
            'add' => 'Add variant',
            'sku' => 'SKU / Code',
            'status' => 'Status',
            'capacity_min' => 'Min capacity',
            'capacity_max' => 'Max capacity',
            'duration_minutes' => 'Duration (minutes)',
            'pricing_type' => 'Pricing type',
            'base_price' => 'Base price',
            'currency' => 'Currency',
            'inventory_type' => 'Inventory type',
            'inventory_total' => 'Inventory total',
            'booking_type' => 'Booking type',
            'min_advance_booking_hours' => 'Min advance (hours)',
            'max_advance_booking_days' => 'Max advance (days)',
            'start_time' => 'Start time',
            'end_time' => 'End time',
            'sort_order' => 'Sort order',
        ],

        'service_variant_status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'hidden' => 'Hidden',
        ],

        'service_variant_pricing_type' => [
            'per_person' => 'Per person',
            'per_unit' => 'Per unit',
            'per_room' => 'Per room',
            'per_vehicle' => 'Per vehicle',
        ],

        'service_variant_inventory_type' => [
            'unlimited' => 'Unlimited',
            'fixed' => 'Fixed',
            'request' => 'On request',
        ],

        'service_variant_booking_type' => [
            'instant' => 'Instant',
            'request' => 'On request',
        ],

        'service_fields' => [
            'account_id' => 'Account',
            'service_type_id' => 'Service type',
            'city_id' => 'City code',
            'status' => 'Status',
            'name' => 'Name',
            'description' => 'Description',
            'activities' => 'Activities',
            'activities_help' => 'Select the activities that apply to this service.',
        ],

        'service_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'service_type' => 'Service type',
            'name' => 'Name',
            'status' => 'Status',
        ],

        'service_status' => [
            'active' => 'Active',
            'onhold' => 'On hold',
            'inactive' => 'Inactive',
            'terminated' => 'Terminated',
        ],

        'plan_user_price' => 'User range price',
        'plan_user_prices' => 'User range prices',

        'plan_user_price_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'plan_user_price_fields' => [
            'up_to' => 'Up to (number of users)',
            'up_to_help' => 'E.g. 4 for "1 to 4 users", 10 for "5 to 10", etc.',
            'price' => 'Price',
            'description' => 'Description',
        ],

        'plan_user_price_columns' => [
            'id' => 'ID',
            'up_to' => 'Up to users',
            'up_to_format' => 'Up to :count users',
            'price' => 'Price',
        ],

    ],

];

