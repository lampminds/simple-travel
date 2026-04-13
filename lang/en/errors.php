<?php

return [
    '403' => [
        'code' => '403',
        'title' => 'Access denied',
        'description' => 'You do not have permission to view this page.',
        'home' => 'Go to Homepage',
    ],
    '404' => [
        'code' => '404',
        'title' => 'Page Not Found',
        'description' => 'Looks like the page you\'re looking for doesn\'t exist.',
        'home' => 'Go to Homepage',
    ],
    '500' => [
        'code' => '500',
        'title' => 'Something went wrong',
        'description' => 'We are working to fix the problem. Please try again in a few minutes.',
        'home' => 'Go to Homepage',
    ],
    '503' => [
        'code' => '503',
        'title' => 'Service unavailable',
        'description' => 'We are temporarily unavailable. Please try again shortly.',
        'home' => 'Go to Homepage',
    ],

    'invitation' => [
        'code' => 'Invitation',
        'title' => 'This invitation link cannot be used',
        'description_expired' => 'This link has expired. Ask your company administrator to send you a new invitation.',
        'description_revoked' => 'This invitation was revoked and the link no longer works. Contact your company administrator if you still need access.',
        'description_declined' => 'This invitation is no longer active.',
        'description_accepted' => 'This invitation was already used. Sign in with the account you created, or ask for a new invitation if you need help.',
        'description_invalid' => 'This invitation link is not valid. Check the address or request a new invitation from your administrator.',
        'home' => 'Go to Homepage',
    ],
];
