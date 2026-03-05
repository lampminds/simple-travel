<?php

/**
 * Portuguese translations for Filament admin (project-specific).
 * Used when locale is pt (e.g. via the panel language switcher).
 */

return [

    'common' => [
        'active' => 'Ativo',
    ],

    'pages' => [
        'list_records_count' => 'Total: :count :label',
    ],

    'resources' => [

        'account' => 'Conta',
        'accounts' => 'Contas',

        'account_tabs' => [
            'main' => 'Dados principais',
            'tax_ids' => 'Identificações fiscais',
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
            'account_id' => 'Conta',
            'name' => 'Nome',
            'email' => 'E-mail',
            'password' => 'Palavra-passe',
            'roles' => 'Funções',
        ],

        'user_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'name' => 'Nome',
            'email' => 'E-mail',
            'roles' => 'Funções',
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
            'sort_order' => 'Ordem',
        ],

        'contact_department_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'sort_order' => 'Ordem',
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
        ],

        'contact_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'name' => 'Nome',
            'department' => 'Departamento',
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

        'service_plan' => 'Plano de serviço',
        'service_plans' => 'Planos de serviço',

        'service_plan_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_plan_fields' => [
            'code' => 'Código',
            'active' => 'Ativo',
            'price' => 'Preço',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_plan_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'price' => 'Preço',
            'active' => 'Ativo',
        ],

        'service_plan_item' => 'Item do plano',
        'service_plan_items' => 'Itens do plano',

        'service_plan_item_fields' => [
            'parent_id' => 'Item pai',
            'sort_order' => 'Ordem',
            'active' => 'Ativo',
            'text' => 'Texto',
        ],

        'service_plan_item_columns' => [
            'sort_order' => 'Ordem',
            'parent' => 'Pai',
            'text' => 'Texto',
            'active' => 'Ativo',
        ],

        'nav_service_plans' => 'Planos e preços',

        'service_user_price' => 'Preço por faixa de usuários',
        'service_user_prices' => 'Preços por faixa de usuários',

        'service_user_price_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_user_price_fields' => [
            'up_to' => 'Até (quantidade de usuários)',
            'up_to_help' => 'Ex.: 4 para a faixa "1 a 4 usuários", 10 para "5 a 10", etc.',
            'price' => 'Preço',
            'description' => 'Descrição',
        ],

        'service_user_price_columns' => [
            'id' => 'ID',
            'up_to' => 'Até usuários',
            'up_to_format' => 'Até :count usuários',
            'price' => 'Preço',
        ],

    ],

];
