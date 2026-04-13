<?php

return [
    '403' => [
        'code' => '403',
        'title' => 'Acceso denegado',
        'description' => 'No tienes permiso para ver esta página.',
        'home' => 'Ir al inicio',
    ],
    '404' => [
        'code' => '404',
        'title' => 'Página no encontrada',
        'description' => 'Parece que la página que buscas no existe.',
        'home' => 'Ir al inicio',
    ],
    '500' => [
        'code' => '500',
        'title' => 'Algo salió mal',
        'description' => 'Estamos trabajando para solucionarlo. Por favor, inténtalo de nuevo en unos minutos.',
        'home' => 'Ir al inicio',
    ],
    '503' => [
        'code' => '503',
        'title' => 'Servicio no disponible',
        'description' => 'No estamos disponibles temporalmente. Por favor, inténtalo de nuevo en breve.',
        'home' => 'Ir al inicio',
    ],

    'invitation' => [
        'code' => 'Invitación',
        'title' => 'Este enlace de invitación no se puede usar',
        'description_expired' => 'Este enlace caducó. Pedí a quien administra tu empresa que te envíe una invitación nueva.',
        'description_revoked' => 'Esta invitación fue revocada y el enlace ya no sirve. Contactá al administrador de tu empresa si seguís necesitando acceso.',
        'description_declined' => 'Esta invitación ya no está activa.',
        'description_accepted' => 'Esta invitación ya fue utilizada. Iniciá sesión con la cuenta que creaste o pedí una nueva invitación si necesitás ayuda.',
        'description_invalid' => 'Este enlace de invitación no es válido. Revisá la dirección o pedí una nueva invitación a tu administrador.',
        'home' => 'Ir al inicio',
    ],
];
