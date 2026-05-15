<?php

/**
 * English translations for Filament admin (project-specific).
 * Used when APP_LOCALE=en or when switching language to English.
 */

return [

    'clusters' => [
        'accounts' => 'Accounts',
        'catalog' => 'Catalog',
        'gastronomy' => 'Gastronomy',
        'hospitality' => 'Accommodation',
        'experiences' => 'Experiences',
        'crm' => 'CRM',
        'commercial' => 'Commercial',
        'administration' => 'Administration',
        'transport' => 'Transport',
    ],

    'common' => [
        'active' => 'Active',
        'view' => 'View',
        'close' => 'Close',
        'copy' => 'Copy',
        'code_copied' => 'Code copied to clipboard',
        'code_copy_failed' => 'Could not copy (browser blocked or unsupported).',
        'click_to_copy_code' => 'Click to copy this code',
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
            'business_types' => 'Business types',
        ],

        'account_type_category_fields' => [
            'label' => 'Account types',
            'help' => 'One or more business types (e.g. provider, agency). Managed from Account types under Parameters.',
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
            'account_type' => 'Account type',
        ],

        'user_actions' => [
            'open_website_impersonation' => 'Website access link',
            'open_website_impersonation_tooltip' => 'Generate a one-time link to open the website in another browser as this user.',
            'impersonation_modal_heading' => 'One-time website access link',
            'impersonation_modal_help' => 'Copy the link and open it in another browser (or a private window). The link expires in a few minutes and works once.',
            'impersonation_forbidden' => 'You are not allowed to generate this link.',
            'impersonation_invalid_target' => 'This user cannot be used for this link.',
            'impersonation_link_aria' => 'One-time access link',
            'impersonation_link_label' => 'Link',
            'impersonation_copy_button' => 'Copy',
            'impersonation_copied' => 'Copied',
            'impersonation_copy_failed' => 'Copy failed',
            'impersonation_copy_hint' => 'Tip: you can also triple-click the box and press Ctrl+C (Cmd+C on Mac).',
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

        'account_type' => 'Account type',
        'account_types' => 'Account types',

        'account_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'account_type_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'account_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
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
        'user_filters' => [
            'account' => 'Company',
        ],

        'user_invitation' => 'Invitation',
        'user_invitations' => 'Invitations',

        'user_invitation_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'account_inviting' => 'Inviting account',
            'email' => 'Email',
            'name' => 'Invitee name',
            'role' => 'Role',
            'type' => 'Type',
            'status' => 'Status',
            'expires_at' => 'Expires at',
            'invited_by' => 'Invited by',
        ],

        'user_invitation_fields' => [
            'account_id' => 'Account',
            'account_inviting' => 'Inviting account',
            'account_inviting_helper' => 'The account that sent this invitation (e.g. operator). Defaults to the same as Account when left empty on create.',
            'email' => 'Email',
            'name' => 'Invitee name',
            'role_id' => 'Role',
            'role_external_owner' => 'owner (new company)',
            'role_id_external_helper' => 'External invitations always use the owner role for the new company that is created at registration.',
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

        'account_relationship' => 'Commercial relationship',
        'account_relationships' => 'Commercial relationships',

        'account_relationship_columns' => [
            'id' => 'ID',
            'operator_account' => 'Operator',
            'provider_account' => 'Provider',
            'status' => 'Status',
            'created_via' => 'Origin',
            'approved_at' => 'Approved at',
        ],

        'account_relationship_fields' => [
            'operator_account_id' => 'Operator account',
            'provider_account_id' => 'Provider account',
            'status' => 'Status',
            'created_via' => 'Origin',
            'source_invitation_id' => 'Source invitation',
            'approved_by_user_id' => 'Approved by',
            'approved_at' => 'Approved at',
            'suspended_at' => 'Suspended at',
            'terminated_at' => 'Terminated at',
        ],

        'account_relationship_filters' => [
            'status' => 'Status',
            'created_via' => 'Origin',
        ],

        'account_relationship_status' => [
            'approved' => 'Approved',
            'suspended' => 'Suspended',
            'terminated' => 'Terminated',
        ],

        'account_relationship_created_via' => [
            'invitation' => 'Invitation',
            'manual' => 'Manual',
            'system' => 'System',
        ],

        'role' => 'Role',
        'roles' => 'Roles',

        'role_fields' => [
            'account_id' => 'Account',
            'name' => 'Name',
            'permissions' => 'Permissions',
        ],

        'role_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'name' => 'Name',
            'permissions_count' => 'Permissions',
        ],

        'role_filters' => [
            'account_id' => 'Account',
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
            'is_unique_per_person' => 'Unique per person',
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

        'person' => 'Person',
        'persons' => 'Persons',

        'person_tabs' => [
            'general' => 'General',
            'users' => 'Linked users',
            'account_memberships' => 'Linked accounts',
            'contact_methods' => 'Contact methods',
            'contact_links' => 'Cross-account links',
        ],

        'person_fields' => [
            'name' => 'Name',
            'user_id' => 'User',
            'add_user_link' => 'Link user',
            'account_id' => 'Account',
            'contact_department_id' => 'Department',
            'contact_position_id' => 'Position',
            'is_primary' => 'Primary contact for account',
            'is_public_contact' => 'Public contact',
            'is_preferred_contact_mode' => 'Preferred contact mode',
            'add_account_membership' => 'Add linked account',
            'contact_type_id' => 'Channel type',
            'contact_method_value' => 'Value',
            'contact_method_is_primary' => 'Primary for this channel',
            'is_verified' => 'Verified',
            'add_contact_method' => 'Add contact method',
            'link_account_id' => 'Account (owns link)',
            'link_source_account_id' => 'Source account (contact origin)',
            'is_favorite' => 'Favorite',
            'visibility' => 'Visibility',
            'add_contact_link' => 'Add cross-account link',
        ],

        'person_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'users_count' => 'Users',
            'account_memberships_count' => 'Linked accounts',
            'contact_methods_count' => 'Methods',
            'contact_links_count' => 'Links',
        ],

        'person_visibility' => [
            'private' => 'Private',
            'shared' => 'Shared',
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
            'list_order' => 'Order',
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

        'currency_cat_catalog_label' => 'Currency #:id (ref #:ref)',

        'currency_rate' => 'Exchange rate',
        'currency_rates' => 'Exchange rates',

        'currency_rate_fields' => [
            'currency_id' => 'Currency',
            'units_per_usd' => 'Units per 1 USD',
            'units_per_usd_help' => 'How many units of this currency equal 1 US dollar. For USD this is always 1.',
            'starting_at' => 'Effective from',
            'starting_at_help' => 'This rate applies from this date (start of day) until a newer row exists for the same currency.',
        ],

        'currency_rate_columns' => [
            'id' => 'ID',
            'currency' => 'Currency',
            'units_per_usd' => 'Units / USD',
            'starting_at' => 'Effective from',
        ],

        'currency_rate_validation' => [
            'duplicate_starting_at' => 'A rate for this currency with the same effective date already exists.',
            'units_must_be_positive' => 'The value must be greater than zero.',
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
            'value' => 'Value',
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
            'usd_price' => 'USD price',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'usd_price' => 'USD price',
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

        'module' => 'Module',
        'modules' => 'Modules',

        'module_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'module_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'module_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'active' => 'Active',
        ],

        'nav_contacts' => 'Contacts',
        'nav_catalog_conditions' => 'Conditions',
        'nav_catalog_experiences' => 'Service experiences',
        'nav_catalog_features' => 'Features',
        'nav_accounts_price_lists' => 'Price lists',
        'nav_plans' => 'Plans and pricing',
        'nav_services' => 'Services',
        'nav_accounts_transfer' => 'Transfer',
        'nav_hotels' => 'Hotels',
        'nav_activities' => 'Activities',
        'nav_gastronomy' => 'Gastronomy',
        'nav_parameters' => 'Settings',
        'nav_users' => 'Users',
        'nav_authorization' => 'Authorization',
        'nav_onboarding' => 'Getting started',
        'nav_ai' => 'AI assistant',

        'ai_knowledge_item' => 'Knowledge article',
        'ai_knowledge_items' => 'Knowledge base (AI)',

        'ai_knowledge_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'ai_knowledge_fields' => [
            'key' => 'Stable key',
            'key_help' => 'Use letters, numbers, and underscores only (e.g. edit_service_image).',
            'title' => 'Title',
            'content_short' => 'Short summary',
            'content' => 'Body',
            'url' => 'Related URL',
            'tags' => 'Tags',
            'tags_help' => 'Comma-separated keywords for filtering.',
        ],

        'ai_knowledge_columns' => [
            'id' => 'ID',
            'key' => 'Key',
            'title_preview' => 'Title (first locale)',
            'translations_count' => 'Locales',
        ],

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

        'todo_category_actions' => [
            'copy_to_account' => 'Copy to account',
            'copy_to_account_heading' => 'Copy tasks to an account',
            'copy_to_account_description' => 'Creates a new copy of every task in this category (from all accounts), all assigned to the account you select. Rows in todo_tasks and todo_task_translations are duplicated.',
            'copy_destination_account' => 'Account',
            'copy_failed_title' => 'Could not copy tasks',
            'copy_invalid_account' => 'Select a valid account.',
            'copy_none_title' => 'No tasks to copy',
            'copy_none_body' => 'This category has no tasks yet.',
            'copy_all_skipped_title' => 'No new tasks created',
            'copy_all_skipped_body' => 'All :skipped task(s) were skipped because a row with the same code already exists on the selected account.',
            'copy_success_title' => 'Tasks copied',
            'copy_success_body' => ':created task(s) created. :skipped skipped (same code already on the account).',
        ],

        'todo_task' => 'To-do task (template)',
        'todo_tasks' => 'To-do tasks (templates)',

        'todo_task_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'todo_task_fields' => [
            'account_id' => 'Account',
            'code' => 'Code',
            'todo_category_id' => 'Category',
            'original_task_id' => 'Based on task (optional)',
            'action_type' => 'Action type',
            'action_url' => 'URL',
            'action_url_help' => 'Full URL (including https://).',
            'route_name' => 'Route',
            'route_name_help' => 'Named GET routes from the application (Filament, Livewire, and similar internal routes are hidden).',
            'verification_type' => 'Verification type',
            'verification_url' => 'Verification URL',
            'sort_order' => 'Sort order',
            'name' => 'Title',
            'description' => 'Description',
        ],

        'todo_task_action_types' => [
            'none' => 'None',
            'route' => 'Route',
            'url' => 'URL',
            'external' => 'External',
        ],

        'todo_task_verification_types' => [
            'none' => 'None',
            'api-check' => 'API check',
        ],

        'todo_task_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'sort_order' => 'Sort order',
            'code' => 'Code',
            'category' => 'Category',
            'name' => 'Title',
            'action_type' => 'Action',
            'verification_type' => 'Verification',
        ],

        'todo_task_filters' => [
            'account_id' => 'Account',
        ],

        'todo_category_filters' => [
            'account_id' => 'With tasks for account',
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

        'service_gastronomy_type' => 'Type',
        'service_gastronomy_types' => 'Types',

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

        'service_gastronomy_venue' => 'Venue',
        'service_gastronomy_venues' => 'Venues',

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

        'service_gastronomy_menu' => 'Menu',
        'service_gastronomy_menus' => 'Menus',

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

        'service_gastronomy_menu_category' => 'Menu category',
        'service_gastronomy_menu_categories' => 'Menu categories',

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

        'service_gastronomy_feature_category' => 'Feature category',
        'service_gastronomy_feature_categories' => 'Feature categories',

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

        'service_gastronomy_feature' => 'Feature',
        'service_gastronomy_features' => 'Features',

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
            'navigation_label' => 'Scopes by service type',
            'title' => 'Manage scopes by service type',
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
                'saved' => 'Scopes saved.',
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
            'description' => 'Description',
            'sort_order' => 'Sort order',
        ],

        'service_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'sort_order' => 'Sort order',
        ],

        'service_experience' => 'Service experience',
        'service_experiences' => 'Service experiences',

        'service_experience_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_experience_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_experience_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_experience_category' => 'Service experience category',
        'service_experience_categories' => 'Service experience categories',

        'service_experience_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_experience_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_experience_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_activity_type' => 'Activity type',
        'service_activity_types' => 'Activity types',

        'service_activity_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_activity_type_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_activity_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
        ],

        'service_activity_type_category' => 'Activity type category',
        'service_activity_type_categories' => 'Activity type categories',

        'service_activity_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'service_activity_type_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_activity_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_activity' => 'Activity service',
        'service_activities' => 'Activity services',

        'service_activity_tabs' => [
            'general' => 'General',
            'technical' => 'Technical',
        ],

        'service_activity_fields' => [
            'service_id' => 'Service',
            'service_activity_type_id' => 'Activity type (legacy)',
            'activity_types' => 'Activity types',
            'activity_types_help' => 'Select one or more catalogue types that describe this service.',
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

        'service_activity_columns' => [
            'id' => 'ID',
            'service' => 'Service',
            'type' => 'Type',
            'difficulty' => 'Difficulty',
        ],

        'service_activity_difficulty' => [
            'easy' => 'Easy',
            'moderate' => 'Moderate',
            'difficult' => 'Difficult',
        ],

        'service_detail_topic' => 'Condition topic',
        'service_detail_topics' => 'Condition topics',

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

        'service_detail_topic_category' => 'Condition category',
        'service_detail_topic_categories' => 'Condition categories',

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

        'service_detail' => 'Condition',
        'service_details' => 'Conditions',

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
            'add' => 'Add condition',
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
            'details' => 'Conditions',
            'experiences' => 'Experiences',
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
            'suspended' => 'Suspended',
            'discontinued' => 'Discontinued',
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
            'experiences' => 'Experiences',
            'experiences_help' => 'Select the experiences that apply to this service.',
        ],

        'service_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'service_type' => 'Service type',
            'name' => 'Name',
            'status' => 'Status',
        ],

        'service_delete_cascade' => [
            'modal_heading' => 'Delete service',
            'modal_intro' => 'This will permanently delete the service and all related records listed below.',
            'modal_confirm' => 'Delete service and related data',
            'grand_total' => 'Total rows to remove (including this service): :count',
            'labels' => [
                'translations' => 'Service translations',
                'experience_assignments' => 'Experience assignments',
                'details' => 'Service condition rows',
                'feature_links' => 'Feature links',
                'variants' => 'Variants',
                'variant_translations' => 'Variant translations',
                'variant_availability_rules' => 'Variant availability rules',
                'variant_availability_overrides' => 'Variant availability overrides',
                'price_list_items' => 'Price list items',
                'allocations' => 'Allocations',
                'service_offers' => 'Service offers',
                'operator_catalog_items' => 'Operator catalog items',
                'media_files' => 'Media library files',
                'hotel_type_assignments' => 'Hotel type assignments',
                'service_hotels' => 'Hotel profile rows',
                'service_activity' => 'Activity profile rows',
                'gastronomy_menu_assignments' => 'Menu format assignments',
                'gastronomy_venue_assignments' => 'Venue assignments',
                'gastronomy_experiences' => 'Experiences',
                'gastronomy_schedules' => 'Schedules',
                'gastronomy_capacities' => 'Capacities',
                'cuisine_gastronomy_assignments' => 'Cuisine assignments',
                'service_gastronomies' => 'Profile rows',
                'transfer_routes' => 'Transfer routes',
                'transfer_vehicles' => 'Transfer vehicles',
                'transfer_prices' => 'Transfer prices',
                'service_transfers' => 'Transfer profile rows',
            ],
        ],

        'service_status' => [
            'active' => 'Active',
            'onhold' => 'On hold',
            'suspended' => 'Suspended',
            'discontinued' => 'Discontinued',
            'inactive' => 'Inactive',
            'terminated' => 'Terminated',
        ],

        'price_list' => 'Price list',
        'price_lists' => 'Price lists',

        'price_list_owner_type' => [
            'account' => 'Account',
            'user' => 'User',
        ],

        'price_list_fields' => [
            'owner_type' => 'Owner type',
            'owner_id' => 'Owner',
            'name' => 'Name',
            'currency_id' => 'Currency',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'assignments' => 'Assignments',
        ],

        'price_list_tabs' => [
            'general' => 'General',
            'assignments' => 'Assignments',
        ],

        'price_list_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'owner' => 'Owner',
            'currency' => 'Currency',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'items_count' => 'Rows',
        ],

        'price_list_item' => 'Price list item',
        'price_list_items' => 'Price list items',

        'price_list_item_fields' => [
            'price_list_id' => 'Price list',
            'service_id' => 'Service (all variants)',
            'service_variant_id' => 'Service variant',
            'price' => 'Price',
            'pricing_mode' => 'Pricing mode',
        ],

        'price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Price list',
            'target' => 'Target',
            'service_all_variants' => 'All variants: :label',
            'service_variant' => 'Variant',
            'price' => 'Price',
            'pricing_mode' => 'Mode',
        ],

        'price_list_item_filters' => [
            'price_list_id' => 'Price list',
        ],

        'price_list_item_pricing_mode' => [
            'fixed' => 'Fixed',
            'percentage' => 'Percentage',
        ],

        'price_list_assignment_fields' => [
            'operator_id' => 'Operator (account)',
            'assigned_to_id' => 'Assigned to (account)',
            'adjustment_type' => 'Adjustment type',
            'adjustment_value' => 'Adjustment value',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'add' => 'Add assignment',
        ],

        'price_list_assignment_adjustment_type' => [
            'none' => 'No adjustment',
            'percentage' => 'Percentage',
            'fixed' => 'Fixed amount',
        ],

        'provider_price_list' => 'Provider price list',
        'provider_price_lists' => 'Provider price lists',

        'provider_price_list_tabs' => [
            'general' => 'General',
            'assignments' => 'Operator assignments',
        ],

        'provider_price_list_fields' => [
            'provider_id' => 'Provider (account)',
            'name' => 'Name',
            'currency_id' => 'Currency',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active list',
            'assignments' => 'Assignments',
        ],

        'provider_price_list_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'provider' => 'Provider',
            'currency' => 'Currency',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'items_count' => 'Rows',
        ],

        'provider_price_list_assignment_fields' => [
            'operator_id' => 'Operator (account)',
            'adjustment_type' => 'Adjustment type',
            'adjustment_value' => 'Adjustment value',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'add' => 'Add assignment',
        ],

        'provider_price_list_assignment_adjustment_type' => [
            'none' => 'No adjustment',
            'percentage' => 'Percentage',
            'fixed' => 'Fixed amount',
        ],

        'provider_price_list_item' => 'Provider list item',
        'provider_price_list_items' => 'Provider list items',

        'provider_price_list_item_fields' => [
            'price_list_id' => 'Provider price list',
            'service_id' => 'Service (all variants)',
            'service_variant_id' => 'Service variant',
            'price' => 'Price',
            'pricing_mode' => 'Pricing mode',
        ],

        'provider_price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'List',
            'target' => 'Target',
            'service_all_variants' => 'All variants: :label',
            'price' => 'Price',
            'pricing_mode' => 'Mode',
        ],

        'provider_price_list_item_filters' => [
            'price_list_id' => 'List',
        ],

        'provider_price_list_item_pricing_mode' => [
            'fixed' => 'Fixed',
            'percentage' => 'Percentage',
        ],

        'operator_price_list' => 'Operator price list',
        'operator_price_lists' => 'Operator price lists',

        'operator_price_list_tabs' => [
            'general' => 'General',
            'assignments' => 'Agency assignments',
        ],

        'operator_price_list_fields' => [
            'operator_id' => 'Operator (account)',
            'name' => 'Name',
            'currency_id' => 'Currency',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active list',
            'assignments' => 'Assignments',
        ],

        'operator_price_list_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'operator' => 'Operator',
            'currency' => 'Currency',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'items_count' => 'Rows',
        ],

        'operator_price_list_assignment_fields' => [
            'agency_id' => 'Agency (account)',
            'adjustment_type' => 'Adjustment type',
            'adjustment_value' => 'Adjustment value',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
            'is_active' => 'Active',
            'add' => 'Add assignment',
        ],

        'operator_price_list_assignment_adjustment_type' => [
            'none' => 'No adjustment',
            'percentage' => 'Percentage',
            'fixed' => 'Fixed amount',
        ],

        'operator_price_list_item' => 'Operator list item',
        'operator_price_list_items' => 'Operator list items',

        'operator_price_list_item_fields' => [
            'price_list_id' => 'Operator price list',
            'catalog_entry_id' => 'Catalog entry',
            'price' => 'Price',
            'pricing_mode' => 'Pricing mode',
        ],

        'operator_price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'List',
            'catalog_entry' => 'Catalog entry',
            'price' => 'Price',
            'pricing_mode' => 'Mode',
        ],

        'operator_price_list_item_filters' => [
            'price_list_id' => 'List',
        ],

        'operator_price_list_item_pricing_mode' => [
            'fixed' => 'Fixed',
            'percentage' => 'Percentage',
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

        'nav_transport' => 'Transfers',

        'service_transfer_location_type_category' => 'Transfer location type category',
        'service_transfer_location_type_categories' => 'Transfer location type categories',
        'service_transfer_location_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],
        'service_transfer_location_type_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],
        'service_transfer_location_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],

        'service_transfer_location_type' => 'Transfer location type',
        'service_transfer_location_types' => 'Transfer location types',
        'service_transfer_location_type_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],
        'service_transfer_location_type_fields' => [
            'code' => 'Code',
            'category' => 'Category',
            'sort_order' => 'Sort order',
        ],
        'service_transfer_location_type_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'category' => 'Category',
            'name' => 'Name',
        ],

        'service_transfer_location' => 'Transfer location',
        'service_transfer_locations' => 'Transfer locations',
        'service_transfer_location_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],
        'service_transfer_location_fields' => [
            'service_transfer_location_type_id' => 'Location type',
            'address' => 'Address',
            'city_id' => 'City',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'airport_code' => 'Airport code',
            'is_active' => 'Active',
        ],
        'service_transfer_location_columns' => [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'airport_code' => 'Airport',
            'city' => 'City',
        ],

        'service_transfer_vehicle_type_category' => 'Transfer vehicle type category',
        'service_transfer_vehicle_type_categories' => 'Transfer vehicle type categories',
        'service_transfer_vehicle_type_category_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],
        'service_transfer_vehicle_type_category_fields' => [
            'code' => 'Code',
            'name' => 'Name',
        ],
        'service_transfer_vehicle_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ],
        'service_transfer_vehicle_type_category_relation' => [
            'vehicle_types_tab' => 'Vehicle types',
        ],

        'service_transfer_vehicle_type' => 'Transfer vehicle type',
        'service_transfer_vehicle_types' => 'Transfer vehicle types',
        'service_transfer_vehicle_type_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_vehicle_type_fields' => [
            'account_id' => 'Account',
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
            'max_passengers' => 'Max passengers',
            'max_luggage' => 'Max luggage',
        ],
        'service_transfer_vehicle_type_columns' => [
            'id' => 'ID',
            'account' => 'Account',
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
            'max_passengers' => 'Max pax',
            'max_luggage' => 'Max luggage',
        ],

        'service_transfer' => 'Service transfer profile',
        'service_transfers' => 'Service transfer profiles',
        'service_transfer_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_fields' => [
            'service_id' => 'Service',
            'transfer_type' => 'Transfer type',
            'modality' => 'Modality',
            'allows_multiple_stops' => 'Allows multiple stops',
            'max_passengers' => 'Max passengers',
            'max_luggage' => 'Max luggage',
            'default_duration_minutes' => 'Default duration (minutes)',
            'requires_flight_info' => 'Requires flight info',
            'requires_pickup_time' => 'Requires pickup time',
            'requires_dropoff_time' => 'Requires drop-off time',
        ],
        'service_transfer_columns' => [
            'id' => 'ID',
            'service' => 'Service',
            'transfer_type' => 'Type',
            'modality' => 'Modality',
        ],
        'service_transfer_transfer_type' => [
            'one_way' => 'One way',
            'round_trip' => 'Round trip',
        ],
        'service_transfer_modality' => [
            'private' => 'Private',
            'shared' => 'Shared',
        ],

        'service_transfer_route' => 'Transfer route',
        'service_transfer_routes' => 'Transfer routes',
        'service_transfer_route_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_route_fields' => [
            'service_transfer_id' => 'Transfer profile',
            'origin_location_id' => 'Origin',
            'destination_location_id' => 'Destination',
            'is_active' => 'Active',
            'distance_km' => 'Distance (km)',
            'duration_minutes' => 'Duration (minutes)',
        ],
        'service_transfer_route_columns' => [
            'id' => 'ID',
            'transfer' => 'Profile',
            'origin' => 'Origin',
            'destination' => 'Destination',
        ],
        'service_transfer_route_validation' => [
            'different_endpoints' => 'Origin and destination must be different.',
        ],

        'service_transfer_vehicle' => 'Transfer vehicle assignment',
        'service_transfer_vehicles' => 'Transfer vehicle assignments',
        'service_transfer_vehicle_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_vehicle_fields' => [
            'service_transfer_id' => 'Transfer profile',
            'service_transfer_vehicle_type_id' => 'Vehicle type',
            'is_default' => 'Default for this profile',
        ],
        'service_transfer_vehicle_columns' => [
            'id' => 'ID',
            'transfer' => 'Profile',
            'vehicle_type' => 'Vehicle type',
        ],

        'service_transfer_price' => 'Transfer price',
        'service_transfer_prices' => 'Transfer prices',
        'service_transfer_price_tabs' => [
            'general' => 'General',
        ],
        'service_transfer_price_fields' => [
            'service_transfer_id' => 'Transfer profile',
            'route_id' => 'Route (optional)',
            'service_transfer_vehicle_type_id' => 'Vehicle type (optional)',
            'pricing_type' => 'Pricing type',
            'currency_id' => 'Currency',
            'base_price' => 'Base price',
            'price_per_person' => 'Price per person',
            'price_per_extra_passenger' => 'Price per extra passenger',
            'min_passengers' => 'Min passengers',
            'max_passengers' => 'Max passengers',
            'valid_from' => 'Valid from',
            'valid_to' => 'Valid to',
        ],
        'service_transfer_price_columns' => [
            'id' => 'ID',
            'transfer' => 'Profile',
            'route' => 'Route',
            'vehicle_type' => 'Vehicle type',
            'pricing_type' => 'Pricing',
            'currency' => 'Currency',
            'base_price' => 'Base',
        ],
        'service_transfer_price_pricing_type' => [
            'per_vehicle' => 'Per vehicle',
            'per_person' => 'Per person',
        ],
        'service_transfer_price_validation' => [
            'route_belongs_to_transfer' => 'The route must belong to the selected transfer profile.',
        ],

    ],

];

