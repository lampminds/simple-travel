<?php

/**
 * Spanish authentication messages (login, throttle, email verification, etc.).
 */

return [

    'form_errors_summary' => 'Hubo un error. Revisá los mensajes debajo de cada campo.',

    'kicked_out_session' => 'Tu sesión fue cerrada por un administrador. Volvé a iniciar sesión.',

    'failed' => 'Las credenciales no coinciden con nuestros registros.',
    'password' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos de acceso. Por favor, inténtelo de nuevo en :seconds segundos.',

    'login' => [
        'title' => 'Ingresar',
        'heading' => '¡Bienvenido de nuevo!',
        'intro' => 'Ingresá tu email y contraseña para acceder.',
        'email' => 'Email',
        'password' => 'Contraseña',
        'placeholder_email' => 'Email',
        'placeholder_password' => 'Contraseña',
        'forgot_password' => '¿Olvidaste tu contraseña?',
        'remember_me' => 'Recordarme',
        'submit' => 'Ingresar',
        'or' => 'OR',
        'google' => 'Google',
        'facebook' => 'Facebook',
        'social_auth_failed' => 'No se pudo completar el acceso con la red social. Intentá nuevamente.',
        'social_email_required' => 'No pudimos obtener tu email desde la red social. Usá acceso con email y contraseña.',
        'social_user_not_found' => 'No encontramos una cuenta con ese email. Primero registrate con email y contraseña.',
        'no_account' => '¿No tenés cuenta?',
        'create_account' => 'Crear cuenta',
    ],

    'register' => [
        'title' => 'Crear cuenta',
        'heading' => 'Crear cuenta',
        'intro' => 'Completá los datos de tu empresa y personales para registrarte.',
        'important' => 'Importante:',
        'important_text' => 'Si ya existe una cuenta para tu empresa no debes registrarte aquí sino desde una invitación del administrador de tu empresa.',
        'company_name' => 'Nombre de empresa',
        'company_type' => 'Tipo de empresa',
        'your_name' => 'Tu nombre',
        'email' => 'Email',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
        'placeholder_company_name' => 'Nombre de tu empresa',
        'placeholder_name' => 'Tu nombre',
        'placeholder_email' => 'Email',
        'placeholder_password' => 'Mínimo 8 caracteres',
        'placeholder_password_confirmation' => 'Repetí la contraseña',
        'password_show' => 'Mostrar contraseña',
        'password_hide' => 'Ocultar contraseña',
        'password_generate' => 'Generar contraseña aleatoria',
        'select_type' => 'Seleccionar tipo...',
        'submit' => 'Crear cuenta',
        'or' => 'OR',
        'signup_github' => 'Registrarse con Github',
        'already_have_account' => '¿Ya tenés cuenta?',
        'login' => 'Ingresar',
        'carousel_slide1_title' => 'Gestioná tu negocio con facilidad',
        'carousel_slide1_text' => 'Destacá tu aplicación con una landing de calidad, diseñada y desarrollada por profesionales.',
        'carousel_slide2_title' => 'La mejor forma de mostrar tu app',
        'carousel_slide2_text' => 'Presentá tu producto de forma clara y atractiva para tus clientes.',
        'carousel_slide3_title' => 'Soluciones que convierten leads en clientes',
        'carousel_slide3_text' => 'Herramientas pensadas para potenciar tu operación y tus ventas.',

        'intro_internal' => 'Completá tus datos para unirte a la cuenta de tu empresa.',
        'intro_external' => 'Fuiste invitado a probar la plataforma. Registrá tu empresa y tu usuario.',
        'invitation_from' => 'Te invita: :name (:company)',
        'important_text_internal' => 'Este enlace es personal. Indicá el email que usarás para tu cuenta; no puede estar ya registrado. Después tendrás que confirmarlo por email, igual que en cualquier alta.',
        'important_text_external' => 'Este enlace es personal. Vas a crear una empresa nueva. Indicá el email que usarás; no puede estar ya registrado. Después tendrás que confirmarlo por email.',
        'invited_company' => 'Empresa',
        'invitation_invalid' => 'La invitación no es válida o expiró.',
        'invitation_email_mismatch' => 'El email debe coincidir con la invitación.',
    ],

    'verification' => [
        'title' => 'Confirmar email',
        'heading' => 'Confirmá tu email',
        'intro' => 'Te enviamos un email de bienvenida con un enlace para verificar tu dirección. Hacé clic en ese enlace para activar tu cuenta.',
        'link_sent' => 'Te enviamos un nuevo enlace de verificación a tu email.',
        'no_email' => 'Si no recibiste el email, podés pedir que te reenviemos uno.',
        'resend_button' => 'Reenviar email de verificación',
        'continue' => 'Continuar',
        'email_subject' => 'Bienvenido a :app – Confirmá tu email',
        'email_greeting' => '¡Hola :name!',
        'email_body' => 'Gracias por registrarte. Para activar tu cuenta, confirmá tu dirección de email haciendo clic en el botón de abajo.',
        'email_action' => 'Confirmar email',
        'email_footer' => 'Si no creaste una cuenta, podés ignorar este mensaje.',
        'email_salutation' => 'Saludos, :app',
        'verified' => 'Tu email fue confirmado. Ya podés seguir usando tu cuenta.',
        'already_verified' => 'Tu email ya estaba confirmado.',
    ],

];
