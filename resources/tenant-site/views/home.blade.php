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

    <section class="relative md:py-24 py-16 overflow-hidden">
        <div class="container relative">
            <div class="grid grid-cols-1 pb-8 text-center">
                <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Top Destinations</h3>
                <p class="text-slate-400 max-w-xl mx-auto">{{ $subtitle ?? __('tenant.hero_lead') }}</p>
            </div>

            <div class="grid lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 relative mt-6 gap-6">
                @include('site::includes.Hero.top-destinations2')
            </div>
        </div>

        <div class="container relative md:mt-24 mt-16">
            @include('site::includes.Hero.about')
        </div>

        <div class="container relative md:mt-24 mt-16">
            <div class="grid grid-cols-1 pb-8 text-center">
                <h3 class="mb-6 md:text-3xl text-2xl md:leading-normal leading-normal font-semibold">Tours Packages</h3>
                <p class="text-slate-400 max-w-xl mx-auto">{{ $subtitle ?? __('tenant.hero_lead') }}</p>
            </div>

            <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 mt-6 gap-6">
                @include('site::includes.Hero.tours-packages3')
            </div>
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
