<header>
    <nav class="navbar navbar-expand-lg topnav-menu {{$classList}} {{$sticky === true ? 'sticky' : ''}}">
        <div class="container {{$fixedWidth !== true ? '-fluid' : ''}}">
            <x-site-logo class="navbar-brand me-lg-3 me-auto" />

            <a href="#" class="navbar-toggler me-3" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content4"
               aria-controls="topnav-menu-content4" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </a>

            <div class="collapse navbar-collapse" id="topnav-menu-content4">
                <ul class="navbar-nav mx-auto website-center-nav">
                    @php
                        $websiteNavRoots = auth()->check()
                            ? \App\Support\WebsiteNavigation::navbarMenuRoots()
                            : null;
                        $showDynamicMenu = $websiteNavRoots !== null && $websiteNavRoots->isNotEmpty();

                        $currentAccount = auth()->user()?->currentAccount();
                        $publicWebsiteUrl = $currentAccount
                            ? \App\Support\TenantWebsiteHostParser::publicWebsiteUrlForNick($currentAccount->nick)
                            : null;
                        $hasAccountTypes = count(\App\Support\CurrentAccountSession::typeIds(request())) > 0;
                    @endphp
                    @if(! $showDynamicMenu)
                        <li class="nav-item pe-3">
                            <a class="nav-link" href="{{ route('second', [ 'account' , 'dashboard']) }}">
                                <div class="d-flex align-items-center">
                                    <span class="icon-xs me-1 flex-shrink-0">
                                        @svg('/duotone-icons/layout/Layout-4-blocks')
                                    </span>
                                    <div class="flex-grow-1">Home</div>
                                </div>
                            </a>
                        </li>
                        @if($publicWebsiteUrl && $hasAccountTypes)
                            <li class="nav-item pe-3">
                                <a class="nav-link" href="{{ $publicWebsiteUrl }}" target="_blank" rel="noopener noreferrer">
                                    <div class="d-flex align-items-center">
                                        <span class="icon-xs me-1 flex-shrink-0">
                                            @svg('/duotone-icons/home/Globe')
                                        </span>
                                        <div class="flex-grow-1">{{ __('account.nav_public_website') }}</div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endif
                    @if($showDynamicMenu)
                        @foreach($websiteNavRoots as $navMenu)
                            @include('layouts.partials.website-nav-menu-node', ['menu' => $navMenu, 'level' => 0])
                        @endforeach
                    @endif
                    @if(auth()->user()?->hasRole('admin'))
                        <li class="nav-item pe-3">
                            <a class="nav-link" href="{{ url('/smpl_adm') }}">
                                <div class="d-flex align-items-center">
                                    <span class="icon-xs me-1 flex-shrink-0">
                                        @svg('/duotone-icons/devices/Laptop')
                                    </span>
                                    <div class="flex-grow-1">Admin</div>
                                </div>
                            </a>
                        </li>
                    @endif
                </ul>

                <ul class="navbar-nav align-items-center">
                    <!-- notification start -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notifications" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="icon-xs">
                                @svg('/duotone-icons/general/Notification#2')
                            </span>
                        </a>
                        <div class="dropdown-menu p-2" aria-labelledby="notifications">
                            <!-- notification item start -->
                            <a class="dropdown-item p-2" href="#">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="bg-soft-primary avatar avatar-xs rounded icon icon-with-bg icon-xxs text-primary me-3 flex-shrink-0">
                                        @svg('/duotone-icons/communication/Add-user')
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-medium my-0 fs-13">New User Registered</h6>
                                        <span class="text-muted"><small>2 min ago</small></span>
                                    </div>
                                </div>
                            </a>
                            <!-- notification item end -->

                            <!-- notification item start -->
                            <a class="dropdown-item p-2" href="#">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="bg-soft-orange avatar avatar-xs rounded icon icon-with-bg icon-xxs text-orange me-3 flex-shrink-0">
                                        @svg('/duotone-icons/communication/Chat-check')
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-medium my-0 fs-13">A new comment on your post</h6>
                                        <span class="text-muted"><small>3 min ago</small></span>
                                    </div>
                                </div>
                            </a>
                            <!-- notification item end -->

                            <!-- notification item start -->
                            <a class="dropdown-item p-2" href="#">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="bg-soft-success avatar avatar-xs rounded icon icon-with-bg icon-xxs text-success me-3 flex-shrink-0">
                                        @svg('/duotone-icons/communication/Mail-attachment')
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-medium my-0 fs-13">A new message from</h6>
                                        <span class="text-muted"><small>10 min ago</small></span>
                                    </div>
                                </div>
                            </a>
                            <!-- notification item end -->

                            <!-- notification item start -->
                            <a class="dropdown-item p-2" href="#">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="bg-soft-danger avatar avatar-xs rounded icon icon-with-bg icon-xxs text-danger me-3 flex-shrink-0">
                                        @svg('/duotone-icons/general/Like')
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-medium my-0 fs-13">A new like on your comment</h6>
                                        <span class="text-muted"><small>14 min ago</small></span>
                                    </div>
                                </div>
                            </a>
                            <!-- notification item end -->

                            <!-- view all start -->
                            <a href="#" class="mt-2 text-center bg-light fs-13 btn btn-light btn-sm d-block">View
                                All</a>
                            <!-- view all end -->
                        </div>
                    </li>
                    <!-- notification end -->

                    <!-- profile dropdown start -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle py-0" href="#" id="user" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="{{ auth()->user()->avatarThumbUrl() }}"
                                         class="avatar avatar-xs rounded-circle me-2" alt="{{ auth()->user()->name }}"/>
                                </div>
                                <div class="flex-grow-1 ms-1 lh-base">
                                    <span class="fw-semibold fs-13 d-block line-height-normal">{{ auth()->user()->name }}</span>
                                    <span class="text-muted fs-13">{{ auth()->user()->roleNamesForCurrentAccount()->first() ?? __('profile.menu_subtitle') }}</span>
                                    @if($currentAccount)
                                        <span class="text-muted fs-12 d-block">
                                            {{ \Illuminate\Support\Str::limit($currentAccount->commercial_name ?? $currentAccount->name ?? $currentAccount->nick ?? '', 12, '') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="user">
                            @php
                                $switchAccounts = auth()->user()->switchableAccounts();
                                $currentAccountId = auth()->user()->currentAccountId();
                                $currentLocale = app()->getLocale();
                            @endphp
                            @if($switchAccounts->count() > 1)
                                <h6 class="dropdown-header px-2 py-1 fs-12 text-muted mb-0">{{ __('account.switch_heading') }}</h6>
                                @foreach($switchAccounts as $acc)
                                    <form method="POST" action="{{ route('account.switch') }}" class="w-100">
                                        @csrf
                                        <input type="hidden" name="account_id" value="{{ $acc->id }}">
                                        <input type="hidden" name="redirect_to" value="{{ route('account.dashboard', absolute: false) }}">
                                        <button type="submit"
                                                class="dropdown-item p-2 border-0 bg-transparent w-100 text-start @if((int) $currentAccountId === (int) $acc->id) active @endif">
                                            <i class="icon icon-xxs me-1 icon-dual" data-feather="briefcase"></i>
                                            {{ $acc->commercial_name ?? $acc->name ?? $acc->nick }}
                                        </button>
                                    </form>
                                @endforeach
                                <div class="dropdown-divider"></div>
                            @endif
                            <!-- item start -->
                            <a class="dropdown-item p-2" href="{{ route('account.profile.edit') }}">
                                <i class="icon icon-xxs me-1 icon-dual" data-feather="user"></i>
                                {{ __('profile.menu_profile') }}
                            </a>
                            <!-- item end -->

                            @if(auth()->user()->hasRoleForCurrentAccount('owner'))
                                <!-- item start -->
                                <a class="dropdown-item p-2" href="{{ route('account.company.edit') }}">
                                    <i class="icon icon-xxs me-1 icon-dual" data-feather="briefcase"></i>
                                    Empresa
                                </a>
                                <!-- item end -->

                                <!-- item start -->
                                <a class="dropdown-item p-2" href="{{ route('account.invitations.index') }}">
                                    <i class="icon icon-xxs me-1 icon-dual" data-feather="mail"></i>
                                    {{ __('invitations.menu') }}
                                </a>
                                <!-- item end -->
                            @endif

                            <!-- item start -->
                            <a class="dropdown-item p-2" href="{{ route('second', ['account', 'settings']) }}">
                                <i class="icon icon-xxs me-1 icon-dual" data-feather="settings"></i>
                                Configuración
                            </a>
                            <!-- item end -->

                            <!-- item start -->
                            <a class="dropdown-item p-2" href="#">
                                <i class="icon icon-xxs me-1 icon-dual" data-feather="aperture"></i>
                                Support
                            </a>
                            <!-- item end -->

                            @if(isset($languages) && $languages->isNotEmpty())
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header px-2 py-1 fs-12 text-muted mb-0">{{ __('nav.language') }}</h6>
                                @foreach($languages as $lang)
                                    @php
                                        $isActiveLanguage = \App\Models\Locale::primaryTagMatches($lang->locale, $currentLocale);
                                        $flag = $lang->locale?->flagEmoji() ?? '🌐';
                                    @endphp
                                    <a class="dropdown-item p-2 d-flex align-items-center {{ $isActiveLanguage ? 'active' : '' }}"
                                       href="{{ route('locale', ['language' => $lang->id]) }}">
                                        <span class="me-2" aria-hidden="true">{{ $flag }}</span>
                                        <span>{{ $lang->display_name }}</span>
                                    </a>
                                @endforeach
                            @endif

                            <div class="dropdown-divider"></div>

                            <!-- item start -->
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item p-2 border-0 bg-transparent w-100 text-start">
                                    <i class="icon icon-xxs me-1 icon-dual" data-feather="log-out"></i>
                                    {{ __('profile.sign_out') }}
                                </button>
                            </form>
                            <!-- item end -->
                        </div>
                    </li>
                    <!-- profile dropdown end -->
                </ul>
            </div>
        </div>
    </nav>
</header>
