@extends('site::layouts.main')

@php
    $heroBackground = $account->homeBackgroundUrl();
@endphp

@section('meta_title', $title ?? $account->nick)

@section('content')
    @include('site::includes.navbar')

    {{-- Hero matches the purchased index-three layout: title and search sit together over the photo. --}}
    <section class="relative md:pt-48 md:pb-36 py-36 table w-full items-center bg-center bg-cover" id="home"
        @if ($heroBackground) style="background-image: url('{{ $heroBackground }}');" @endif>
        <div class="absolute inset-0 bg-slate-900/40"></div>
        <div class="container relative">
            <div class="grid grid-cols-1">
                <div class="text-center">
                    <h1 class="font-bold text-white lg:leading-normal leading-normal text-4xl lg:text-6xl mb-6 mt-5">{{ $title ?? $account->nick }}</h1>
                    <p class="text-white/70 text-xl max-w-xl mx-auto">{{ $subtitle ?? __('tenant.hero_lead') }}</p>

                    <div class="mt-6">
                        <form method="get" action="{{ url('/') }}" class="p-4 bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-gray-700">
                            <div class="registration-form text-slate-900 text-start">
                                <div class="grid lg:grid-cols-5 md:grid-cols-2 grid-cols-1 gap-4">
                                    <div>
                                        <label class="form-label font-medium text-slate-900 dark:text-white" for="search-keyword">{{ __('tenant.search_label') }}:</label>
                                        <div class="relative mt-2">
                                            <i data-feather="search" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                            <input name="q" type="text" id="search-keyword" value="{{ request('q') }}" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" placeholder="{{ __('tenant.search_placeholder') }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-medium text-slate-900 dark:text-white" for="search-date-from">{{ __('tenant.search_date_from') }}:</label>
                                        <div class="relative mt-2">
                                            <i data-feather="calendar" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                            <input name="date_from" type="text" id="search-date-from" value="{{ request('date_from') }}" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 start" placeholder="{{ __('tenant.search_date_placeholder') }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-medium text-slate-900 dark:text-white" for="search-date-to">{{ __('tenant.search_date_to') }}:</label>
                                        <div class="relative mt-2">
                                            <i data-feather="calendar" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                            <input name="date_to" type="text" id="search-date-to" value="{{ request('date_to') }}" class="w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0 end" placeholder="{{ __('tenant.search_date_placeholder') }}">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-medium text-slate-900 dark:text-white" for="search-persons">{{ __('tenant.search_persons') }}:</label>
                                        <div class="relative mt-2">
                                            <i data-feather="users" class="size-[18px] absolute top-[10px] start-3 text-slate-400"></i>
                                            <select name="persons" id="search-persons" class="form-select w-full py-2 px-3 ps-10 h-10 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-md outline-none border border-gray-100 dark:border-gray-800 focus:ring-0">
                                                <option value="" @selected(request('persons') === null || request('persons') === '')>{{ __('tenant.search_persons_placeholder') }}</option>
                                                @foreach ([1, 2, 3, 4, 5] as $personCount)
                                                    <option value="{{ $personCount }}" @selected((string) request('persons') === (string) $personCount)>{{ $personCount }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="lg:mt-[35px]">
                                        <input type="submit" id="search-buy" name="search" class="py-1 px-5 h-10 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full cursor-pointer" value="{{ __('tenant.search_submit') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative md:py-24 py-16 overflow-hidden" id="paquetes">
        <div class="container relative">
            <div class="grid grid-cols-1 pb-8 text-center">
                <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">{{ __('tenant.packages_heading') }}</h3>
                <p class="text-slate-400 max-w-xl mx-auto">{{ __('tenant.packages_lead') }}</p>
            </div>

            @if (($packages ?? []) === [])
                <p class="text-center text-slate-400">{{ __('tenant.packages_empty') }}</p>
            @else
                <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 mt-6 gap-6">
                    @foreach ($packages as $package)
                        <div class="group rounded-md shadow-sm dark:shadow-gray-700">
                            <div class="relative overflow-hidden rounded-md shadow-sm dark:shadow-gray-700 mx-2 mt-2 h-44 bg-slate-100 dark:bg-slate-800">
                                @if (! empty($package['cover_url']))
                                    <img src="{{ $package['cover_url'] }}" class="size-full object-cover scale-125 group-hover:scale-100 duration-500" alt="{{ $package['title'] }}">
                                @else
                                    <div class="size-full flex items-center justify-center text-slate-300">
                                        <i data-feather="image" class="size-8"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="p-3">
                                @if (($package['location'] ?? '') !== '')
                                    <p class="flex items-center text-slate-400 font-medium mb-2"><i data-feather="map-pin" class="text-red-500 size-4 me-1"></i> {{ $package['location'] }}</p>
                                @endif
                                <a href="{{ $package['url'] }}" class="text-lg font-medium hover:text-red-500 duration-500 ease-in-out">{{ $package['title'] }}</a>

                                <div class="mt-3 pt-3 flex justify-between items-end gap-3 border-t border-slate-100 dark:border-gray-800">
                                    <div>
                                        @forelse ($package['prices'] as $price)
                                            <h5 class="text-lg font-medium text-red-500">{{ $price }}</h5>
                                        @empty
                                            <span class="text-slate-400">{{ __('tenant.package_price_on_request') }}</span>
                                            @if ($package['show_login'] ?? false)
                                                <a href="{{ $package['login_url'] }}" class="mt-2 py-1 px-3 inline-block tracking-wide align-middle duration-500 text-sm text-center bg-red-500 text-white rounded-md">{{ __('tenant.package_login') }}</a>
                                            @endif
                                        @endforelse
                                    </div>

                                    <a href="{{ $package['url'] }}" class="text-slate-400 hover:text-red-500 shrink-0">{{ __('tenant.packages_details') }} <i class="mdi mdi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="container relative md:mt-24 mt-16">
            <div class="grid grid-cols-1 pb-6 text-center">
                <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">What Our Users Say</h3>
                <p class="text-slate-400 max-w-xl mx-auto">{{ $subtitle ?? __('tenant.hero_lead') }}</p>
            </div>

            <div class="grid grid-cols-1 mt-6">
                <div class="tiny-three-item">
                    @include('site::includes.Hero.reviews')
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid relative">
        <div class="grid grid-cols-1 relative">
            <div class="tiny-twelve-item">
                @include('site::includes.Hero.insta-post')
            </div>
        </div>
    </div>

    @if (filled($about ?? null))
        <section class="relative md:py-24 py-16 overflow-hidden">
            <div class="container relative">
                @include('site::includes.Hero.about')
            </div>
        </section>
    @endif
@endsection

@if (! $heroBackground)
    @push('scripts')
        <script>
            easy_background("#home",
                {
                    slide: ["{{ site_asset('images/bg/4.jpg') }}", "{{ site_asset('images/bg/5.jpg') }}", "{{ site_asset('images/bg/6.jpg') }}"],
                    delay: [4000, 4000, 4000],
                }
            );
        </script>
    @endpush
@endif
