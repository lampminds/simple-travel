@extends('site::layouts.no-header')

@php
    $authAccount = tenant_website_account();
    $authBackground = $authAccount?->homeBackgroundUrl() ?: site_asset('images/bg/6.jpg');
    $authLogo = $authAccount?->portalLogoOnWhitePath() ?: $authAccount?->portalLogoPath();
    $authName = trim((string) ($authAccount?->commercial_name ?: $authAccount?->name ?: $authAccount?->nick ?: config('app.name')));
@endphp

@section('meta_title')
    @yield('page_title', $authName)
@endsection

@section('content')
    <section class="md:h-screen py-36 flex items-center relative overflow-hidden zoom-image">
        <div class="absolute inset-0 image-wrap z-1 bg-no-repeat bg-center bg-cover" style="background-image: url('{{ $authBackground }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900 z-2" id="particles-snow"></div>
        <div class="container relative z-3">
            <div class="flex justify-center">
                <div class="max-w-[400px] w-full m-auto p-6 bg-white dark:bg-slate-900 shadow-md dark:shadow-gray-700 rounded-md">
                    <a href="{{ url('/') }}" class="block text-center">
                        @if ($authLogo)
                            <img src="{{ $authLogo }}" class="mx-auto" style="height: 64px; width: auto; object-fit: contain;" alt="{{ $authName }}">
                        @else
                            <span class="text-xl font-semibold">{{ $authName }}</span>
                        @endif
                    </a>
                    <h5 class="my-6 text-xl font-semibold">@yield('auth_heading')</h5>
                    @yield('auth_body')
                </div>
            </div>
        </div>
    </section>
@endsection
