@extends('layouts.base', ['title' => $title])

@section('content')
    <div class="d-flex flex-column min-vh-100 bg-white">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container">
                    <div class="d-flex align-items-center">
                        <x-site-logo variant="navbar" class="navbar-brand logo" :height="48" />
                        @include('layouts.partials.test-site-badge')
                    </div>

                    <ul class="navbar-nav align-items-center ms-auto">
                        @include('layouts.partials.language-switcher')
                        @guest
                            <li class="nav-item ms-2">
                                <a class="btn btn-primary btn-sm" href="{{ route('login') }}">{{ __('nav.login') }}</a>
                            </li>
                        @else
                            <li class="nav-item ms-2">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('account.dashboard') }}">{{ __('nav.my_account') }}</a>
                            </li>
                            <li class="nav-item ms-2">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">{{ __('nav.logout') }}</button>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </nav>
        </header>

        <main class="flex-grow-1 d-flex align-items-center">
            <div class="container py-5">
                <div class="row">
                    <div class="col-lg-8">
                        <h1 class="display-5 fw-semibold mb-3">{{ $title }}</h1>
                        <p class="fs-17 text-muted mb-4">{{ __('portal.home_lead') }}</p>
                        @guest
                            <a class="btn btn-primary" href="{{ route('login') }}">{{ __('nav.login') }}</a>
                        @else
                            <a class="btn btn-primary" href="{{ route('account.dashboard') }}">{{ __('nav.my_account') }}</a>
                        @endguest
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-top py-4">
            <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
                <p class="text-muted small mb-0">
                    <script>document.write(new Date().getFullYear())</script>
                    {{ $title }}
                </p>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span>{{ __('portal.powered_by') }}</span>
                    <x-site-logo platform :height="28" :link="false" />
                </div>
            </div>
        </footer>
    </div>
@endsection
