{{-- Logo: change default height here to apply site-wide (navbar, footer, auth). Override per usage with :height="60" etc. --}}
@props([
    'height' => 60,
    'variant' => 'single',
    'url' => null,
    'link' => true,
    'class' => '',
])

@php
    $url = $url ?? route('home');
    $linkClass = trim('d-inline-flex align-items-center ' . $class);
@endphp

@if($link)
    <a href="{{ $url }}" class="{{ $linkClass }}" {{ $attributes->except(['height', 'variant', 'url', 'link', 'class']) }}>
        @if($variant === 'navbar')
            <img src="/images/logo.png" height="{{ $height }}" class="align-top logo-dark" alt=""/>
            <img src="/images/logo-light.png" height="{{ $height }}" class="align-top logo-light" alt=""/>
        @else
            <img src="/images/logo.png" height="{{ $height }}" class="align-top d-inline-block" alt=""/>
        @endif
    </a>
@else
    <span class="{{ $linkClass }}" {{ $attributes->except(['height', 'variant', 'url', 'link', 'class']) }}>
        <img src="/images/logo.png" height="{{ $height }}" class="align-top d-inline-block" alt=""/>
    </span>
@endif
