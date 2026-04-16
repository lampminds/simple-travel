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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarLandings" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('nav.landings') }}<i data-feather="chevron-down"
                                       class="d-inline-block icon icon-xxs ms-1 mt-lg-0 mt-1"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg" aria-labelledby="navbarLandings">
                            <div class="row mx-0">
                                <div class="col-md-6 p-lg-0">
                                    <label class="nav-title fw-semibold fs-14 text-dark text-uppercase mb-3">Web</label>
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('second', [ 'landings' , 'app']) }}"
                                               aria-labelledby="navbarLandings">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="bg-soft-primary text-primary avatar avatar-xs shadow rounded icon icon-with-bg icon-xs me-3 flex-shrink-0">
                                                         @svg('/duotone-icons/devices/iPhone-X')
                                                    </span>
                                                    <div class="flex-grow-1">
                                                        App
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item active">
                                            <a class="nav-link mt-1"
                                               href="{{ route('second', [ 'landings' , 'saas']) }}"
                                               aria-labelledby="navbarLandings">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="bg-soft-success avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-success me-3 flex-shrink-0">
                                                         @svg('/duotone-icons/devices/Laptop')
                                                    </span>
                                                    <div class="flex-grow-1">Saas Modern</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mt-1"
                                               href="{{ route('second', [ 'landings' , 'saas2']) }}"
                                               aria-labelledby="navbarLandings">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="bg-soft-info avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-info me-3 flex-shrink-0">
                                                         @svg('/duotone-icons/devices/Display#2')
                                                    </span>
                                                    <div class="flex-grow-1">Saas Classic</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mt-1"
                                               href="{{ route('second', [ 'landings' , 'startup']) }}"
                                               aria-labelledby="navbarLandings">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="bg-soft-orange avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-orange me-3 flex-shrink-0">
                                                         @svg('/duotone-icons/devices/Diagnostics')
                                                    </span>
                                                    <div class="flex-grow-1">Startup</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mt-1"
                                               href="{{ route('second', [ 'landings' , 'software']) }}"
                                               aria-labelledby="navbarLandings">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="bg-soft-warning avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-warning me-3 flex-shrink-0">
                                                         @svg('/duotone-icons/shopping/Box#2')
                                                    </span>
                                                    <div class="flex-grow-1">Software</div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6 p-lg-0">
                                    <label class="nav-title fw-semibold fs-14 text-dark text-uppercase my-3 mt-md-0">Services</label>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <ul class="nav">
                                                <li class="nav-item">
                                                    <a class="nav-link"
                                                       href="{{ route('second', [ 'landings' , 'agency']) }}"
                                                       aria-labelledby="navbarLandings">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="bg-soft-secondary avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-secondary me-3 flex-shrink-0">
                                                                 @svg('/duotone-icons/design/Color-profile')
                                                            </span>
                                                            <div class="flex-grow-1">Agency</div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link mt-1"
                                                       href="{{ route('second', [ 'landings' , 'coworking']) }}"
                                                       aria-labelledby="navbarLandings">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="bg-soft-info avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-info me-3 flex-shrink-0">
                                                                 @svg('/duotone-icons/home/Home')
                                                            </span>
                                                            <div class="flex-grow-1">Coworking</div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link mt-1"
                                                       href="{{ route('second', [ 'landings' , 'crypto']) }}"
                                                       aria-labelledby="navbarLandings">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="bg-soft-orange avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-orange me-3 flex-shrink-0">
                                                                 @svg('/duotone-icons/shopping/Bitcoin')
                                                            </span>
                                                            <div class="flex-grow-1">Crypto</div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link mt-1"
                                                       href="{{ route('second', [ 'landings' , 'marketing']) }}"
                                                       aria-labelledby="navbarLandings">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="bg-soft-primary avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-primary me-3 flex-shrink-0">
                                                                 @svg('/duotone-icons/communication/Urgent-mail')
                                                            </span>
                                                            <div class="flex-grow-1">Marketing</div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link mt-2"
                                                       href="{{ route('second', [ 'landings' , 'portfolio']) }}"
                                                       aria-labelledby="navbarLandings">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="bg-soft-danger avatar avatar-xs shadow rounded icon icon-with-bg icon-xs text-danger me-3 flex-shrink-0">
                                                                 @svg('/duotone-icons/design/Image')
                                                            </span>
                                                            <div class="flex-grow-1">Portfolio</div>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarPages" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('nav.pages') }}<i data-feather="chevron-down"
                                    class="d-inline-block icon icon-xxs ms-1 mt-lg-0 mt-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarPages">
                            <ul class="nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="accountPages" role="button"
                                       data-bs-toggle="dropdown" aria-labelledby="navbarPages" aria-haspopup="true"
                                       aria-expanded="false">
                                        Account
                                        <div class="arrow"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="accountPages">
                                        <ul class="nav">
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'account' , 'dashboard']) }}">Dashboard</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'account' , 'settings']) }}">Settings</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="authPages" role="button"
                                       data-bs-toggle="dropdown" aria-labelledby="navbarPages" aria-haspopup="true"
                                       aria-expanded="false">
                                        Authentication
                                        <div class="arrow"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="authPages">
                                        <ul class="nav">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('second', [ 'auth' , 'login']) }}">Login</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('second', [ 'auth' , 'signup']) }}">SignUp</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'auth' , 'forgot-password']) }}">Forget
                                                    Password</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('second', [ 'auth' , 'confirm']) }}">Confirm
                                                    Account</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="blogPages" role="button"
                                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                       aria-labelledby="navbarPages">
                                        Blog
                                        <div class="arrow"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="blogPages">
                                        <ul class="nav">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('second', [ 'pages' , 'blog']) }}"
                                                   aria-labelledby="blogPages">Blog</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'pages' , 'blog-post']) }}"
                                                   aria-labelledby="blogPages">Blog
                                                    Post</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <hr class="my-2"/>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'pages' , 'company']) }}"
                                       aria-labelledby="navbarPages">Company</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'pages' , 'contact']) }}"
                                       aria-labelledby="navbarPages">Contact</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'pages' , 'career']) }}"
                                       aria-labelledby="navbarPages">Career</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'pages' , 'pricing']) }}"
                                       aria-labelledby="navbarPages">Pricing</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="portfolioPages" role="button"
                                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                       aria-labelledby="navbarPages">
                                        Portfolio
                                        <div class="arrow"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="portfolioPages">
                                        <ul class="nav">
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'pages' , 'portfolio-grid']) }}"
                                                   aria-labelledby="portfolioPages">Portfolio Grid</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'pages' , 'portfolio-masonry']) }}"
                                                   aria-labelledby="portfolioPages">Portfolio Masonry</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link"
                                                   href="{{ route('second', [ 'pages' , 'portfolio-item']) }}"
                                                   aria-labelledby="portfolioPages">Portfolio Item</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <hr class="my-2"/>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'pages' , 'help-desk']) }}"
                                       aria-labelledby="navbarPages">Help</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDocs" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('nav.ui_kit') }}<i data-feather="chevron-down"
                                     class="d-inline-block icon icon-xxs ms-1 mt-lg-0 mt-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDocs">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'ui-kit' , 'colors']) }}"
                                       aria-labelledby="navbarDocs">Colors</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'ui-kit' , 'typography']) }}"
                                       aria-labelledby="navbarDocs">Typography</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'ui-kit' , 'components']) }}"
                                       aria-labelledby="navbarDocs">Components</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'ui-kit' , 'custom']) }}"
                                       aria-labelledby="navbarDocs">Custom</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('second', [ 'ui-kit' , 'plugins']) }}"
                                       aria-labelledby="navbarDocs">Plugins</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                @endunless

                <ul class="navbar-nav align-items-lg-center d-flex ms-auto">
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('template.demos') }}">Template</a>
                    </li>
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
