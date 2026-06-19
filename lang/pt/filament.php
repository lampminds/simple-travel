<?php

/**
 * Portuguese translations for Filament admin (project-specific).
 * Used when locale is pt (e.g. via the panel language switcher).
 */

return [

    'clusters' => [
        'accounts' => 'Contas',
        'catalog' => 'Catálogo',
        'gastronomy' => 'Gastronomia',
        'hospitality' => 'Alojamento',
        'experiences' => 'Experiências',
        'crm' => 'CRM',
        'commercial' => 'Comercial',
        'administration' => 'Administração',
        'system_tables' => 'Tabelas do sistema',
        'transport' => 'Transporte',
    ],

    'panel' => [
        'cluster_subnav_hide' => 'Ocultar menu do módulo',
        'cluster_subnav_show' => 'Mostrar menu do módulo',
    ],

    'common' => [
        'active' => 'Ativo',
        'view' => 'Ver',
        'close' => 'Fechar',
        'copy' => 'Copiar',
        'code_copied' => 'Código copiado para a área de transferência',
        'code_copy_failed' => 'Não foi possível copiar (bloqueio do navegador ou não suportado).',
        'click_to_copy_code' => 'Clique para copiar este código',
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
            'business_types' => 'Tipos de conta',
        ],

        'account_type_category_fields' => [
            'label' => 'Tipos de conta',
            'help' => 'Um ou mais tipos de negócio (prestador, agência, etc.). Geridos em Tipos de conta (parâmetros).',
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
            'city_id' => 'Cidade',
            'state_id' => 'Estado',
            'country_id' => 'País',
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
            'account_type' => 'Tipo de conta',
        ],

        'user_actions' => [
            'impersonate' => 'Personificar',
            'open_website_impersonation' => 'Ligação de acesso ao site',
            'open_website_impersonation_tooltip' => 'Gera uma ligação de utilização única para abrir o site noutro browser como este utilizador.',
            'impersonation_modal_heading' => 'Ligação de acesso ao site (uso único)',
            'impersonation_modal_help' => 'Copie a ligação e abra noutro browser (ou janela privada). Expira em poucos minutos e só funciona uma vez.',
            'impersonation_forbidden' => 'Não tem permissão para gerar esta ligação.',
            'impersonation_invalid_target' => 'Este utilizador não pode ser usado nesta ligação.',
            'impersonation_link_aria' => 'Ligação de acesso de utilização única',
            'impersonation_link_label' => 'Ligação',
            'impersonation_copy_button' => 'Copiar',
            'impersonation_copied' => 'Copiado',
            'impersonation_copy_failed' => 'Falhou ao copiar',
            'impersonation_copy_hint' => 'Dica: também pode fazer triplo clique na caixa e Ctrl+C (Cmd+C no Mac).',
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

        'cat_document' => 'Tipo de documento',
        'cat_documents' => 'Tipos de documento',

        'cat_document_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'cat_document_fields' => [
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
            'language' => 'Idioma',
        ],

        'cat_document_columns' => [
            'id' => 'ID',
            'group' => 'Grupo',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
        ],

        'cat_gender' => 'Género',
        'cat_genders' => 'Géneros',

        'cat_gender_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'cat_gender_fields' => [
            'code' => 'Código',
            'code_help' => 'Identificador estável em inglês (letras, números, hífens e sublinhados). Deve ser único.',
            'name' => 'Nome',
        ],

        'cat_gender_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'translations_count' => 'Idiomas',
        ],

        'cat_gender_filter' => [
            'active_status' => 'Estado',
            'active_only' => 'Só ativos',
            'inactive_only' => 'Só inativos',
            'active_all' => 'Todos',
        ],

        'account_type' => 'Tipo de conta',
        'account_types' => 'Tipos de conta',

        'account_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'account_type_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'account_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'user' => 'Utilizador',
        'users' => 'Utilizadores',

        'user_tabs' => [
            'general' => 'Perfil',
            'accounts_roles' => 'Contas e funções',
        ],

        'user_fields' => [
            'accounts' => 'Contas',
            'name' => 'Nome',
            'email' => 'E-mail',
            'password' => 'Palavra-passe',
            'roles' => 'Funções',
            'memberships_heading' => 'Contas e permissões',
            'memberships_help' => 'Uma linha por conta. Escolha primeiro a conta e depois as funções dessa equipa (Spatie usa account_id como equipa).',
            'account' => 'Conta',
            'add_membership' => 'Adicionar conta',
        ],

        'user_columns' => [
            'id' => 'ID',
            'accounts' => 'Contas',
            'name' => 'Nome',
            'email' => 'E-mail',
            'roles' => 'Funções',
        ],
        'user_filters' => [
            'account' => 'Empresa',
        ],

        'user_invitation' => 'Convite',
        'user_invitations' => 'Convites',

        'user_invitation_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'account_inviting' => 'Conta que convida',
            'email' => 'E-mail',
            'name' => 'Nome do contacto',
            'company_name' => 'Empresa',
            'role' => 'Função',
            'type' => 'Tipo',
            'status' => 'Status',
            'expires_at' => 'Expira em',
            'invited_by' => 'Convidado por',
        ],

        'user_invitation_fields' => [
            'account_id' => 'Conta',
            'account_inviting' => 'Conta que convida',
            'account_inviting_helper' => 'A conta que gerou o convite (p. ex. operador). Se ficar vazio ao criar, usa a mesma que Conta.',
            'invited_account_id' => 'Empresa destino (utilizador existente)',
            'email' => 'E-mail',
            'name' => 'Nome do contacto',
            'company_name' => 'Nome da empresa',
            'role_id' => 'Função',
            'role_external_owner' => 'owner (empresa nova)',
            'role_id_external_helper' => 'Convites externos usam sempre a função owner na nova empresa criada no registo.',
            'type' => 'Tipo',
            'status' => 'Status',
            'expires_at' => 'Expira em',
            'invited_by' => 'Convidado por',
            'token' => 'Token',
            'accepted_at' => 'Aceite em',
            'declined_at' => 'Recusado em',
        ],

        'user_invitation_filters' => [
            'type' => 'Tipo',
            'status' => 'Status',
        ],

        'role' => 'Função',
        'roles' => 'Funções',

        'role_fields' => [
            'account_id' => 'Conta',
            'name' => 'Nome',
            'permissions' => 'Permissões',
        ],

        'role_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'name' => 'Nome',
            'permissions_count' => 'Permissões',
        ],

        'role_filters' => [
            'account_id' => 'Conta',
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

        'account_document' => 'Documento da conta',
        'account_documents' => 'Documentos da conta',

        'account_document_fields' => [
            'account_id' => 'Conta',
            'document_id' => 'Tipo de documento',
            'value' => 'Valor',
            'add' => 'Adicionar identificação fiscal',
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
            'is_unique_per_person' => 'Único por pessoa',
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

        'person' => 'Pessoa',
        'persons' => 'Pessoas',

        'person_tabs' => [
            'general' => 'Geral',
            'users' => 'Utilizadores ligados',
            'account_memberships' => 'Contas vinculadas',
            'contact_methods' => 'Meios de contacto',
            'contact_links' => 'Ligações entre contas',
        ],

        'person_fields' => [
            'name' => 'Nome',
            'document_name' => 'Nome nos documentos',
            'given_name' => 'Nome(s) próprio(s)',
            'family_name' => 'Apelido(s)',
            'date_of_birth' => 'Data de nascimento',
            'gender_id' => 'Género',
            'nationality_id' => 'Nacionalidade',
            'user_id' => 'Utilizador',
            'add_user_link' => 'Ligar utilizador',
            'account_id' => 'Conta',
            'contact_department_id' => 'Departamento',
            'contact_position_id' => 'Cargo',
            'is_primary' => 'Contacto principal da conta',
            'is_public_contact' => 'Contacto público',
            'is_preferred_contact_mode' => 'Modo de contacto preferido',
            'add_account_membership' => 'Adicionar conta vinculada',
            'contact_type_id' => 'Tipo de canal',
            'contact_method_value' => 'Valor',
            'contact_method_is_primary' => 'Principal neste canal',
            'is_verified' => 'Verificado',
            'add_contact_method' => 'Adicionar meio de contacto',
            'link_account_id' => 'Conta (dona da ligação)',
            'link_source_account_id' => 'Conta de origem do contacto',
            'is_favorite' => 'Favorito',
            'visibility' => 'Visibilidade',
            'add_contact_link' => 'Adicionar ligação entre contas',
        ],

        'person_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'gender' => 'Género',
            'date_of_birth' => 'Data de nascimento',
            'users_count' => 'Utilizadores',
            'account_memberships_count' => 'Contas vinculadas',
            'contact_methods_count' => 'Meios',
            'contact_links_count' => 'Ligações',
        ],

        'person_visibility' => [
            'private' => 'Privado',
            'shared' => 'Partilhado',
        ],

        'provider' => 'Prestador',
        'providers' => 'Prestadores',

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
            'city_id' => 'C�digo da cidade',
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

        'provider_category' => 'Categoria de prestador',
        'provider_categories' => 'Categorias de prestador',

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
            'list_order' => 'Ordem',
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

        'lmp_country' => 'País',
        'lmp_countries' => 'Países',

        'lmp_country_fields' => [
            'name' => 'Nome',
            'iso_2' => 'ISO 2',
            'iso_3' => 'ISO 3',
            'phonecode' => 'Código telefónico',
            'capital' => 'Capital',
            'currency_id' => 'Moeda',
            'tld' => 'Domínio de nível superior',
            'emoji' => 'Emoji',
        ],

        'lmp_country_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'iso_2' => 'ISO 2',
            'iso_3' => 'ISO 3',
            'capital' => 'Capital',
            'currency' => 'Moeda',
        ],

        'lmp_state' => 'Estado / província',
        'lmp_states' => 'Estados / províncias',

        'lmp_state_fields' => [
            'name' => 'Nome',
            'country_id' => 'País',
            'level' => 'Nível',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'timezone_id' => 'ID do fuso horário',
            'parent_id' => 'Estado pai',
        ],

        'lmp_state_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'country' => 'País',
            'parent' => 'Estado pai',
            'level' => 'Nível',
        ],

        'lmp_state_filters' => [
            'country_id' => 'País',
        ],

        'lmp_city' => 'Cidade',
        'lmp_cities' => 'Cidades',

        'lmp_city_fields' => [
            'name' => 'Nome',
            'state_id' => 'Estado / província',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'timezone_id' => 'ID do fuso horário',
        ],

        'lmp_city_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'state' => 'Estado / província',
            'country' => 'País',
            'system_transfer_locations' => 'POIs transfer (sistema)',
        ],

        'lmp_city_filters' => [
            'country_id' => 'País',
            'state_id' => 'Estado / província',
        ],

        'lmp_city_actions' => [
            'generate_transfer_locations' => 'Gerar locais de transfer',
            'generate_transfer_locations_heading' => 'Gerar catálogo de transfer (sistema)',
            'generate_transfer_locations_description' => 'Usa OpenAI para sugerir pontos desta cidade (account_id null). Com tradução: uma chamada para a lista e uma ou duas chamadas em lote para todos os nomes (sem MyMemory/Google por ponto).',
            'replace_existing' => 'Substituir catálogo existente desta cidade',
            'replace_existing_help' => 'Remove todos os locais de sistema vinculados a esta cidade antes de inserir os novos.',
            'translate_to_other_languages' => 'Traduzir para os outros idiomas ativos',
            'translate_to_other_languages_help' => 'Segunda requisição à OpenAI: traduz todos os nomes para os outros idiomas ativos em uma ou duas chamadas em lote.',
            'source_language' => 'Idioma de origem para rótulos da IA',
            'max_suggestions' => 'Máximo de sugestões',
            'additional_context' => 'Contexto adicional (opcional)',
            'generate_failed_title' => 'Não foi possível gerar locais',
            'generate_none_title' => 'Nenhum local criado',
            'generate_none_body' => 'A IA não retornou linhas novas (duplicados ignorados: :skipped).',
            'generate_success_title' => 'Catálogo de transfer atualizado',
            'generate_success_body' => 'Criados :created local(is). A IA sugeriu :ai. Ignorados :skipped duplicado(s). Removidos :removed registro(s) anterior(es). Chamadas OpenAI: :openai_calls.',
            'generate_translation_fallbacks' => ':count nome(s) permaneceram no idioma de origem porque a tradução falhou.',
            'openai_rate_limit' => 'OpenAI atingiu o limite de requisições (request limit exceeded). Aguarde alguns minutos e tente de novo, ou verifique o uso em platform.openai.com. Embeddings e esta ação usam a mesma API key.',
            'openai_quota' => 'Cota ou crédito da OpenAI esgotado. Adicione saldo em platform.openai.com — nada foi salvo.',
            'openai_invalid_key' => 'A OpenAI rejeitou a API key. Verifique OPENAI_API_KEY no .env.',
            'openai_model' => 'O modelo de chat ":model" não está disponível para esta chave. Defina OPENAI_CHAT_MODEL no .env (ex.: gpt-4o-mini).',
            'openai_generic' => 'Falha na requisição à OpenAI: :detail',
        ],

        'currency_cat_catalog_label' => 'Moeda #:id (ref #:ref)',

        'currency_rate' => 'Taxa de câmbio',
        'currency_rates' => 'Taxas de câmbio',

        'currency_rate_fields' => [
            'account_id' => 'Conta',
            'account_id_help' => 'Deixe vazio para taxas oficiais do sistema. Indique uma conta para override do cliente.',
            'currency_id' => 'Moeda',
            'source' => 'Fonte',
            'source_help' => 'Reservado para uso futuro (diferentes fontes de cotação).',
            'units_per_usd_buy' => 'Compra (unidades por 1 USD)',
            'units_per_usd_sell' => 'Venda (unidades por 1 USD)',
            'units_per_usd_help' => 'Quantas unidades desta moeda equivalem a 1 dólar. Para USD ambos são 1.',
            'starting_at' => 'Válido a partir de',
            'starting_at_help' => 'Válido a partir desta data (início do dia) até um registro mais recente do mesmo âmbito.',
            'is_active' => 'Ativo',
        ],

        'currency_rate_columns' => [
            'id' => 'ID',
            'account' => 'Âmbito',
            'currency' => 'Moeda',
            'units_per_usd_buy' => 'Compra / USD',
            'units_per_usd_sell' => 'Venda / USD',
            'starting_at' => 'Válido a partir de',
            'is_active' => 'Ativo',
        ],

        'currency_rate_scope' => [
            'system' => 'Sistema',
        ],

        'currency_rate_filters' => [
            'all_active_states' => 'Todos',
            'active_only' => 'Somente ativos',
            'inactive_only' => 'Somente inativos',
            'scope' => 'Âmbito',
            'all_scopes' => 'Todos os âmbitos',
            'system_only' => 'Somente sistema',
            'tenant_only' => 'Somente overrides de conta',
        ],

        'currency_rate_validation' => [
            'duplicate_starting_at' => 'Já existe uma taxa para esta moeda, âmbito e data de vigência.',
            'units_must_be_positive' => 'O valor deve ser maior que zero.',
        ],

        'menu' => 'Item de menu',
        'menus' => 'Menus do site',

        'menu_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'account_types' => 'Visibilidade por tipo',
        ],

        'menu_fields' => [
            'slug' => 'Slug',
            'slug_help' => 'Chave interna (única). Usada no código; pode não aparecer no site público.',
            'parent_id' => 'Pai',
            'icon' => 'Ícone',
            'route' => 'Nome da rota',
            'translation_name' => 'Rótulo',
            'translation_tip' => 'Tooltip',
            'excluded_account_types' => 'Oculto para tipos de conta',
            'excluded_account_types_help' => 'Deixe vazio para mostrar o item a todos os tipos. Marque os tipos que não devem ver este menu.',
        ],

        'menu_columns' => [
            'id' => 'ID',
            'label' => 'Rótulo',
            'route' => 'Rota',
            'parent' => 'Pai',
            'parent_none' => '— Nível raiz —',
            'excluded_account_types' => 'Oculto para tipos',
            'excluded_account_types_none' => 'Todos os tipos',
        ],

        'menu_duplicate' => 'Duplicar',

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
            'value' => 'Valor',
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
            'account_types' => 'Tipos de conta',
            'modules' => 'Módulos',
            'items' => 'Itens do plano',
        ],

        'plan_fields' => [
            'code' => 'Código',
            'active' => 'Ativo',
            'usd_price' => 'Preço em USD',
            'name' => 'Nome',
            'description' => 'Descrição',
            'account_types' => 'Tipos de conta aplicáveis',
            'account_types_help' => 'Deixe vazio para todos os tipos. Selecione tipos para restringir este plano.',
        ],

        'plan_relation' => [
            'modules_tab' => 'Módulos do plano',
            'module' => 'Módulo',
            'module_code' => 'Código',
            'module_name' => 'Nome',
            'add_module' => 'Adicionar módulo',
        ],

        'plan_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'account_types' => 'Tipos',
            'account_types_all' => 'Todos os tipos de conta',
            'modules_count' => 'Módulos',
            'usd_price' => 'Preço em USD',
            'active' => 'Ativo',
        ],

        'plan_filter' => [
            'account_type' => 'Tipo de conta',
            'account_type_placeholder' => 'Todos os tipos de conta',
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

        'module' => 'Módulo',
        'modules' => 'Módulos',

        'module_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
            'account_types' => 'Tipos de conta',
            'features' => 'Características',
            'pricing' => 'Preços',
        ],

        'module_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'account_types' => 'Tipos de conta aplicáveis',
            'account_types_help' => 'Deixe vazio para todos os tipos. Selecione tipos para restringir este módulo.',
        ],

        'module_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'active' => 'Ativo',
            'account_types' => 'Tipos de conta',
            'account_types_all' => 'Todos os tipos de conta',
        ],

        'module_filter' => [
            'account_type' => 'Tipo de conta',
            'account_type_placeholder' => 'Todos os tipos de conta',
        ],

        'module_actions' => [
            'copy' => 'Copiar',
            'copy_heading' => 'Copiar módulo',
            'copy_description' => 'Cria um módulo novo com traduções, tipos de conta, características e preços copiados deste registro. As atribuições a planos não são copiadas.',
            'copy_failed_title' => 'Não foi possível copiar o módulo',
            'copy_code_required' => 'Informe um código para o novo módulo.',
            'copy_code_exists' => 'Já existe um módulo com este código.',
            'copy_success_title' => 'Módulo copiado',
            'copy_success_body' => 'O módulo :code foi criado. Pode revisá-lo e ajustá-lo agora.',
        ],

        'module_relation' => [
            'features_tab' => 'Características',
            'prices_tab' => 'Preços',
        ],

        'module_feature_fields' => [
            'text' => 'Texto da característica',
            'language' => 'Idioma',
            'add' => 'Adicionar característica',
            'add_translation' => 'Adicionar tradução',
        ],

        'module_feature_columns' => [
            'text' => 'Texto',
        ],

        'module_price_fields' => [
            'add' => 'Adicionar preço',
            'billing_type' => 'Modelo de faturação',
            'billing_fixed' => 'Fixo',
            'billing_per_user' => 'Por utilizador',
            'billing_hybrid' => 'Híbrido',
            'billing_usage' => 'Por utilização',
            'base_price' => 'Preço base',
            'base_price_per_user_help' => 'Taxa mensal fixa somada ao componente por usuário.',
            'base_price_fixed_help' => 'Valor mensal fixo do módulo.',
            'included_users' => 'Utilizadores incluídos',
            'included_users_help' => 'Apenas híbrido: utilizadores cobertos pelo preço base. Os adicionais pagam o preço por utilizador.',
            'price_per_user' => 'Preço por utilizador',
            'price_per_user_per_user_help' => 'Total mensal = preço base + (preço por utilizador × quantidade de utilizadores).',
            'price_per_user_hybrid_help' => 'Cobrado por cada utilizador acima dos incluídos.',
            'tiers_section' => 'Escalões por utilizadores',
            'add_tier' => 'Adicionar escalão',
        ],

        'module_price_columns' => [
            'billing_type' => 'Faturação',
            'base_price' => 'Base',
            'price_per_user' => 'Por utilizador',
            'tiers' => 'Escalões',
        ],

        'module_price_tier_fields' => [
            'from_users' => 'De utilizadores',
            'to_users' => 'Até utilizadores',
            'price_per_user' => 'Preço por utilizador',
        ],

        'nav_contacts' => 'Contactos',
        'nav_catalog_conditions' => 'Condições',
        'nav_catalog_experiences' => 'Experiências de serviço',
        'nav_catalog_features' => 'Características',
        'nav_accounts_price_lists' => 'Listas de preços',
        'nav_plans' => 'Planos e preços',
        'nav_services' => 'Serviços',
        'nav_accounts_transfer' => 'Transporte',
        'nav_hotels' => 'Alojamentos',
        'nav_activities' => 'Atividades',
        'nav_gastronomy' => 'Gastronomia',
        'nav_parameters' => 'Configurações',
        'nav_users' => 'Utilizadores',
        'nav_authorization' => 'Autorização',
        'nav_onboarding' => 'Guia de início',
        'nav_ai' => 'Assistente IA',

        'ai_knowledge_item' => 'Artigo de conhecimento',
        'ai_knowledge_items' => 'Base de conhecimento (IA)',

        'ai_knowledge_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'ai_knowledge_fields' => [
            'key' => 'Chave estável',
            'key_help' => 'Apenas letras, números e sublinhados (ex.: editar_imagem_servico).',
            'title' => 'Título',
            'content_short' => 'Resumo curto',
            'content' => 'Corpo',
            'url' => 'URL relacionada',
            'tags' => 'Etiquetas',
            'tags_help' => 'Palavras-chave separadas por vírgulas para filtrar.',
        ],

        'ai_knowledge_columns' => [
            'id' => 'ID',
            'key' => 'Chave',
            'title_preview' => 'Título (primeiro idioma)',
            'translations_count' => 'Idiomas',
        ],

        'ai_usage_log' => 'Consumo de IA',
        'ai_usage_logs' => 'Consumos de IA',

        'ai_usage_log_columns' => [
            'created_at' => 'Data',
            'usage_type' => 'Tipo',
            'user' => 'Utilizador',
            'account' => 'Conta',
            'total_tokens' => 'Tokens',
            'estimated_usd' => 'Custo estimado (USD)',
        ],

        'ai_usage_log_types' => [
            'assistant' => 'Assistente',
            'translation' => 'Tradução (API gratuita)',
            'openai_translation' => 'Tradução OpenAI',
        ],

        'ai_usage_log_filters' => [
            'user' => 'Utilizador',
            'account' => 'Conta',
            'date_range' => 'Intervalo de datas',
            'created_from' => 'De',
            'created_until' => 'Até',
        ],

        'cat_faq' => 'Pergunta frequente',
        'cat_faqs' => 'Perguntas frequentes',

        'cat_faq_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'cat_faq_fields' => [
            'code' => 'Código',
            'code_help' => 'Identificador estável em inglês (letras, números, hífens e sublinhados).',
            'account_type' => 'Tipo de conta (opcional)',
            'sort_order' => 'Ordem',
            'notes' => 'Notas internas',
            'question' => 'Pergunta',
            'answer' => 'Resposta',
        ],

        'cat_faq_columns' => [
            'id' => 'ID',
            'sort_order' => 'Ordem',
            'code' => 'Código',
            'account_type' => 'Tipo de conta',
            'question_preview' => 'Pergunta',
            'translations_count' => 'Idiomas',
        ],

        'cat_faq_filter' => [
            'active_status' => 'Estado',
            'active_only' => 'Somente ativas',
            'inactive_only' => 'Somente inativas',
            'active_all' => 'Todas',
        ],

        'cat_booking_status' => 'Estado de reserva',
        'cat_booking_statuses' => 'Estados de reserva',

        'cat_booking_status_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'cat_booking_status_fields' => [
            'type' => 'Âmbito',
            'code' => 'Código',
            'code_help' => 'Identificador estável em inglês (letras, números, hífens e sublinhados). Único por âmbito.',
            'sort_order' => 'Ordem',
            'name' => 'Nome',
            'help_tip' => 'Texto de ajuda',
            'description' => 'Descrição',
        ],

        'cat_booking_status_columns' => [
            'id' => 'ID',
            'sort_order' => 'Ordem',
            'type' => 'Âmbito',
            'code' => 'Código',
            'name' => 'Nome',
            'translations_count' => 'Idiomas',
        ],

        'cat_booking_status_type' => [
            'main' => 'Cabeçalho da reserva',
            'item' => 'Item da reserva',
        ],

        'cat_booking_status_filter' => [
            'active_status' => 'Estado',
            'active_only' => 'Somente ativos',
            'inactive_only' => 'Somente inativos',
            'active_all' => 'Todos',
        ],

        'todo_category' => 'Categoria de tarefas',
        'todo_categories' => 'Categorias de tarefas',

        'todo_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'todo_category_fields' => [
            'code' => 'Código',
            'sort_order' => 'Ordem',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'todo_category_columns' => [
            'id' => 'ID',
            'sort_order' => 'Ordem',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'todo_category_actions' => [
            'copy_to_account' => 'Copiar para conta',
            'copy_to_account_heading' => 'Copiar tarefas para uma conta',
            'copy_to_account_description' => 'Cria uma cópia de cada tarefa desta categoria (de todas as contas), todas atribuídas à conta que escolher. Duplica linhas em todo_tasks e todo_task_translations.',
            'copy_destination_account' => 'Conta',
            'copy_failed_title' => 'Não foi possível copiar as tarefas',
            'copy_invalid_account' => 'Selecione uma conta válida.',
            'copy_none_title' => 'Não há tarefas para copiar',
            'copy_none_body' => 'Esta categoria ainda não tem tarefas.',
            'copy_all_skipped_title' => 'Nenhuma tarefa nova criada',
            'copy_all_skipped_body' => 'Todas as :skipped tarefa(s) já existiam na conta selecionada (mesmo código).',
            'copy_success_title' => 'Tarefas copiadas',
            'copy_success_body' => 'Foram criadas :created tarefa(s). :skipped ignorada(s) (mesmo código na conta).',
        ],

        'todo_task' => 'Tarefa (modelo)',
        'todo_tasks' => 'Tarefas (modelos)',

        'todo_task_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'todo_task_fields' => [
            'account_id' => 'Conta',
            'code' => 'Código',
            'todo_category_id' => 'Categoria',
            'original_task_id' => 'Baseada na tarefa (opcional)',
            'action_type' => 'Tipo de ação',
            'action_url' => 'URL',
            'action_url_help' => 'URL completa (incluindo https://).',
            'route_name' => 'Rota',
            'route_name_help' => 'Rotas GET nomeadas da aplicação (Filament, Livewire e rotas internas semelhantes ficam ocultas).',
            'verification_type' => 'Tipo de verificação',
            'verification_url' => 'URL de verificação',
            'sort_order' => 'Ordem',
            'name' => 'Título',
            'description' => 'Descrição',
        ],

        'todo_task_action_types' => [
            'none' => 'Nenhuma',
            'route' => 'Rota',
            'url' => 'URL',
            'external' => 'Externo',
        ],

        'todo_task_verification_types' => [
            'none' => 'Nenhuma',
            'api-check' => 'Verificação API',
        ],

        'todo_task_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'sort_order' => 'Ordem',
            'code' => 'Código',
            'category' => 'Categoria',
            'name' => 'Título',
            'action_type' => 'Ação',
            'verification_type' => 'Verificação',
        ],

        'todo_task_filters' => [
            'account_id' => 'Conta',
        ],

        'todo_category_filters' => [
            'account_id' => 'Com tarefas da conta',
        ],

        'service_hotel_type' => 'Tipo de alojamento',
        'service_hotel_types' => 'Tipos de alojamento',

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

        'service_hotel_type_category' => 'Categoria de tipo de alojamento',
        'service_hotel_type_categories' => 'Categorias de tipos de alojamento',

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

        'service_gastronomy_type' => 'Tipo',
        'service_gastronomy_types' => 'Tipos',

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

        'service_gastronomy_type_assignment' => 'Atribuição de tipo gastronómico',
        'service_gastronomy_type_assignments' => 'Atribuições de tipos gastronómicos',

        'service_gastronomy_type_assignment_tabs' => [
            'general' => 'Geral',
        ],

        'service_gastronomy_type_assignment_fields' => [
            'service_gastronomy_id' => 'Perfil de gastronomia',
            'service_gastronomy_type_id' => 'Tipo de gastronomia',
        ],

        'service_gastronomy_type_assignment_columns' => [
            'id' => 'ID',
            'service' => 'Serviço',
            'type' => 'Tipo',
        ],

        'service_gastronomy_venue' => 'Venue',
        'service_gastronomy_venues' => 'Venues',

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

        'service_gastronomy_menu' => 'Menu',
        'service_gastronomy_menus' => 'Menus',

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

        'service_gastronomy_menu_category' => 'Categoria de menu',
        'service_gastronomy_menu_categories' => 'Categorias de menus',

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

        'service_gastronomy_feature_category' => 'Categoria de característica',
        'service_gastronomy_feature_categories' => 'Categorias de características',

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

        'service_gastronomy_feature' => 'Característica',
        'service_gastronomy_features' => 'Características',

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
            'navigation_label' => 'Scopes por tipo de serviço',
            'title' => 'Gerir scopes por tipo de serviço',
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
                'saved' => 'Scopes guardados.',
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
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
        ],

        'service_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'description' => 'Descrição',
            'sort_order' => 'Ordem',
        ],

        'service_experience' => 'Experiência de serviço',
        'service_experiences' => 'Experiências de serviço',

        'service_experience_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_experience_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_experience_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_experience_category' => 'Categoria de experiência',
        'service_experience_categories' => 'Categorias de experiência',

        'service_experience_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_experience_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_experience_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_activity_type' => 'Tipo de atividade',
        'service_activity_types' => 'Tipos de atividade',

        'service_activity_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_activity_type_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_activity_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
            'category' => 'Categoria',
        ],

        'service_activity_type_category' => 'Categoria de tipo de atividade',
        'service_activity_type_categories' => 'Categorias de tipo de atividade',

        'service_activity_type_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_activity_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_activity_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_activity' => 'Serviço de atividade',
        'service_activities' => 'Serviços de atividade',

        'service_activity_tabs' => [
            'general' => 'Geral',
            'technical' => 'Técnico',
        ],

        'service_activity_fields' => [
            'service_id' => 'Serviço',
            'service_activity_type_id' => 'Tipo de atividade (legado)',
            'activity_types' => 'Tipos de atividade',
            'activity_types_help' => 'Selecione um ou mais tipos do catálogo que descrevam este serviço.',
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

        'service_activity_columns' => [
            'id' => 'ID',
            'service' => 'Serviço',
            'type' => 'Tipo',
            'difficulty' => 'Dificuldade',
        ],

        'service_activity_difficulty' => [
            'easy' => 'Fácil',
            'moderate' => 'Moderada',
            'difficult' => 'Difícil',
        ],

        'service_detail_topic' => 'Tema de condição',
        'service_detail_topics' => 'Temas de condição',

        'service_detail_topic_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],

        'service_detail_topic_fields' => [
            'code' => 'Código',
            'category' => 'Categoria',
            'visibility' => 'Visibilidade',
            'scope' => 'Âmbito',
            'condition_key' => 'Chave de condição padrão',
            'active' => 'Ativo',
            'name' => 'Nome',
            'description' => 'Descrição',
        ],

        'service_detail_topic_scopes' => [
            'informational' => 'Informativo',
            'service' => 'Serviço',
            'commercial' => 'Comercial',
            'legal' => 'Legal',
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
            'scope' => 'Âmbito',
            'condition_key' => 'Chave de condição',
            'active' => 'Ativo',
        ],

        'service_detail_condition_key' => 'Chave de condição',
        'service_detail_condition_keys' => 'Chaves de condição',

        'service_detail_condition_key_categories' => [
            'payment' => 'Pagamento',
            'operation' => 'Operação',
            'transport' => 'Transporte',
            'accommodation' => 'Alojamento',
            'safety' => 'Segurança',
            'legal' => 'Legal',
            'inclusions' => 'Inclusões',
            'traveler' => 'Viajante',
            'service' => 'Serviço',
        ],

        'service_detail_condition_key_fields' => [
            'category' => 'Categoria',
            'code' => 'Código',
            'code_help' => 'Identificador curto em inglês (ex. cancellation_policy).',
            'description' => 'Descrição',
        ],

        'service_detail_condition_key_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoria',
            'description' => 'Descrição',
        ],

        'service_detail_topic_category' => 'Categoria de condição',
        'service_detail_topic_categories' => 'Categorias de condição',

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

        'service_detail' => 'Condição',
        'service_details' => 'Condições',

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
            'add' => 'Adicionar condição',
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
            'details' => 'Condições',
            'experiences' => 'Experiências',
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
            'suspended' => 'Suspenso',
            'discontinued' => 'Descontinuado',
        ],

        'service_variant_pricing_type' => [
            'per_person' => 'Por pessoa',
            'per_unit' => 'Por unidade',
            'per_room' => 'Por quarto',
            'per_vehicle' => 'Por veículo',
            'per_group' => 'Por grupo',
        ],

        'service_variant_inventory_type' => [
            'unlimited' => 'Ilimitado',
            'per_day' => 'Por dia',
            'per_timeslot' => 'Por faixa horária',
            'per_departure' => 'Por partida',
        ],

        'service_fields' => [
            'account_id' => 'Conta',
            'service_type_id' => 'Tipo de serviço',
            'city_id' => 'C�digo da cidade',
            'status' => 'Estado',
            'name' => 'Nome',
            'description' => 'Descrição',
            'experiences' => 'Experiências',
            'experiences_help' => 'Selecione as experiências que se aplicam a este serviço.',
        ],

        'service_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'service_type' => 'Tipo de serviço',
            'name' => 'Nome',
            'status' => 'Estado',
        ],

        'service_delete_cascade' => [
            'modal_heading' => 'Eliminar serviço',
            'modal_intro' => 'O serviço e todos os registos relacionados indicados abaixo serão eliminados permanentemente.',
            'modal_confirm' => 'Eliminar serviço e dados relacionados',
            'grand_total' => 'Total de linhas a remover (incluindo este serviço): :count',
            'labels' => [
                'translations' => 'Traduções do serviço',
                'experience_assignments' => 'Atribuições de experiências',
                'details' => 'Linhas de condição do serviço',
                'feature_links' => 'Ligações a características',
                'variants' => 'Variantes',
                'variant_translations' => 'Traduções de variantes',
                'variant_availability_rules' => 'Regras de disponibilidade das variantes',
                'variant_availability_overrides' => 'Exceções de disponibilidade',
                'price_list_items' => 'Itens de listas de preços',
                'allocations' => 'Alocações',
                'service_offers' => 'Ofertas entre fornecedor e operador',
                'operator_package_items' => 'Itens de pacotes do operador',
                'media_files' => 'Ficheiros na biblioteca de média',
                'hotel_type_assignments' => 'Atribuições de tipos de alojamento',
                'service_hotels' => 'Linhas de perfil de alojamento',
                'service_activity' => 'Linhas de perfil de atividade',
                'gastronomy_menu_assignments' => 'Atribuições de formato de menu',
                'gastronomy_venue_assignments' => 'Atribuições de venue',
                'gastronomy_experiences' => 'Experiências',
                'gastronomy_schedules' => 'Horários',
                'gastronomy_capacities' => 'Capacidades',
                'cuisine_gastronomy_assignments' => 'Atribuições de cozinha',
                'gastronomy_type_assignments' => 'Atribuições de tipos gastronómicos',
                'service_gastronomies' => 'Linhas de perfil',
                'transfer_routes' => 'Rotas de transfer',
                'transfer_schedules' => 'Horários de transfer',
                'transfer_vehicles' => 'Veículos de transfer',
                'transfer_prices' => 'Preços de transfer',
                'service_transfers' => 'Linhas de perfil de transfer',
            ],
        ],

        'service_status' => [
            'active' => 'Ativo',
            'onhold' => 'Em espera',
            'suspended' => 'Suspenso',
            'discontinued' => 'Descontinuado',
            'inactive' => 'Inativo',
            'terminated' => 'Encerrado',
        ],

        'price_list' => 'Lista de preços',
        'price_lists' => 'Listas de preços',

        'price_list_owner_type' => [
            'account' => 'Conta',
            'user' => 'Utilizador',
        ],

        'price_list_fields' => [
            'owner_type' => 'Tipo de proprietário',
            'owner_id' => 'Proprietário',
            'name' => 'Nome',
            'currency_id' => 'Moeda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida até',
            'is_active' => 'Ativa',
            'assignments' => 'Atribuições',
        ],

        'price_list_tabs' => [
            'general' => 'Geral',
            'assignments' => 'Atribuições',
        ],

        'price_list_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'owner' => 'Proprietário',
            'currency' => 'Moeda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida até',
            'is_active' => 'Ativa',
            'items_count' => 'Linhas',
        ],

        'price_list_item' => 'Item de lista',
        'price_list_items' => 'Itens de listas',

        'price_list_item_fields' => [
            'price_list_id' => 'Lista',
            'service_id' => 'Serviço (todas as variantes)',
            'service_variant_id' => 'Variante de serviço',
            'price' => 'Preço',
            'pricing_mode' => 'Modo de preço',
        ],

        'price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Lista',
            'target' => 'Alvo',
            'service_all_variants' => 'Todas as variantes: :label',
            'service_variant' => 'Variante',
            'price' => 'Preço',
            'pricing_mode' => 'Modo',
        ],

        'price_list_item_filters' => [
            'price_list_id' => 'Lista',
        ],

        'price_list_item_pricing_mode' => [
            'fixed' => 'Fixo',
            'percentage' => 'Percentagem',
        ],

        'price_list_assignment_fields' => [
            'operator_id' => 'Operador (conta)',
            'assigned_to_id' => 'Atribuído a (conta)',
            'adjustment_type' => 'Tipo de ajuste',
            'adjustment_value' => 'Valor do ajuste',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido até',
            'is_active' => 'Ativa',
            'add' => 'Adicionar atribuição',
        ],

        'price_list_assignment_adjustment_type' => [
            'none' => 'Sem ajuste',
            'percentage' => 'Percentagem',
            'fixed' => 'Montante fixo',
        ],

        'provider_price_list' => 'Lista de preços (fornecedor)',
        'provider_price_lists' => 'Listas de preços (fornecedor)',

        'provider_price_list_tabs' => [
            'general' => 'Geral',
            'assignments' => 'Atribuições a operadores',
        ],

        'provider_price_list_fields' => [
            'provider_id' => 'Fornecedor (conta)',
            'name' => 'Nome',
            'currency_id' => 'Moeda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida até',
            'is_active' => 'Lista ativa',
            'assignments' => 'Atribuições',
        ],

        'provider_price_list_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'provider' => 'Fornecedor',
            'currency' => 'Moeda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida até',
            'is_active' => 'Ativa',
            'items_count' => 'Linhas',
        ],

        'provider_price_list_assignment_fields' => [
            'operator_id' => 'Operador (conta)',
            'adjustment_type' => 'Tipo de ajuste',
            'adjustment_value' => 'Valor do ajuste',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido até',
            'is_active' => 'Ativa',
            'add' => 'Adicionar atribuição',
        ],

        'provider_price_list_assignment_adjustment_type' => [
            'none' => 'Sem ajuste',
            'percentage' => 'Percentagem',
            'fixed' => 'Montante fixo',
        ],

        'provider_price_list_item' => 'Item de lista (fornecedor)',
        'provider_price_list_items' => 'Itens de listas (fornecedor)',

        'provider_price_list_item_fields' => [
            'price_list_id' => 'Lista de preços (fornecedor)',
            'service_id' => 'Serviço (todas as variantes)',
            'service_variant_id' => 'Variante de serviço',
            'price' => 'Preço',
            'pricing_mode' => 'Modo de preço',
        ],

        'provider_price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Lista',
            'target' => 'Alvo',
            'service_all_variants' => 'Todas as variantes: :label',
            'price' => 'Preço',
            'pricing_mode' => 'Modo',
        ],

        'provider_price_list_item_filters' => [
            'price_list_id' => 'Lista',
        ],

        'provider_price_list_item_pricing_mode' => [
            'variant_base' => 'Preço base da variante',
            'fixed' => 'Fixo',
            'percentage' => 'Percentagem',
        ],

        'operator_price_list' => 'Lista de preços (operador)',
        'operator_price_lists' => 'Listas de preços (operador)',

        'operator_price_list_tabs' => [
            'general' => 'Geral',
            'assignments' => 'Atribuições a agências',
        ],

        'operator_price_list_fields' => [
            'operator_id' => 'Operador (conta)',
            'name' => 'Nome',
            'currency_id' => 'Moeda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida até',
            'is_active' => 'Lista ativa',
            'assignments' => 'Atribuições',
        ],

        'operator_price_list_columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'operator' => 'Operador',
            'currency' => 'Moeda',
            'valid_from' => 'Válida desde',
            'valid_to' => 'Válida até',
            'is_active' => 'Ativa',
            'items_count' => 'Linhas',
        ],

        'operator_price_list_assignment_fields' => [
            'agency_id' => 'Agência (conta)',
            'adjustment_type' => 'Tipo de ajuste',
            'adjustment_value' => 'Valor do ajuste',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido até',
            'is_active' => 'Ativa',
            'add' => 'Adicionar atribuição',
        ],

        'operator_price_list_assignment_adjustment_type' => [
            'none' => 'Sem ajuste',
            'percentage' => 'Percentagem',
            'fixed' => 'Montante fixo',
        ],

        'operator_price_list_item' => 'Item de lista (operador)',
        'operator_price_list_items' => 'Itens de listas (operador)',

        'operator_price_list_item_fields' => [
            'price_list_id' => 'Lista de preços (operador)',
            'catalog_entry_id' => 'Entrada de catálogo',
            'price' => 'Preço',
            'pricing_mode' => 'Modo de preço',
        ],

        'operator_price_list_item_columns' => [
            'id' => 'ID',
            'price_list' => 'Lista',
            'catalog_entry' => 'Catálogo',
            'price' => 'Preço',
            'pricing_mode' => 'Modo',
        ],

        'operator_price_list_item_filters' => [
            'price_list_id' => 'Lista',
        ],

        'operator_price_list_item_pricing_mode' => [
            'fixed_delta' => 'Fixo +/-',
            'percentage' => 'Percentagem',
            'fixed_price' => 'Preço fixo',
            'direct' => 'Preço fixo',
            'fixed' => 'Preço fixo',
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

        'nav_transport' => 'Transfers',

        'service_transfer_location_type_category' => 'Categoria de tipo de local de transfer',
        'service_transfer_location_type_categories' => 'Categorias de tipo de local de transfer',
        'service_transfer_location_type_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],
        'service_transfer_location_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],
        'service_transfer_location_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'service_transfer_location_type' => 'Tipo de local de transfer',
        'service_transfer_location_types' => 'Tipos de local de transfer',
        'service_transfer_location_type_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],
        'service_transfer_location_type_fields' => [
            'code' => 'Código',
            'category' => 'Categoria',
            'sort_order' => 'Ordem',
        ],
        'service_transfer_location_type_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'category' => 'Categoria',
            'name' => 'Nome',
        ],

        'service_transfer_location' => 'Local de transfer',
        'service_transfer_locations' => 'Locais de transfer',
        'service_transfer_location_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],
        'service_transfer_location_fields' => [
            'service_transfer_location_type_id' => 'Tipo de local',
            'address' => 'Morada',
            'city_id' => 'Cidade',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'airport_code' => 'Código aeroporto',
            'is_active' => 'Ativo',
        ],
        'service_transfer_location_columns' => [
            'id' => 'ID',
            'type' => 'Tipo',
            'name' => 'Nome',
            'airport_code' => 'Aeroporto',
            'city' => 'Cidade',
        ],

        'service_transfer_vehicle_type_category' => 'Categoria de tipo de veículo de transfer',
        'service_transfer_vehicle_type_categories' => 'Categorias de tipo de veículo de transfer',
        'service_transfer_vehicle_type_category_tabs' => [
            'general' => 'Geral',
            'translations' => 'Traduções',
        ],
        'service_transfer_vehicle_type_category_fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],
        'service_transfer_vehicle_type_category_columns' => [
            'id' => 'ID',
            'code' => 'Código',
            'name' => 'Nome',
        ],
        'service_transfer_vehicle_type_category_relation' => [
            'vehicle_types_tab' => 'Tipos de veículo',
        ],

        'service_transfer_vehicle_type' => 'Tipo de veículo de transfer',
        'service_transfer_vehicle_types' => 'Tipos de veículo de transfer',
        'service_transfer_vehicle_type_tabs' => [
            'general' => 'Geral',
        ],
        'service_transfer_vehicle_type_fields' => [
            'account_id' => 'Conta',
            'category' => 'Categoria',
            'code' => 'Código',
            'name' => 'Nome',
            'max_passengers' => 'Máx. passageiros',
            'max_luggage' => 'Máx. bagagem',
        ],
        'service_transfer_vehicle_type_columns' => [
            'id' => 'ID',
            'account' => 'Conta',
            'category' => 'Categoria',
            'code' => 'Código',
            'name' => 'Nome',
            'max_passengers' => 'Máx. pax',
            'max_luggage' => 'Máx. bagagem',
        ],

        'service_transfer' => 'Perfil de transfer do serviço',
        'service_transfers' => 'Perfis de transfer',
        'service_transfer_tabs' => [
            'general' => 'Geral',
        ],
        'service_transfer_fields' => [
            'service_id' => 'Serviço',
            'service_variant_id' => 'Variante de serviço',
            'transfer_type' => 'Tipo de trajeto',
            'modality' => 'Modalidade',
            'allows_multiple_stops' => 'Permite várias paragens',
            'max_passengers' => 'Máx. passageiros',
            'max_luggage' => 'Máx. bagagem',
            'default_duration_minutes' => 'Duração predefinida (min)',
            'requires_flight_info' => 'Requer dados de voo',
            'requires_pickup_time' => 'Requer hora de recolha',
            'requires_dropoff_time' => 'Requer hora de entrega',
        ],
        'service_transfer_columns' => [
            'id' => 'ID',
            'service' => 'Serviço',
            'transfer_type' => 'Tipo',
            'modality' => 'Modalidade',
        ],
        'service_transfer_transfer_type' => [
            'one_way' => 'Só ida',
            'round_trip' => 'Ida e volta',
        ],
        'service_transfer_modality' => [
            'private' => 'Privado',
            'shared' => 'Partilhado',
        ],

        'service_transfer_route' => 'Rota de transfer',
        'service_transfer_routes' => 'Rotas de transfer',
        'service_transfer_route_tabs' => [
            'general' => 'Geral',
        ],
        'service_transfer_route_fields' => [
            'service_transfer_id' => 'Perfil de transfer',
            'origin_location_id' => 'Origem',
            'destination_location_id' => 'Destino',
            'is_active' => 'Ativa',
            'distance_km' => 'Distância (km)',
            'duration_minutes' => 'Duração (min)',
        ],
        'service_transfer_route_columns' => [
            'id' => 'ID',
            'transfer' => 'Perfil',
            'origin' => 'Origem',
            'destination' => 'Destino',
        ],
        'service_transfer_route_validation' => [
            'different_endpoints' => 'Origem e destino devem ser diferentes.',
        ],

        'service_transfer_vehicle' => 'Atribuição de veículo',
        'service_transfer_vehicles' => 'Atribuições de veículo',
        'service_transfer_vehicle_tabs' => [
            'general' => 'Geral',
        ],
        'service_transfer_vehicle_fields' => [
            'service_transfer_id' => 'Perfil de transfer',
            'service_transfer_vehicle_type_id' => 'Tipo de veículo',
            'is_default' => 'Predefinido neste perfil',
        ],
        'service_transfer_vehicle_columns' => [
            'id' => 'ID',
            'transfer' => 'Perfil',
            'vehicle_type' => 'Tipo de veículo',
        ],

        'service_transfer_price' => 'Preço de transfer',
        'service_transfer_prices' => 'Preços de transfer',
        'service_transfer_price_tabs' => [
            'general' => 'Geral',
        ],
        'service_transfer_price_fields' => [
            'service_transfer_id' => 'Perfil de transfer',
            'route_id' => 'Rota (opcional)',
            'service_transfer_vehicle_type_id' => 'Tipo de veículo (opcional)',
            'pricing_type' => 'Tipo de preço',
            'currency_id' => 'Moeda',
            'base_price' => 'Preço base',
            'price_per_person' => 'Preço por pessoa',
            'price_per_extra_passenger' => 'Preço por passageiro extra',
            'min_passengers' => 'Mín. passageiros',
            'max_passengers' => 'Máx. passageiros',
            'valid_from' => 'Válido desde',
            'valid_to' => 'Válido até',
        ],
        'service_transfer_price_columns' => [
            'id' => 'ID',
            'transfer' => 'Perfil',
            'route' => 'Rota',
            'vehicle_type' => 'Tipo veículo',
            'pricing_type' => 'Preço',
            'currency' => 'Moeda',
            'base_price' => 'Base',
        ],
        'service_transfer_price_pricing_type' => [
            'per_vehicle' => 'Por veículo',
            'per_person' => 'Por pessoa',
        ],
        'service_transfer_price_validation' => [
            'route_belongs_to_transfer' => 'A rota deve pertencer ao perfil de transfer selecionado.',
        ],

    ],

];

