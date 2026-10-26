@php
$navs = [
    [
        'icon' => 'airplay',
        'title' => 'Profile',
        'link' => url('/user-profile'),
    ],
    [
        'icon' => 'edit',
        'title' => 'Billing',
        'link' => url('/user-billing'),
    ],
    [
        'icon' => 'credit-card',
        'title' => 'Payment',
        'link' => url('/user-payment'),
    ],
    [
        'icon' => 'file-text',
        'title' => 'Invoice',
        'link' => url('/user-invoice'),
    ],
    [
        'icon' => 'share-2',
        'title' => 'Social Profile',
        'link' => url('/user-social'),
    ],
    [
        'icon' => 'bell',
        'title' => 'Notifications',
        'link' => url('/user-notification'),
    ],
    [
        'icon' => 'settings',
        'title' => 'Settings',
        'link' => url('/user-setting'),
    ],
    [
        'icon' => 'log-out',
        'title' => 'Sign Out',
        'link' => url('/lock-screen'),
    ]
];
@endphp

@foreach ($navs as $item)
    <li class="navbar-item account-menu">
        <a href="{{ $item['link'] }}" class="navbar-link text-slate-400 flex items-center py-2 rounded">
            <span class="me-2 mb-0"><i data-feather="{{ $item['icon'] }}" class="size-4"></i></span>
            <h6 class="mb-0 font-medium">{{ $item['title'] }}</h6>
        </a>
    </li>
@endforeach