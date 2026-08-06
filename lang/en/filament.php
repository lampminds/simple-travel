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
        'system_tables' => 'System tables',
        'transport' => 'Transport',
    ],

    'panel' => [
        'cluster_subnav_hide' => 'Hide module menu',
        'cluster_subnav_show' => 'Show module menu',
        'back_to_site' => 'Back to site',
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
            'impersonate' => 'Impersonate',
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

        'cat_document' => 'Document type',
        'cat_documents' => 'Document types',

        'cat_document_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'cat_document_fields' => [
            'group' => 'Group',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'sort_order' => 'Sort order',
            'language' => 'Language',
        ],

        'cat_document_columns' => [
            'id' => 'ID',
            'group' => 'Group',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'sort_order' => 'Sort order',
        ],

        'cat_gender' => 'Gender',
        'cat_genders' => 'Genders',

        'cat_gender_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'cat_gender_fields' => [
            'code' => 'Code',
            'code_help' => 'Stable identifier in English (letters, numbers, dashes and underscores). Must be unique.',
            'name' => 'Name',
        ],

        'cat_gender_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'translations_count' => 'Locales',
        ],

        'cat_gender_filter' => [
            'active_status' => 'Status',
            'active_only' => 'Active only',
            'inactive_only' => 'Inactive only',
            'active_all' => 'All',
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
            'name' => 'Contact name',
            'company_name' => 'Company',
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
            'invited_account_id' => 'Target company (existing user)',
            'email' => 'Email',
            'name' => 'Contact name',
            'company_name' => 'Company name',
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

        'account_document' => 'Account document',
        'account_documents' => 'Account documents',

        'account_document_fields' => [
            'account_id' => 'Account',
            'document_id' => 'Document type',
            'value' => 'Value',
            'add' => 'Add Tax ID',
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
            'document_name' => 'Name as on documents',
            'given_name' => 'Given name(s)',
            'family_name' => 'Family name(s)',
            'date_of_birth' => 'Date of birth',
            'gender_id' => 'Gender',
            'nationality_id' => 'Nationality',
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
            'gender' => 'Gender',
            'date_of_birth' => 'Date of birth',
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

        'lmp_country' => 'Country',
        'lmp_countries' => 'Countries',

        'lmp_country_fields' => [
            'name' => 'Name',
            'iso_2' => 'ISO 2',
            'iso_3' => 'ISO 3',
            'phonecode' => 'Phone code',
            'capital' => 'Capital',
            'currency_id' => 'Currency',
            'tld' => 'Top-level domain',
            'emoji' => 'Emoji',
        ],

        'lmp_country_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'iso_2' => 'ISO 2',
            'iso_3' => 'ISO 3',
            'capital' => 'Capital',
            'currency' => 'Currency',
        ],

        'lmp_state' => 'State / province',
        'lmp_states' => 'States / provinces',

        'lmp_state_fields' => [
            'name' => 'Name',
            'country_id' => 'Country',
            'level' => 'Level',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'timezone_id' => 'Timezone ID',
            'parent_id' => 'Parent state',
        ],

        'lmp_state_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'country' => 'Country',
            'parent' => 'Parent state',
            'level' => 'Level',
        ],

        'lmp_state_filters' => [
            'country_id' => 'Country',
        ],

        'lmp_city' => 'City',
        'lmp_cities' => 'Cities',

        'lmp_city_fields' => [
            'name' => 'Name',
            'state_id' => 'State / province',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'timezone_id' => 'Timezone ID',
        ],

        'lmp_city_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'state' => 'State / province',
            'country' => 'Country',
            'system_transfer_locations' => 'System transfer POIs',
        ],

        'lmp_city_filters' => [
            'country_id' => 'Country',
            'state_id' => 'State / province',
        ],

        'lmp_city_actions' => [
            'generate_transfer_locations' => 'Generate transfer locations',
            'generate_transfer_locations_heading' => 'Generate system transfer catalog',
            'generate_transfer_locations_description' => 'Uses OpenAI to suggest pickup/drop-off points for this city (account_id null). With translation enabled: one API call for the list and one or two batch calls to translate all names (no per-location MyMemory/Google requests).',
            'replace_existing' => 'Replace existing system catalog for this city',
            'replace_existing_help' => 'Deletes all system transfer locations linked to this city before inserting new ones.',
            'translate_to_other_languages' => 'Translate to other active languages',
            'translate_to_other_languages_help' => 'Second OpenAI request translates every name into the other active languages in one or two batch calls.',
            'source_language' => 'Source language for AI labels',
            'max_suggestions' => 'Maximum suggestions',
            'additional_context' => 'Additional context (optional)',
            'generate_failed_title' => 'Could not generate locations',
            'generate_none_title' => 'No locations created',
            'generate_none_body' => 'The AI returned no new rows (skipped duplicates: :skipped).',
            'generate_success_title' => 'Transfer catalog updated',
            'generate_success_body' => 'Created :created location(s). AI suggested :ai. Skipped :skipped duplicate(s). Removed :removed previous row(s). OpenAI calls: :openai_calls.',
            'generate_translation_fallbacks' => ':count name(s) kept the source language because translation failed.',
            'openai_rate_limit' => 'OpenAI rate limit reached (request limit exceeded). Wait a few minutes and try again, or check usage at platform.openai.com. Embeddings and this action share the same API key.',
            'openai_quota' => 'OpenAI quota or billing limit reached. Add credits or upgrade the plan at platform.openai.com — nothing was saved.',
            'openai_invalid_key' => 'OpenAI rejected the API key. Check OPENAI_API_KEY in .env.',
            'openai_model' => 'Chat model ":model" is not available for this key. Set OPENAI_CHAT_MODEL in .env (e.g. gpt-4o-mini).',
            'openai_generic' => 'OpenAI request failed: :detail',
        ],

        'currency_cat_catalog_label' => 'Currency #:id (ref #:ref)',

        'currency_rate' => 'Exchange rate',
        'currency_rates' => 'Exchange rates',

        'currency_rate_fields' => [
            'account_id' => 'Account',
            'account_id_help' => 'Leave empty for official system rates. Set an account for a tenant-specific override.',
            'currency_id' => 'Currency',
            'source' => 'Source',
            'source_help' => 'Reserved for future use (different rate providers).',
            'units_per_usd_buy' => 'Buy (units per 1 USD)',
            'units_per_usd_sell' => 'Sell (units per 1 USD)',
            'units_per_usd_help' => 'How many units of this currency equal 1 US dollar. For USD both are always 1.',
            'starting_at' => 'Effective from',
            'starting_at_help' => 'This rate applies from this date (start of day) until a newer row exists for the same scope.',
            'is_active' => 'Active',
        ],

        'currency_rate_columns' => [
            'id' => 'ID',
            'account' => 'Scope',
            'currency' => 'Currency',
            'units_per_usd_buy' => 'Buy / USD',
            'units_per_usd_sell' => 'Sell / USD',
            'starting_at' => 'Effective from',
            'is_active' => 'Active',
        ],

        'currency_rate_scope' => [
            'system' => 'System',
        ],

        'currency_rate_filters' => [
            'all_active_states' => 'All',
            'active_only' => 'Active only',
            'inactive_only' => 'Inactive only',
            'scope' => 'Scope',
            'all_scopes' => 'All scopes',
            'system_only' => 'System only',
            'tenant_only' => 'Tenant overrides only',
        ],

        'currency_rate_validation' => [
            'duplicate_starting_at' => 'A rate for this currency, scope and effective date already exists.',
            'units_must_be_positive' => 'The value must be greater than zero.',
        ],

        'menu' => 'Menu item',
        'menus' => 'Website menus',

        'menu_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
            'account_types' => 'Visibility by type',
        ],

        'menu_fields' => [
            'slug' => 'Slug',
            'slug_help' => 'Internal key (unique). Used in code, not necessarily shown on the public site.',
            'parent_id' => 'Parent',
            'icon' => 'Icon',
            'route' => 'Route name',
            'translation_name' => 'Label',
            'translation_tip' => 'Tooltip',
            'excluded_account_types' => 'Hidden for account types',
            'excluded_account_types_help' => 'Leave empty to show this item for every account type. Check types that must not see this menu.',
        ],

        'menu_columns' => [
            'id' => 'ID',
            'label' => 'Label',
            'route' => 'Route',
            'parent' => 'Parent',
            'parent_none' => '— Top level —',
            'excluded_account_types' => 'Hidden for types',
            'excluded_account_types_none' => 'All types',
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
            'account_types' => 'Account types',
            'modules' => 'Modules',
            'items' => 'Plan items',
        ],

        'plan_fields' => [
            'code' => 'Code',
            'active' => 'Active',
            'usd_price' => 'USD price',
            'name' => 'Name',
            'description' => 'Description',
            'account_types' => 'Applicable account types',
            'account_types_help' => 'Leave empty to allow every account type. Select types to restrict this plan.',
        ],

        'plan_relation' => [
            'modules_tab' => 'Modules in plan',
            'module' => 'Module',
            'module_code' => 'Code',
            'module_name' => 'Name',
            'add_module' => 'Add module',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'account_types' => 'Types',
            'account_types_all' => 'All account types',
            'modules_count' => 'Modules',
            'usd_price' => 'USD price',
            'active' => 'Active',
        ],

        'plan_filter' => [
            'account_type' => 'Account type',
            'account_type_placeholder' => 'All account types',
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
            'account_types' => 'Account types',
            'features' => 'Features',
            'pricing' => 'Pricing',
        ],

        'module_fields' => [
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'account_types' => 'Applicable account types',
            'account_types_help' => 'Leave empty to allow every account type. Select types to restrict this module.',
        ],

        'module_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'active' => 'Active',
            'account_types' => 'Account types',
            'account_types_all' => 'All account types',
        ],

        'module_filter' => [
            'account_type' => 'Account type',
            'account_type_placeholder' => 'All account types',
        ],

        'module_actions' => [
            'copy' => 'Copy',
            'copy_heading' => 'Copy module',
            'copy_description' => 'Creates a new module with translations, account types, features, and pricing copied from this record. Plan assignments are not copied.',
            'copy_failed_title' => 'Could not copy module',
            'copy_code_required' => 'Enter a code for the new module.',
            'copy_code_exists' => 'A module with this code already exists.',
            'copy_success_title' => 'Module copied',
            'copy_success_body' => 'Module :code was created. You can review and adjust it now.',
        ],

        'module_relation' => [
            'features_tab' => 'Features',
            'prices_tab' => 'Pricing',
        ],

        'module_feature_fields' => [
            'text' => 'Feature text',
            'language' => 'Language',
            'add' => 'Add feature',
            'add_translation' => 'Add translation',
        ],

        'module_feature_columns' => [
            'text' => 'Text',
        ],

        'module_price_fields' => [
            'add' => 'Add price',
            'billing_type' => 'Billing model',
            'billing_fixed' => 'Fixed',
            'billing_per_user' => 'Per user',
            'billing_hybrid' => 'Hybrid',
            'billing_usage' => 'Usage',
            'base_price' => 'Base price',
            'base_price_per_user_help' => 'Fixed monthly fee added on top of the per-user component.',
            'base_price_fixed_help' => 'Fixed monthly module amount.',
            'included_users' => 'Included users',
            'included_users_help' => 'Hybrid only: users covered by the base price. Additional users pay the per-user rate.',
            'price_per_user' => 'Price per user',
            'price_per_user_per_user_help' => 'Monthly total = base price + (price per user × number of users).',
            'price_per_user_hybrid_help' => 'Charged for each user above the included users.',
            'tiers_section' => 'User tiers',
            'add_tier' => 'Add tier',
        ],

        'module_price_columns' => [
            'billing_type' => 'Billing',
            'base_price' => 'Base',
            'price_per_user' => 'Per user',
            'tiers' => 'Tiers',
        ],

        'module_price_tier_fields' => [
            'from_users' => 'From users',
            'to_users' => 'To users',
            'price_per_user' => 'Price per user',
        ],

        'nav_contacts' => 'Contacts',
        'nav_catalog_conditions' => 'Conditions',
        'nav_catalog_experiences' => 'Service experiences',
        'nav_catalog_features' => 'Features',
        'nav_accounts_price_lists' => 'Price lists',
        'nav_plans' => 'Plans and pricing',
        'nav_services' => 'Services',
        'nav_accounts_transfer' => 'Transfer',
        'nav_hotels' => 'Accommodation',
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

        'ai_usage_log' => 'AI usage entry',
        'ai_usage_logs' => 'AI usage log',

        'ai_usage_log_columns' => [
            'created_at' => 'Date',
            'usage_type' => 'Type',
            'user' => 'User',
            'account' => 'Account',
            'total_tokens' => 'Tokens',
            'estimated_usd' => 'Estimated cost (USD)',
        ],

        'ai_usage_log_types' => [
            'assistant' => 'Assistant',
            'translation' => 'Translation (free API)',
            'openai_translation' => 'OpenAI translation',
        ],

        'ai_usage_log_filters' => [
            'user' => 'User',
            'account' => 'Account',
            'date_range' => 'Date range',
            'created_from' => 'From',
            'created_until' => 'Until',
        ],

        'cat_helper' => 'Context help',
        'cat_helpers' => 'Context help snippets',
        'cat_helper_duplicate' => 'Duplicate',

        'cat_helper_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'cat_helper_fields' => [
            'screen_code' => 'Screen',
            'screen_code_help' => 'Logical screen or flow this help belongs to (letters, numbers, dashes and underscores).',
            'code' => 'Helper key',
            'code_help' => 'Stable identifier for Blade / front-end (letters, numbers, dashes and underscores).',
            'account_type' => 'Account type (optional)',
            'service_type' => 'Service type (optional)',
            'notes' => 'Internal notes',
            'text' => 'HTML content',
            'text_help' => 'Use the toolbar to format text and attach images; files are stored under public storage (no separate media collection).',
            'text_nesting_depth_exceeded' => 'The help text is too deeply structured for the editor to save in one step. Simplify formatting (fewer nested lists or styles), save again, or contact an administrator if this persists.',
        ],

        'cat_helper_columns' => [
            'id' => 'ID',
            'screen_and_code' => 'Screen & key',
            'screen_code' => 'Screen',
            'code' => 'Helper key',
            'account_type' => 'Account type',
            'service_type' => 'Service type',
            'text_preview' => 'Help text',
            'translations_count' => 'Locales',
        ],

        'cat_faq' => 'FAQ',
        'cat_faqs' => 'FAQs',

        'cat_faq_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'cat_faq_fields' => [
            'code' => 'Code',
            'code_help' => 'Stable identifier in English (letters, numbers, dashes and underscores).',
            'account_type' => 'Account type (optional)',
            'sort_order' => 'Sort order',
            'notes' => 'Internal notes',
            'question' => 'Question',
            'answer' => 'Answer',
        ],

        'cat_faq_columns' => [
            'id' => 'ID',
            'sort_order' => 'Sort order',
            'code' => 'Code',
            'account_type' => 'Account type',
            'question_preview' => 'Question',
            'translations_count' => 'Locales',
        ],

        'cat_faq_filter' => [
            'active_status' => 'Status',
            'active_only' => 'Active only',
            'inactive_only' => 'Inactive only',
            'active_all' => 'All',
        ],

        'cat_booking_status' => 'Booking status',
        'cat_booking_statuses' => 'Booking statuses',

        'cat_booking_status_tabs' => [
            'general' => 'General',
            'translations' => 'Translations',
        ],

        'cat_booking_status_fields' => [
            'type' => 'Scope',
            'code' => 'Code',
            'code_help' => 'Stable identifier in English (letters, numbers, dashes and underscores). Unique per scope.',
            'sort_order' => 'Sort order',
            'name' => 'Name',
            'help_tip' => 'Help tip',
            'description' => 'Description',
        ],

        'cat_booking_status_columns' => [
            'id' => 'ID',
            'sort_order' => 'Sort order',
            'type' => 'Scope',
            'code' => 'Code',
            'name' => 'Name',
            'translations_count' => 'Locales',
        ],

        'cat_booking_status_type' => [
            'main' => 'Booking header',
            'item' => 'Booking item',
        ],

        'cat_booking_status_filter' => [
            'active_status' => 'Status',
            'active_only' => 'Active only',
            'inactive_only' => 'Inactive only',
            'active_all' => 'All',
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

        'service_hotel_type' => 'Accommodation type',
        'service_hotel_types' => 'Accommodation types',

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

        'service_hotel_type_category' => 'Accommodation type category',
        'service_hotel_type_categories' => 'Accommodation type categories',

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

        'service_gastronomy_type_assignment' => 'Gastronomy type assignment',
        'service_gastronomy_type_assignments' => 'Gastronomy type assignments',

        'service_gastronomy_type_assignment_tabs' => [
            'general' => 'General',
        ],

        'service_gastronomy_type_assignment_fields' => [
            'service_gastronomy_id' => 'Gastronomy profile',
            'service_gastronomy_type_id' => 'Gastronomy type',
        ],

        'service_gastronomy_type_assignment_columns' => [
            'id' => 'ID',
            'service' => 'Service',
            'type' => 'Type',
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
            'scope' => 'Scope',
            'condition_key' => 'Default condition key',
            'active' => 'Active',
            'name' => 'Name',
            'description' => 'Description',
        ],

        'service_detail_topic_scopes' => [
            'informational' => 'Informational',
            'service' => 'Service',
            'commercial' => 'Commercial',
            'legal' => 'Legal',
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
            'scope' => 'Scope',
            'condition_key' => 'Condition key',
            'active' => 'Active',
        ],

        'service_detail_condition_key' => 'Condition key',
        'service_detail_condition_keys' => 'Condition keys',

        'service_detail_condition_key_categories' => [
            'payment' => 'Payment',
            'operation' => 'Operation',
            'transport' => 'Transport',
            'accommodation' => 'Accommodation',
            'safety' => 'Safety',
            'legal' => 'Legal',
            'inclusions' => 'Inclusions',
            'traveler' => 'Traveler',
            'service' => 'Service',
        ],

        'service_detail_condition_key_fields' => [
            'category' => 'Category',
            'code' => 'Code',
            'code_help' => 'Short identifier in English (e.g. cancellation_policy).',
            'description' => 'Description',
        ],

        'service_detail_condition_key_columns' => [
            'id' => 'ID',
            'code' => 'Code',
            'category' => 'Category',
            'description' => 'Description',
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
            'translations' => 'Service description',
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
            'per_group' => 'Per group',
        ],

        'service_variant_inventory_type' => [
            'unlimited' => 'Unlimited',
            'per_day' => 'Per day',
            'per_timeslot' => 'Per time slot',
            'per_departure' => 'Per departure',
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
                'operator_package_items' => 'Operator package items',
                'media_files' => 'Media library files',
                'hotel_type_assignments' => 'Accommodation type assignments',
                'service_hotels' => 'Accommodation profile rows',
                'service_activity' => 'Activity profile rows',
                'gastronomy_menu_assignments' => 'Menu format assignments',
                'gastronomy_venue_assignments' => 'Venue assignments',
                'gastronomy_experiences' => 'Experiences',
                'gastronomy_schedules' => 'Schedules',
                'gastronomy_capacities' => 'Capacities',
                'cuisine_gastronomy_assignments' => 'Cuisine assignments',
                'gastronomy_type_assignments' => 'Gastronomy type assignments',
                'service_gastronomies' => 'Profile rows',
                'transfer_routes' => 'Transfer routes',
                'transfer_schedules' => 'Transfer schedules',
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
            'variant_base' => 'Variant base price',
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
            'fixed_delta' => 'Fixed +/-',
            'percentage' => 'Percentage',
            'fixed_price' => 'Fixed price',
            'direct' => 'Fixed price',
            'fixed' => 'Fixed price',
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
            'service_variant_id' => 'Service variant',
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

