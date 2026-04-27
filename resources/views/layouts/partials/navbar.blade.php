<!-- Navbar Start -->
<header>
    <nav class="navbar navbar-expand-lg topnav-menu z-10 {{$topbarColor}} {{$sticky === true ? 'sticky' : ''}}">
        <div class="container {{$fixedWidth !== true ? '-fluid' : ''}}">
            <x-site-logo variant="navbar" class="navbar-brand logo" />
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content"
                    aria-controls="topnav-menu-content" aria-expanded="false" aria-label="{{ __('nav.toggle_nav') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                @if($hideSearch !== true)
                    <ul class="navbar-nav align-items-lg-center d-flex me-auto">
                        <li>
                            <form action="#" id="search" class="form-inline d-none d-sm-flex">
                                <div class="form-control-with-hint ms-lg-2 ms-xl-4">
                                    <input type="text" class="form-control" id="search-input"
                                           placeholder="{{ __('nav.search_placeholder') }}"/>
                                    <span class="form-control-feedback uil uil-search fs-16"></span>
                                </div>
                            </form>
                        </li>
                    </ul>
                @endif

                @unless (request()->routeIs('home'))
                <ul class="navbar-nav align-items-lg-center {{$classList}}">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">{{ __('nav.home') }}</a>
                    </li>
                </ul>
                @endunless

                <ul class="navbar-nav align-items-lg-center d-flex ms-auto">
                    @if (is_dev_server())
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('template.demos') }}">Template</a>
                    </li>
                    @endif
                    @include('layouts.partials.language-switcher')
                    @guest
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">{{ __('nav.login') }}</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary btn-sm" href="{{ route('register') }}">{{ __('nav.register') }}</a>
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
        </div>
    </nav>
</header>
<!-- Navbar End -->
