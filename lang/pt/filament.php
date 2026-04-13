<?php

/**
 * Portuguese translations for Filament admin (project-specific).
 * Used when locale is pt (e.g. via the panel language switcher).
 */

return [

    'common' => [
        'active' => 'Ativo',
        'navigation_badge_tooltip' => 'Total de registros',
        'select_option' => 'Selecione uma opção',
    ],

    'pages' => [
        'list_records_count' => 'Total: :count :label',
        'website_menu_editor' => [
            'nav_label' => 'Menu do site',
            'title' => 'Editor do menu do site',
            'header_action' => 'Editor visual',
            'section_heading' => 'Árvore',
            'hint' => 'Use as setas para reordenar itens entre irmãos. Abra um item para editar textos, rotas, visibilidade e quais tipos de conta o veem.',
            'move_up' => 'Subir',
            'move_down' => 'Descer',
            'active' => 'Ativo',
            'inactive' => 'Inativo',
        ],
    ],

    'resources' => [

        'account' => 'Conta',
        'accounts' => 'Contas',

        'account_tabs' => [
            'main' => 'Dados principais',
            'tax_ids' => 'Identificações fiscais',
            'categories' => 'Categorias',
        ],

        'account_type_category_fields' => [
            'label' => 'Tipos de conta',
            'help' => 'Um ou mais tipos de negócio (fornecedor, agência, etc.). Só aparecem categorias com grupo «type».',
        ],

        'account_fields' => [
            'nick' => 'Alias',
            'code' => 'Código',
            'name' => 'Nome',
            'commercial_name' => 'Razão social',
            'email' => 'E-mail',
            'phone' => 'Telefone',
            'address_line1' => 'Morada (linha 1)',
            'address_line2' => 'Morada (linha 2)',
            'postal_code' => 'Código postal',
            'code_help' => 'Gerado automaticamente na criação.',
        ],

        'account_columns' => [
            'id' => 'ID',
            'nick' => 'Alias',
            'code' => 'Código',
            'name' => 'Nome',
            'commercial_name' => 'Razão social',
            'email' => 'E-mail',
            'account_category' => 'Categoria de conta',
        ],

        'account_category' => 'Categoria de conta',
        'account_categories' => 'Categorias de conta',

        'account_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'account_category_fields' => [
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
            'language' => 'Idioma',
        ],

        'account_category_columns' => [
            'id' => 'ID',
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
        ],

        'user' => 'Utilizador',
        'users' => 'Utilizadores',

        'user_fields' => [
            'accounts' => 'Contas',
            'name' => 'Nome',
            'email' => 'E-mail',
            'password' => 'Palavra-passe',
            'roles' => 'Funções',
        ],

        'user_columns' => [
            'id' => 'ID',
            'accounts' => 'Contas',
            'name' => 'Nome',
            'email' => 'E-mail',
            'roles' => 'Funções',
        ],

        'role' => 'Função',
        'roles' => 'Funções',

        'role_fields' => [
            'name' => 'Nome',
            'permissions' => 'Permissões',
        ],

        'role_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'permissions_count' => 'Permissões',
        ],

        'permission' => 'Permissão',
        'permissions' => 'Permissões',

        'permission_fields' => [
            'name' => 'Nome da permissão',
            'name_help' => 'Identificador estável (ex.: manage_services). O guard é web.',
        ],

        'permission_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'roles_count' => 'Funções',
        ],

        'parameter' => 'Parâmetro',
        'parameters' => 'Parâmetros',

        'parameter_fields' => [
            'category' => 'Categoria',
            'code' => 'Código',
            'type' => 'Tipo',
            'value' => 'Valor',
            'mode' => 'Modo',
            'help' => 'Ajuda',
            'comments' => 'Comentários',
        ],

        'parameter_columns' => [
            'id' => 'ID',
            'category' => 'Categoria',
            'code' => 'Código',
            'type' => 'Tipo',
            'value' => 'Valor',
            'mode' => 'Modo',
        ],

        'account_tax_id' => 'Identificação fiscal',
        'account_tax_ids' => 'Identificações fiscais',

        'account_tax_id_fields' => [
            'account_id' => 'Conta',
            'account_category_id' => 'Tipo / Categoria',
            'value' => 'Valor',
            'add' => 'Adicionar identificação fiscal',
        ],

        'account_tax_id_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'category' => 'Categoria',
            'value' => 'Valor',
        ],

        'contact_department' => 'Departamento de contato',
        'contact_departments' => 'Departamentos de contato',

        'contact_department_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'sort_order' => 'Ordem',
        ],

        'contact_department_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'sort_order' => 'Ordem',
        ],

        'contact_position' => 'Cargo de contato',
        'contact_positions' => 'Cargos de contato',

        'contact_position_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'contact_position_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'contact_position_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'contact_type' => 'Tipo de contato',
        'contact_types' => 'Tipos de contato',

        'contact_type_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'mask' => 'Máscara',
            'mask_help' => 'Máscara para formatar o valor (ex.: telefone, documento).',
            'validation' => 'Validação',
            'validation_help' => 'Regra ou padrão de validação do valor.',
        ],

        'contact_type_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'code' => 'Código',
            'mask' => 'Máscara',
            'validation' => 'Validação',
        ],

        'contact' => 'Contato',
        'contacts' => 'Contatos',

        'contact_fields' => [
            'account_id' => 'Conta',
            'name' => 'Nome',
            'contact_department_id' => 'Departamento',
            'contact_position_id' => 'Cargo',
        ],

        'contact_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'name' => 'Nome',
            'department' => 'Departamento',
            'position' => 'Cargo',
        ],

        'provider' => 'Fornecedor',
        'providers' => 'Fornecedores',

        'provider_tabs' => [
            'main' => 'Dados principais',
            'categories' => 'Categorias',
        ],

        'provider_fields' => [
            'name' => 'Nome',
            'commercial_name' => 'Razão social',
            'email' => 'E-mail',
            'phone' => 'Telefone',
            'address_line1' => 'Morada (linha 1)',
            'address_line2' => 'Morada (linha 2)',
            'city_id' => 'Cidade',
            'postal_code' => 'Código postal',
            'status' => 'Estado',
            'inviting_id' => 'Conta convidante',
            'categories' => 'Categorias',
        ],

        'provider_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'commercial_name' => 'Razão social',
            'email' => 'E-mail',
            'status' => 'Estado',
            'inviting' => 'Conta convidante',
        ],

        'provider_status' => [
            'active' => 'Ativo',
            'onhold' => 'Em espera',
            'inactive' => 'Inativo',
            'terminated' => 'Rescindido',
        ],

        'provider_category' => 'Categoria de fornecedor',
        'provider_categories' => 'Categorias de fornecedor',

        'provider_category_fields' => [
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
        ],

        'provider_category_columns' => [
            'id' => 'ID',
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
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

        'currency' => 'Moeda',
        'currencies' => 'Moedas',

        'currency_fields' => [
            'currency' => 'Moeda',
        ],

        'currency_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'symbol' => 'Símbolo',
            'name' => 'Nome',
        ],

        'menu' => 'Item de menu',
        'menus' => 'Menus do site',

        'menu_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'account_types' => 'Tipos de conta',
        ],

        'menu_fields' => [
            'slug' => 'Slug',
            'slug_help' => 'Chave interna (única). Usada no código; pode não aparecer no site público.',
            'parent_id' => 'Pai',
            'icon' => 'Ícone',
            'route' => 'Nome da rota',
            'translation_name' => 'Rótulo',
            'translation_tip' => 'Tooltip',
            'account_types' => 'Visível para tipos de conta',
            'account_types_help' => 'Se nenhum estiver selecionado, o item fica oculto para todos os tipos de conta.',
        ],

        'menu_columns' => [
            'id' => 'ID',
            'label' => 'Rótulo',
            'route' => 'Rota',
            'parent' => 'Pai',
            'account_types' => 'Tipos de conta',
            'account_types_none' => 'Nenhum',
        ],

        'menu_filter' => [
            'scope' => 'Âmbito',
            'all_levels' => 'Todos os níveis',
            'root_only' => 'Apenas raiz',
            'children_of' => 'Filhos de: :label',
            'account_type' => 'Tipo de conta',
            'account_type_placeholder' => 'Todos os tipos',
            'active_status' => 'Ativo',
            'active_all' => 'Todos',
            'active_only' => 'Só ativos',
            'inactive_only' => 'Só inativos',
        ],

        'menu_validation' => [
            'parent_cycle' => 'Esse pai criaria um ciclo na hierarquia.',
        ],

        'parameter_definition' => 'Definição de parâmetro',
        'parameter_definitions' => 'Definições de parâmetros',

        'parameter_definition_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'options' => 'Opções',
            'values' => 'Valores',
        ],

        'parameter_definition_fields' => [
            'category' => 'Categoria',
            'subcategory' => 'Subcategoria',
            'code' => 'Código',
            'type' => 'Tipo',
            'scope' => 'Escopo',
            'has_default' => 'Tem valor padrão',
            'ui_component' => 'Componente UI',
            'ui_options' => 'Opções UI',
            'sort_order' => 'Ordem',
            'default_value' => 'Valor padrão',
            'validation_rules' => 'Regras de validação',
            'translation_name' => 'Nome',
            'translation_description' => 'Descrição',
            'translation_help' => 'Ajuda',
            'comments' => 'Comentários',
        ],

        'parameter_definition_columns' => [
            'id' => 'ID',
            'category' => 'Categoria',
            'subcategory' => 'Subcategoria',
            'code' => 'Código',
            'name' => 'Nome',
            'type' => 'Tipo',
            'scope' => 'Escopo',
            'has_default' => 'Padrão',
            'ui_component' => 'UI',
        ],

        'parameter_option_fields' => [
            'value' => 'Valor armazenado',
            'sort_order' => 'Ordem',
            'label' => 'Rótulo',
            'labels' => 'Rótulos por idioma',
            'add' => 'Adicionar opção',
        ],

        'parameter_definition_options_help' => 'Para select, radio, checkbox e switch são necessárias pelo menos duas opções (por exemplo dois valores explícitos para sim/não). Outros componentes podem deixar a lista vazia e usar texto livre.',
        'parameter_definition_options_min_two' => 'Este componente UI exige pelo menos duas opções com valor armazenado.',
        'parameter_definition_values_tab_help' => 'Escopo sistema: no máximo uma linha (conta ignorada). Por inquilino: conta opcional — deixe vazio para um valor predefinido para todas as contas, ou indique uma conta por linha de substituição.',
        'parameter_definition_values_duplicate_account' => 'Conta duplicada na lista de valores.',

        'parameter_definition_ui_components' => [
            'input' => 'Campo de texto',
            'select' => 'Seleção',
            'checkbox' => 'Caixa de seleção',
            'radio' => 'Opção única',
            'switch' => 'Interruptor',
            'textarea' => 'Área de texto',
            'editor' => 'Editor rico',
            'date' => 'Data',
            'datetime' => 'Data e hora',
            'time' => 'Hora',
        ],

        'parameter_value' => 'Valor de parâmetro',
        'parameter_values' => 'Valores de parâmetros',

        'parameter_value_fields' => [
            'parameter_definition_id' => 'Definição',
            'account_id' => 'Conta',
            'value' => 'Valor',
            'add_row' => 'Adicionar valor',
            'definition_help' => 'Qual parâmetro esta linha configura.',
            'account_placeholder' => 'Predefinição (todas as contas)',
            'account_help_system' => 'Definições de âmbito sistema não guardam conta; fica sempre vazio.',
            'account_help' => 'Opcional. Deixe vazio para um valor predefinido para todas as contas, ou escolha uma conta para um valor por conta.',
            'value_help' => 'Valor armazenado (texto livre, ou uma das opções predefinidas se a definição usar lista).',
        ],

        'parameter_value_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'value' => 'Valor',
        ],

        'parameter_value_duplicate' => 'Já existe um valor para esta definição e conta (ou para o padrão de sistema).',
        'parameter_value_account_system' => 'Sistema',

        'plan' => 'Plano',
        'plans' => 'Planos',

        'plan_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'items' => 'Itens do plano',
        ],

        'plan_fields' => [
            'code' => 'Código',
            'active' => 'Ativo',
            'price' => 'Preço',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'price' => 'Preço',
            'active' => 'Ativo',
        ],

        'plan_item' => 'Item do plano',
        'plan_items' => 'Itens do plano',
        'plan_items_standalone' => 'Itens de planos',

        'plan_item_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'plan_item_standalone_columns' => [
            'id' => 'ID',
            'plan' => 'Plano',
            'parent' => 'Pai (nível superior)',
            'text' => 'Texto',
        ],

        'plan_item_standalone_filter_parent_with_children' => 'Item pai (com subitens)',

        'plan_item_fields' => [
            'plan_id' => 'Plano',
            'parent_id' => 'Item pai',
            'parent_root' => '— Nível superior (sem pai) —',
            'untitled_row' => 'Item sem título',
            'add_row' => 'Adicionar item do plano',
            'sort_order' => 'Ordem',
            'active' => 'Ativo',
            'text' => 'Texto',
        ],

        'plan_items_repeater_help' => 'Adicione primeiro os itens de nível superior, depois os subitens e escolha um pai de nível superior. Arraste as linhas para alterar a ordem.',

        'plan_item_columns' => [
            'sort_order' => 'Ordem',
            'parent' => 'Pai',
            'text' => 'Texto',
            'active' => 'Ativo',
        ],

        'nav_contacts' => 'Contactos',
        'nav_plans' => 'Planos e preços',
        'nav_services' => 'Serviços (prestador)',
        'nav_hotels' => 'Hotéis',
        'nav_excursions' => 'Excursões',
        'nav_gastronomy' => 'Gastronomia',
        'nav_parameters' => 'Configurações',
        'nav_users' => 'Utilizadores',
        'nav_authorization' => 'Autorização',

        'service_hotel_type' => 'Tipo de hotel',
        'service_hotel_types' => 'Tipos de hotel',

        'service_hotel_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_hotel_type_fields' => [
            'code' => 'Código',
            'category' => 'Categoria',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_hotel_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoria',
            'name' => 'Nome',
        ],

        'service_hotel_type_category' => 'Categoria de tipo de hotel',
        'service_hotel_type_categories' => 'Categorias de tipos de hotel',

        'service_hotel_type_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_hotel_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_hotel_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_type' => 'Tipo de gastronomia',
        'service_gastronomy_types' => 'Tipos de gastronomia',

        'service_gastronomy_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_type_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_gastronomy_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_venue' => 'Venue de gastronomia',
        'service_gastronomy_venues' => 'Venues de gastronomia',

        'service_gastronomy_venue_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_venue_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_venue_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_cuisine' => 'Cozinha',
        'service_gastronomy_cuisines' => 'Cozinhas',

        'service_gastronomy_cuisine_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_cuisine_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_cuisine_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_menu' => 'Menu de gastronomia',
        'service_gastronomy_menus' => 'Menus de gastronomia',

        'service_gastronomy_menu_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_menu_fields' => [
            'code' => 'Código',
            'is_default' => 'Menu padrão',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_gastronomy_menu_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'is_default' => 'Padrão',
            'name' => 'Nome',
        ],

        'service_gastronomy_menu_category' => 'Categoria de menu de gastronomia',
        'service_gastronomy_menu_categories' => 'Categorias de menu de gastronomia',

        'service_gastronomy_menu_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_menu_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_menu_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_feature_category' => 'Categoria de característica de gastronomia',
        'service_gastronomy_feature_categories' => 'Categorias de características de gastronomia',

        'service_gastronomy_feature_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_feature_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_feature_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_feature' => 'Característica de gastronomia',
        'service_gastronomy_features' => 'Características de gastronomia',

        'service_gastronomy_feature_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_gastronomy_feature_fields' => [
            'category' => 'Categoria',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_gastronomy_feature_columns' => [
            'id' => 'ID',
            'category' => 'Categoria',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_feature_category' => 'Categoria de característica',
        'service_feature_categories' => 'Categorias de características',

        'service_feature_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_feature_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_feature_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_feature' => 'Característica',
        'service_features' => 'Características',

        'service_feature_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'scopes' => 'Scopes',
        ],

        'service_feature_fields' => [
            'category' => 'Categoria',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'scopes' => 'Scopes',
            'is_selectable' => 'Selecionável',
            'parent_id' => 'Pai',
        ],

        'service_feature_columns' => [
            'id' => 'ID',
            'category' => 'Categoria',
            'code' => 'Código',
            'name' => 'Nome',
            'parent' => 'Pai',
        ],
        
        'service_feature_parent_none' => 'Sem pai',
        'service_feature_set_parent' => 'Definir pai',
        'service_feature_set_parent_failure_title' => 'Atribuição de pai inválida',
        'service_feature_set_parent_success_title' => 'Pai atualizado',
        'service_feature_set_parent_failure_body_self' => 'Não é possível definir a própria feature como pai.',
        'service_feature_set_parent_failure_body_cycle' => 'Não é possível definir um pai que crie um ciclo na hierarquia (recursão).',

        'service_feature_scope' => 'Escopo de característica',
        'service_feature_scopes' => 'Escopos de características',

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
            'unique_pair' => 'Esta combinacao tipo-caracteristica ja existe.',
        ],

        'manage_service_feature_scopes' => [
            'navigation_label' => 'Escopos de características (por tipo)',
            'title' => 'Gerir escopos de características por tipo de serviço',
            'service_type' => 'Tipo de serviço',
            'section_categories' => 'Categorias de características',
            'help_categories' => 'Apenas as características das categorias marcadas são listadas abaixo. Use o alternador em massa no cabeçalho da lista para selecionar todas ou nenhuma.',
            'feature_categories' => 'Categorias a incluir',
            'section_in_scope' => 'No escopo deste tipo',
            'section_available' => 'Disponíveis para adicionar',
            'help_in_scope' => 'Desmarque características para as retirar do escopo deste tipo. As alterações aplicam-se ao guardar.',
            'help_available' => 'Marque características para as incluir no escopo deste tipo ao guardar.',
            'in_scope' => 'Características no escopo',
            'available' => 'Características fora do escopo',
            'actions' => [
                'save' => 'Guardar escopos',
            ],
            'notifications' => [
                'saved' => 'Escopos de características guardados.',
            ],
        ],

        'service_type' => 'Tipo de serviço',
        'service_types' => 'Tipos de serviço',

        'service_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_type_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'sort_order' => 'Ordem',
        ],

        'service_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'sort_order' => 'Ordem',
        ],

        'service_activity' => 'Atividade de serviço',
        'service_activities' => 'Atividades de serviço',

        'service_activity_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_activity_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_activity_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_activity_category' => 'Categoria de atividade',
        'service_activity_categories' => 'Categorias de atividade',

        'service_activity_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_activity_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_activity_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_excursion_type' => 'Tipo de excursão',
        'service_excursion_types' => 'Tipos de excursão',

        'service_excursion_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_excursion_type_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_excursion_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_excursion_type_category' => 'Categoria de tipo de excursão',
        'service_excursion_type_categories' => 'Categorias de tipo de excursão',

        'service_excursion_type_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_excursion_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_excursion_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_excursion' => 'Serviço de excursão',
        'service_excursions' => 'Serviços de excursão',

        'service_excursion_tabs' => [
            'general' => 'Geral',
            'technical' => 'Técnico',
        ],

        'service_excursion_fields' => [
            'service_id' => 'Serviço',
            'service_excursion_type_id' => 'Tipo de excursão',
            'difficulty_level' => 'Dificuldade',
            'min_age' => 'Idade mínima',
            'max_age' => 'Idade máxima',
            'guide_included' => 'Guia incluído',
            'transport_included' => 'Transporte incluído',
            'outdoor_activity' => 'Atividade ao ar livre',
            'requires_good_weather' => 'Requer bom tempo',
            'max_altitude_m' => 'Altitude máxima (m)',
            'distance_km' => 'Distância (km)',
        ],

        'service_excursion_columns' => [
            'id' => 'ID',
            'service' => 'Serviço',
            'type' => 'Tipo',
            'difficulty' => 'Dificuldade',
        ],

        'service_excursion_difficulty' => [
            'easy' => 'Fácil',
            'moderate' => 'Moderada',
            'difficult' => 'Difícil',
        ],

        'service_detail_topic' => 'Tema de detalhe',
        'service_detail_topics' => 'Temas de detalhe',

        'service_detail_topic_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_detail_topic_fields' => [
            'code' => 'Código',
            'category' => 'Categoria',
            'visibility' => 'Visibilidade',
            'active' => 'Ativo',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_detail_topic_visibility' => [
            'public' => 'Público',
            'operator' => 'Operador',
            'internal' => 'Interno',
        ],

        'service_detail_topic_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoria',
            'name' => 'Nome',
            'visibility' => 'Visibilidade',
            'active' => 'Ativo',
        ],

        'service_detail_topic_category' => 'Categoria de tema de detalhe',
        'service_detail_topic_categories' => 'Categorias de tema de detalhe',

        'service_detail_topic_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_detail_topic_category_fields' => [
            'code' => 'Código',
            'active' => 'Ativo',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_detail_topic_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'active' => 'Ativo',
        ],

        'service_detail' => 'Detalhe',
        'service_details' => 'Detalhes',

        'service_detail_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_detail_fields' => [
            'service_id' => 'Serviço',
            'service_detail_topic_id' => 'Tema',
            'language_id' => 'Idioma',
            'description' => 'Descrição',
            'active' => 'Ativo',
            'sort_order' => 'Ordem',
            'add' => 'Adicionar detalhe',
        ],

        'service_detail_columns' => [
            'id' => 'ID',
            'service' => 'Serviço',
            'topic' => 'Tema',
            'language' => 'Idioma',
            'description' => 'Descrição',
            'active' => 'Ativo',
        ],

        'service' => 'Serviço',
        'services' => 'Serviços',

        'service_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'variants' => 'Variantes',
            'details' => 'Detalhes',
            'activities' => 'Atividades',
            'media' => 'Imagens',
        ],

        'service_media' => [
            'main_image' => 'Imagem principal',
            'gallery' => 'Galeria (slider)',
            'gallery_help' => 'Imagens opcionais para o carrossel. Arraste para reordenar.',
            'max_image_size_hint' => 'Máximo 3 MB por imagem.',
        ],

        'service_variant_fields' => [
            'add' => 'Adicionar variante',
            'sku' => 'SKU / Código',
            'status' => 'Estado',
            'capacity_min' => 'Capacidade mín.',
            'capacity_max' => 'Capacidade máx.',
            'duration_minutes' => 'Duração (minutos)',
            'pricing_type' => 'Tipo de preço',
            'base_price' => 'Preço base',
            'currency' => 'Moeda',
            'inventory_type' => 'Tipo de inventário',
            'inventory_total' => 'Inventário total',
            'booking_type' => 'Tipo de reserva',
            'min_advance_booking_hours' => 'Antecedência mín. (horas)',
            'max_advance_booking_days' => 'Antecedência máx. (dias)',
            'start_time' => 'Hora início',
            'end_time' => 'Hora fim',
            'sort_order' => 'Ordem',
        ],

        'service_variant_status' => [
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'hidden' => 'Oculto',
        ],

        'service_variant_pricing_type' => [
            'per_person' => 'Por pessoa',
            'per_unit' => 'Por unidade',
            'per_room' => 'Por quarto',
            'per_vehicle' => 'Por veículo',
        ],

        'service_variant_inventory_type' => [
            'unlimited' => 'Ilimitado',
            'fixed' => 'Fixo',
            'request' => 'Sob pedido',
        ],

        'service_variant_booking_type' => [
            'instant' => 'Instantânea',
            'request' => 'Sob pedido',
        ],

        'service_fields' => [
            'account_id' => 'Conta',
            'service_type_id' => 'Tipo de serviço',
            'city_id' => 'Cidade',
            'status' => 'Estado',
            'name' => 'Nome',
            'description' => 'Descrição',
            'activities' => 'Atividades',
            'activities_help' => 'Selecione as atividades que se aplicam a este serviço.',
        ],

        'service_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'service_type' => 'Tipo de serviço',
            'name' => 'Nome',
            'status' => 'Estado',
        ],

        'service_status' => [
            'active' => 'Ativo',
            'onhold' => 'Em espera',
            'inactive' => 'Inativo',
            'terminated' => 'Encerrado',
        ],

        'plan_user_price' => 'Preço por faixa de usuários',
        'plan_user_prices' => 'Preços por faixa de usuários',

        'plan_user_price_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'plan_user_price_fields' => [
            'up_to' => 'Até (quantidade de usuários)',
            'up_to_help' => 'Ex.: 4 para a faixa "1 a 4 usuários", 10 para "5 a 10", etc.',
            'price' => 'Preço',
            'description' => 'Descrição',
        ],

        'plan_user_price_columns' => [
            'id' => 'ID',
            'up_to' => 'Até usuários',
            'up_to_format' => 'Até :count usuários',
            'price' => 'Preço',
        ],

    ],

];
