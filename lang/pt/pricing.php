<?php

return [
    'page_title' => 'SimpleTravel | Planos e preços',

    // Hero
    'hero_title' => 'Um modelo simples que cresce com sua equipe',
    'hero_lead' => 'Escolha o tipo de conta, informe quantos usuários vai contratar e monte seu plano com o serviço base e os módulos que precisar.',
    'hero_trial_invite' => 'Experimente tudo grátis por 30 dias, com um único usuário incluído.',
    'hero_promo_trial_badge' => 'Grátis',
    'hero_promo_trial_title' => '30 dias para testar tudo',
    'hero_promo_trial_text' => '1 usuário incluído. Plataforma completa, sem cartão para começar.',
    'hero_promo_setup_badge' => 'Sem surpresas',
    'hero_promo_setup_title' => 'Setup $0',
    'hero_promo_setup_text' => 'Sem custos de implementação, ativação ou implantação.',

    'step_account_type' => 'Qual é o seu papel no turismo?',
    'step_account_type_help' => 'Mostramos apenas os módulos disponíveis para operadores, agências ou prestadores.',
    'step_users' => 'Quantos usuários você vai contratar?',
    'step_users_help' => 'O serviço base e alguns módulos calculam o preço conforme a quantidade de usuários ativos.',
    'users_label' => 'Quantidade de usuários',
    'users_context' => 'Preços calculados para :count usuários',

    'step_currency' => 'Em qual moeda deseja ver os preços?',
    'step_currency_help' => 'Os preços do catálogo estão em USD. Mostramos o equivalente com a taxa de câmbio vigente do sistema.',
    'prices_usd_note' => 'Preços de referência em USD.',
    'exchange_rate_note' => 'Taxa de câmbio: 1 USD = :rate :code (em :date).',

    'core_heading' => 'Serviço base',
    'core_intro' => 'O serviço base é obrigatório e inclui o núcleo operativo do sistema.',
    'core_required' => 'Obrigatório',

    'addons_heading' => 'Módulos adicionais',
    'addons_intro' => 'Marque os módulos que deseja incluir. Cada um entra no total estimado.',
    'select_module' => 'Incluir no plano',

    'estimate_heading' => 'Sua estimativa mensal',
    'estimate_intro' => 'Detalhamento do serviço base mais os módulos adicionais selecionados.',
    'estimate_core' => 'Serviço base',
    'estimate_addons' => 'Módulos adicionais',
    'estimate_total' => 'Total estimado',
    'estimate_empty' => 'Não há módulos configurados para este tipo de conta.',
    'estimate_context' => ':count · :code',
    'estimate_cta' => 'Solicitar demo',
    'config_bar_label' => 'Configuração',
    'mobile_view_detail' => 'Ver detalhes',
    'mobile_close_detail' => 'Fechar',
    'users_summary' => ':count usr.',

    'no_modules_for_type' => 'Não há módulos adicionais para este tipo de conta.',
    'custom_quote' => 'Cotação personalizada',

    'billing_fixed' => 'Preço fixo',
    'billing_per_user' => 'Por usuário',
    'billing_per_user_amount' => ':amount por usuário',
    'billing_per_user_base_and_amount' => ':base + :amount por usuário',
    'billing_hybrid' => 'Base + usuários',
    'billing_usage' => 'Conforme uso',
    'billing_not_user_dependent' => 'Não varia com a quantidade de usuários',

    // Block 1: Base por faixa de usuários
    'block1_heading' => 'Abono base por faixa de usuários',
    'block1_intro' => 'O serviço base é obrigatório e inclui o núcleo operativo do sistema. O valor mensal é definido conforme a quantidade de usuários que utilizam a plataforma. Seu investimento acompanha o crescimento da sua equipe.',
    'block1_range_1_4' => '1 a 4 usuários',
    'block1_range_5_10' => '5 a 10 usuários',
    'block1_range_11_20' => '11 a 20 usuários',
    'block1_range_20_plus' => '20+ usuários',
    'block1_range_20_plus_custom' => 'Cotação personalizada',
    'block1_range_1_4_price' => '$$',
    'block1_range_5_10_price' => '$$',
    'block1_range_11_20_price' => '$$',
    'block1_highlight' => 'Sem cobranças por transações, sem comissões por venda e sem limites operativos.',

    'range_label_from_to' => ':from a :to usuários',
    'range_label_up_to' => 'Até :count usuários',

    // Block 2: Módulos adicionais
    'block2_heading' => 'Potencialize sua operação com módulos adicionais',
    'block2_intro' => 'Além do serviço base, você pode incorporar módulos independentes conforme as necessidades da sua empresa. Cada módulo amplia funcionalidades específicas sem afetar o núcleo operativo. Ative só o que precisa. Quando precisar.',

    // Plans section header (legacy / fallback)
    'plans_badge' => 'Planos',
    'plans_heading' => 'Planos e preços',
    'plans_subtitle' => 'Preços que <span class="text-primary fw-bold">funcionam</span> para todos.',

    // Plan names
    'plan_starter' => 'Plano Inicial',
    'plan_professional' => 'Plano Profissional',
    'plan_enterprise' => 'Enterprise',

    // Price display
    'currency' => '$',
    'per_month' => '/ mês',

    // Starter plan
    'plan_starter_price' => '150mil',
    'plan_starter_feature1' => 'Montagem de pacotes dinâmicos',
    'plan_starter_feature2' => 'Serviços individuais (alojamento, traslado, excursão)',
    'plan_starter_feature3' => 'Calendário operativo',
    'plan_starter_feature4' => 'Estados de reserva',
    'plan_starter_feature5' => 'Voucher automático',

    // Professional plan
    'plan_popular_badge' => 'Popular',
    'plan_professional_price' => '99',
    'plan_professional_feature1' => '[Característica 1 do plano Professional]',
    'plan_professional_feature2' => '[Característica 2 do plano Professional]',
    'plan_professional_feature3' => '[Característica 3 do plano Professional]',
    'plan_professional_feature4' => '[Característica 4 do plano Professional]',
    'plan_professional_feature5' => '[Característica 5 do plano Professional]',

    // Enterprise plan
    'plan_enterprise_price' => '599',
    'plan_enterprise_feature1' => '[Característica 1 do plano Enterprise]',
    'plan_enterprise_feature2' => '[Característica 2 do plano Enterprise]',
    'plan_enterprise_feature3' => '[Característica 3 do plano Enterprise]',
    'plan_enterprise_feature4' => '[Característica 4 do plano Enterprise]',
    'plan_enterprise_feature5' => '[Característica 5 do plano Enterprise]',

    'sign_up_now' => 'Cadastre-se agora',
    'no_plans' => 'Não há planos disponíveis no momento.',

    // Benefits section
    'benefits_badge' => 'Benefícios',
    'benefits_heading' => 'Todos os planos incluem estes benefícios',
    'benefits_subtitle' => '[Subtítulo opcional da seção de benefícios]',

    'benefit1_title' => '[Título do benefício 1]',
    'benefit1_desc' => '[Descrição do benefício 1. Completar.]',

    'benefit2_title' => '[Título do benefício 2]',
    'benefit2_desc' => '[Descrição do benefício 2. Completar.]',

    'benefit3_title' => '[Título do benefício 3]',
    'benefit3_desc' => '[Descrição do benefício 3. Completar.]',

    'benefit4_title' => '[Título do benefício 4]',
    'benefit4_desc' => '[Descrição do benefício 4. Completar.]',

    // FAQ section
    'faq_badge' => 'Perguntas frequentes',
    'faq_heading' => 'Perguntas frequentes',
    'faq_subtitle' => '[Subtítulo da seção de perguntas frequentes]',

    'faq1_q' => '[Pergunta frequente 1]',
    'faq1_a' => '[Resposta à pergunta 1. Completar.]',

    'faq2_q' => '[Pergunta frequente 2]',
    'faq2_a' => '[Resposta à pergunta 2. Completar.]',

    'faq3_q' => '[Pergunta frequente 3]',
    'faq3_a' => '[Resposta à pergunta 3. Completar.]',

    'faq4_q' => '[Pergunta frequente 4]',
    'faq4_a' => '[Resposta à pergunta 4. Completar.]',

    // CTA section
    'cta_heading' => 'Tem mais dúvidas?',
    'cta_subtitle' => '[Subtítulo da seção de contato]',

    'cta_contact_title' => 'Fale conosco',
    'cta_contact_desc' => '[Descrição breve que convida a entrar em contato. Completar.]',
    'cta_contact_button' => 'Fale conosco',

    'cta_kb_title' => '[Título do bloco Base de conhecimento / Ajuda]',
    'cta_kb_desc' => '[Descrição do bloco. Completar.]',
    'cta_kb_button' => 'Explorar',
];
