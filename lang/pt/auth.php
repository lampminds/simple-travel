<?php

/**
 * Portuguese authentication messages (login, throttle, email verification, etc.).
 */

return [

    'form_errors_summary' => 'Ocorreu um erro. Verifique as mensagens abaixo de cada campo.',

    'kicked_out_session' => 'A sua sessão foi terminada por um administrador. Inicie sessão novamente.',

    'failed' => 'As credenciais não correspondem aos nossos registros.',
    'password' => 'A senha fornecida está incorreta.',
    'throttle' => 'Muitas tentativas de acesso. Por favor, tente novamente em :seconds segundos.',

    'login' => [
        'title' => 'Entrar',
        'heading' => 'Bem-vindo de volta!',
        'intro' => 'Introduza o seu e-mail e palavra-passe para iniciar sessão.',
        'email' => 'E-mail',
        'password' => 'Palavra-passe',
        'placeholder_email' => 'E-mail',
        'placeholder_password' => 'Palavra-passe',
        'forgot_password' => 'Esqueceu-se da palavra-passe?',
        'remember_me' => 'Lembrar-me',
        'submit' => 'Entrar',
        'or' => 'OU',
        'github' => 'GitHub',
        'no_account' => 'Ainda não tem conta?',
        'create_account' => 'Criar conta',
    ],

    'register' => [
        'title' => 'Criar conta',
        'heading' => 'Criar conta',
        'intro' => 'Preencha os dados da sua empresa e pessoais para se registrar.',
        'important' => 'Importante:',
        'important_text' => 'Se já existe uma conta para a sua empresa, não se registre aqui; você deve fazer o cadastro através de um convite do administrador da sua empresa (implementaremos isso mais adiante).',
        'company_name' => 'Nome da empresa',
        'company_type' => 'Tipo de empresa',
        'your_name' => 'Seu nome',
        'email' => 'E-mail',
        'password' => 'Senha',
        'password_confirmation' => 'Confirmar senha',
        'placeholder_company_name' => 'Nome da sua empresa',
        'placeholder_name' => 'Seu nome',
        'placeholder_email' => 'E-mail',
        'placeholder_password' => 'Mínimo 8 caracteres',
        'placeholder_password_confirmation' => 'Repita a senha',
        'password_show' => 'Mostrar senha',
        'password_hide' => 'Ocultar senha',
        'password_generate' => 'Gerar senha aleatória',
        'select_type' => 'Selecionar tipo...',
        'submit' => 'Criar conta',
        'or' => 'OU',
        'signup_github' => 'Registrar com Github',
        'already_have_account' => 'Já tem uma conta?',
        'login' => 'Entrar',
        'carousel_slide1_title' => 'Gerencie seu negócio com facilidade',
        'carousel_slide1_text' => 'Destaque sua aplicação com uma landing page de qualidade, projetada e desenvolvida por profissionais.',
        'carousel_slide2_title' => 'A melhor forma de mostrar seu app',
        'carousel_slide2_text' => 'Apresente seu produto de forma clara e atrativa para seus clientes.',
        'carousel_slide3_title' => 'Soluções que convertem leads em clientes',
        'carousel_slide3_text' => 'Ferramentas pensadas para potencializar sua operação e suas vendas.',

        'intro_internal' => 'Complete os seus dados para entrar na conta existente da sua empresa.',
        'intro_external' => 'Foi convidado a experimentar a plataforma. Registe a sua empresa e o seu utilizador como de costume.',
        'important_text_internal' => 'Este link é pessoal. Indique o email que vai usar na conta; não pode estar já registado. Depois terá de o confirmar por email, como em qualquer registo.',
        'important_text_external' => 'Este link é pessoal. Vai criar uma empresa nova. Indique o email que vai usar; não pode estar já registado. Depois terá de o confirmar por email.',
        'invited_company' => 'Empresa',
        'invitation_invalid' => 'Este convite é inválido ou expirou.',
        'invitation_email_mismatch' => 'O email deve coincidir com o convite.',
    ],

    'verification' => [
        'title' => 'Confirmar e-mail',
        'heading' => 'Confirme o seu e-mail',
        'intro' => 'Enviamos um e-mail de boas-vindas com um link para verificar o seu endereço. Clique nesse link para ativar a sua conta.',
        'link_sent' => 'Um novo link de verificação foi enviado para o seu e-mail.',
        'no_email' => 'Se não recebeu o e-mail, pode pedir que enviemos outro.',
        'resend_button' => 'Reenviar e-mail de verificação',
        'continue' => 'Continuar',
        'email_subject' => 'Bem-vindo ao :app – Confirme o seu e-mail',
        'email_greeting' => 'Olá :name!',
        'email_body' => 'Obrigado por se registrar. Para ativar a sua conta, confirme o seu endereço de e-mail clicando no botão abaixo.',
        'email_action' => 'Confirmar e-mail',
        'email_footer' => 'Se não criou uma conta, pode ignorar esta mensagem.',
        'verified' => 'O seu e-mail foi confirmado. Já pode continuar a utilizar a sua conta.',
        'already_verified' => 'O seu e-mail já estava confirmado.',
    ],

];
