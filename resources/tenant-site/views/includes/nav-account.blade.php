@if (tenant_website_account())
    @auth
        @php
            $accountUser = auth()->user();
            $accountAvatar = method_exists($accountUser, 'avatarThumbUrl')
                ? $accountUser->avatarThumbUrl()
                : site_asset('images/client/16.jpg');
            $accountUserName = trim((string) ($accountUser->name ?? $accountUser->email ?? ''));
        @endphp
        <li class="dropdown inline-block relative ps-0.5">
            <button data-dropdown-toggle="dropdown" class="dropdown-toggle items-center" type="button">
                <span class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center rounded-md border border-red-500 bg-red-500 text-white">
                    <img src="{{ $accountAvatar }}" class="account-avatar rounded-md" alt="{{ $accountUserName }}">
                </span>
            </button>
            <div class="dropdown-menu absolute end-0 m-0 mt-4 z-10 w-48 rounded-md overflow-hidden bg-white dark:bg-slate-900 shadow-sm dark:shadow-gray-800 hidden" onclick="event.stopPropagation();">
                <ul class="py-2 text-start">
                    @if ($accountUserName !== '')
                        <li class="px-4 py-2 font-medium dark:text-white/70">{{ $accountUserName }}</li>
                        <li class="border-t border-gray-100 dark:border-gray-800 my-2"></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center font-medium py-2 px-4 w-full bg-transparent border-0 text-start cursor-pointer dark:text-white/70 hover:text-red-500 dark:hover:text-white">
                                <i data-feather="log-out" class="size-4 me-2"></i>{{ __('tenant.menu_logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </li>
    @else
        <li class="inline-block relative ps-0.5">
            <a href="{{ route('login') }}" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center rounded-md border border-red-500 bg-red-500 text-white" title="{{ __('auth.login.title') }}">
                <i data-feather="user" class="size-4"></i>
            </a>
        </li>
    @endauth
@else
    <li class="dropdown inline-block relative ps-0.5">
        <button data-dropdown-toggle="dropdown" class="dropdown-toggle items-center" type="button">
            <span class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-base text-center rounded-md border border-red-500 bg-red-500 text-white"><img src="{{ site_asset('images/client/16.jpg') }}" class="rounded-md" alt=""></span>
        </button>
        <div class="dropdown-menu absolute end-0 m-0 mt-4 z-10 w-48 rounded-md overflow-hidden bg-white dark:bg-slate-900 shadow-sm dark:shadow-gray-800 hidden" onclick="event.stopPropagation();">
            <ul class="py-2 text-start">
                <li>
                    <a href="{{ url('/user-profile') }}" class="flex items-center font-medium py-2 px-4 dark:text-white/70 hover:text-red-500 dark:hover:text-white"><i data-feather="user" class="size-4 me-2"></i>Profile</a>
                </li>
                <li>
                    <a href="{{ url('/helpcenter') }}" class="flex items-center font-medium py-2 px-4 dark:text-white/70 hover:text-red-500 dark:hover:text-white"><i data-feather="help-circle" class="size-4 me-2"></i>Helpcenter</a>
                </li>
                <li>
                    <a href="{{ url('/user-setting') }}" class="flex items-center font-medium py-2 px-4 dark:text-white/70 hover:text-red-500 dark:hover:text-white"><i data-feather="settings" class="size-4 me-2"></i>Settings</a>
                </li>
                <li class="border-t border-gray-100 dark:border-gray-800 my-2"></li>
                <li>
                    <a href="{{ url('/login') }}" class="flex items-center font-medium py-2 px-4 dark:text-white/70 hover:text-red-500 dark:hover:text-white"><i data-feather="log-out" class="size-4 me-2"></i>Logout</a>
                </li>
            </ul>
        </div>
    </li>
@endif
