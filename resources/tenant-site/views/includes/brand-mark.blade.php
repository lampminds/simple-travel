@php
    $siteAccount = request()->attributes->get('tenant_website_account');
    $siteAccount = $siteAccount instanceof \App\Models\Account ? $siteAccount : null;
    $logoPath = $siteAccount?->portalLogoPath();
    $logoName = trim((string) ($siteAccount?->commercial_name ?: $siteAccount?->name ?: $siteAccount?->nick ?: ''));
@endphp
@if ($logoPath)
    <img src="{{ $logoPath }}" class="tenant-logo inline-block object-contain align-middle" alt="{{ $logoName }}">
@elseif ($logoName !== '')
    <span class="tenant-logo-name font-semibold align-middle">{{ $logoName }}</span>
@else
    <span class="inline-block dark:hidden">
        <img src="{{ site_asset('images/logo-dark.png') }}" class="h-7 l-dark" alt="">
        <img src="{{ site_asset('images/logo-light.png') }}" class="h-7 l-light" alt="">
    </span>
    <img src="{{ site_asset('images/logo-white.png') }}" class="hidden dark:inline-block" alt="">
@endif
