{{-- Logo: change default height here to apply site-wide (navbar, footer, auth). Override per usage with :height="60" etc. --}}
{{-- Pass platform to keep the Simple Travel mark on an operator portal (footer credit). --}}
@props([
    'height' => 60,
    'variant' => 'single',
    'url' => null,
    'link' => true,
    'platform' => false,
    'class' => '',
])

@php
    $url = $url ?? route('home');
    $linkClass = trim('d-inline-flex align-items-center ' . $class);
    $portalAccount = $platform ? null : operator_portal_account();
    $portalLogo = $portalAccount?->portalLogoPath();
    $portalName = $portalAccount?->commercial_name ?? $portalAccount?->name ?? $portalAccount?->nick;
@endphp

@if($link)
    <a href="{{ $url }}" class="{{ $linkClass }}" {{ $attributes->except(['height', 'variant', 'url', 'link', 'platform', 'class']) }}>
        @if($portalAccount)
            @if($portalLogo)
                <img src="{{ $portalLogo }}" height="{{ $height }}" class="align-top d-inline-block" alt="{{ $portalName }}"/>
            @else
                <span class="fw-semibold text-dark">{{ $portalName }}</span>
            @endif
        @elseif($variant === 'navbar')
            <img src="/images/logo.png" height="{{ $height }}" class="align-top logo-dark" alt=""/>
            <img src="/images/logo-light.png" height="{{ $height }}" class="align-top logo-light" alt=""/>
        @else
            <img src="/images/logo.png" height="{{ $height }}" class="align-top d-inline-block" alt=""/>
        @endif
    </a>
@else
    <span class="{{ $linkClass }}" {{ $attributes->except(['height', 'variant', 'url', 'link', 'platform', 'class']) }}>
        @if($portalAccount)
            @if($portalLogo)
                <img src="{{ $portalLogo }}" height="{{ $height }}" class="align-top d-inline-block" alt="{{ $portalName }}"/>
            @else
                <span class="fw-semibold text-dark">{{ $portalName }}</span>
            @endif
        @else
            <img src="/images/logo.png" height="{{ $height }}" class="align-top d-inline-block" alt=""/>
        @endif
    </span>
@endif
