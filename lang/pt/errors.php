<?php

return [
    '403' => [
        'code' => '403',
        'title' => 'Acesso negado',
        'description' => 'Não tem permissão para ver esta página.',
        'home' => 'Ir para o início',
    ],
    '404' => [
        'code' => '404',
        'title' => 'Página não encontrada',
        'description' => 'Parece que a página que procura não existe.',
        'home' => 'Ir para o início',
    ],
    '500' => [
        'code' => '500',
        'title' => 'Algo correu mal',
        'description' => 'Estamos a trabalhar para resolver o problema. Por favor, tente novamente dentro de alguns minutos.',
        'home' => 'Ir para o início',
    ],
    '503' => [
        'code' => '503',
        'title' => 'Serviço indisponível',
        'description' => 'Estamos temporariamente indisponíveis. Por favor, tente novamente em breve.',
        'home' => 'Ir para o início',
    ],

    'invitation' => [
        'code' => 'Convite',
        'title' => 'Este link de convite não pode ser utilizado',
        'description_expired' => 'Este link expirou. Peça ao administrador da sua empresa que lhe envie um novo convite.',
        'description_revoked' => 'Este convite foi revogado e o link deixou de funcionar. Contacte o administrador da sua empresa se ainda precisar de acesso.',
        'description_declined' => 'Este convite já não está ativo.',
        'description_accepted' => 'Este convite já foi utilizado. Inicie sessão com a conta que criou ou peça um novo convite se precisar de ajuda.',
        'description_invalid' => 'Este link de convite não é válido. Verifique o endereço ou peça um novo convite ao administrador.',
        'home' => 'Ir para o início',
    ],
];
