@extends('site::layouts.main')

@section('meta_title', $package['title'] ?? $account->nick)

@section('content')
    @include('site::includes.navbar')

    <section class="relative table w-full items-center md:pt-48 md:pb-36 py-36 bg-top bg-no-repeat bg-cover" style="background-image: url('{{ $account->homeBackgroundUrl() ?: site_asset('images/bg/cta.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
        <div class="container relative">
            <div class="grid grid-cols-1 text-center mt-10">
                @if (($package['location'] ?? '') !== '')
                    <p class="text-white/70 mb-3 inline-flex items-center justify-center">
                        <i data-feather="map-pin" class="size-4 me-1"></i>
                        {{ $package['location'] }}
                    </p>
                @endif
                <h3 class="text-4xl leading-normal tracking-wider font-semibold text-white">{{ $package['title'] }}</h3>
            </div>
        </div>

        <div class="absolute text-center z-10 bottom-5 start-0 end-0 mx-3">
            <ul class="tracking-[0.5px] mb-0 inline-block">
                <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white/50 hover:text-white">
                    <a href="{{ url('/') }}">{{ __('tenant.package_back') }}</a>
                </li>
                <li class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"><i class="mdi mdi-chevron-right"></i></li>
                <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white/50 hover:text-white">
                    <a href="{{ url('/#paquetes') }}">{{ __('tenant.packages_heading') }}</a>
                </li>
                <li class="inline-block text-base text-white/50 mx-0.5 ltr:rotate-0 rtl:rotate-180"><i class="mdi mdi-chevron-right"></i></li>
                <li class="inline-block uppercase text-[13px] font-bold duration-500 ease-in-out text-white" aria-current="page">{{ $package['title'] }}</li>
            </ul>
        </div>
    </section>

    <section class="relative md:py-24 py-16">
        <div class="container relative">
            <div class="grid lg:grid-cols-12 grid-cols-1 gap-6">
                <div class="lg:col-span-8">
                    @if (($package['description'] ?? '') !== '')
                        <p class="text-slate-400 whitespace-pre-line mb-8">{{ $package['description'] }}</p>
                    @endif

                    @if (($package['hero_images'] ?? []) !== [])
                        <div class="grid md:grid-cols-2 grid-cols-1 gap-4 mb-10">
                            @foreach ($package['hero_images'] as $image)
                                @if (blank($image['url'] ?? ''))
                                    @continue
                                @endif
                                <a href="{{ $image['url'] }}" class="lightbox block overflow-hidden rounded-md">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['label'] ?? $package['title'] }}" class="w-full h-56 object-cover hover:scale-105 duration-500">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <h4 class="text-xl font-semibold mb-4">{{ __('tenant.package_composition') }}</h4>
                    @if (($package['items'] ?? []) === [])
                        <p class="text-slate-400 mb-10">{{ __('tenant.package_no_items') }}</p>
                    @else
                        <div class="overflow-x-auto mb-10 rounded-md border border-slate-100 dark:border-gray-800">
                            <table class="w-full text-start text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3 font-medium">{{ __('tenant.package_col_service') }}</th>
                                        <th class="px-4 py-3 font-medium text-center">{{ __('tenant.package_col_quantity') }}</th>
                                        <th class="px-4 py-3 font-medium">{{ __('tenant.package_col_inclusion') }}</th>
                                        <th class="px-4 py-3 font-medium">{{ __('tenant.package_col_notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($package['items'] as $item)
                                        <tr class="border-t border-slate-100 dark:border-gray-800 align-top">
                                            <td class="px-4 py-3">
                                                <span class="font-medium">{{ $item['service_name'] ?? '—' }}</span>
                                                @if (($item['variant_sku'] ?? '') !== '')
                                                    <span class="block text-slate-400 text-xs mt-1">{{ $item['variant_sku'] }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">{{ $item['quantity'] ?? 1 }}</td>
                                            <td class="px-4 py-3 text-slate-400">{{ $item['inclusion_mode_label'] ?? ($item['inclusion_mode'] ?? '—') }}</td>
                                            <td class="px-4 py-3 text-slate-400 whitespace-pre-line">{{ ($item['notes'] ?? '') !== '' ? $item['notes'] : '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if (($package['feature_groups'] ?? []) !== [])
                        <h4 class="text-xl font-semibold mb-4">{{ __('tenant.package_features') }}</h4>
                        <div class="space-y-6 mb-10">
                            @foreach ($package['feature_groups'] as $group)
                                <div>
                                    @if (($group['title'] ?? '') !== '')
                                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-2">{{ $group['title'] }}</p>
                                    @endif
                                    @if (($group['items'] ?? []) !== [])
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($group['items'] as $feature)
                                                <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-500 text-sm">{{ $feature }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (($group['excluded_items'] ?? []) !== [])
                                        <p class="text-xs uppercase tracking-wide text-slate-400 mt-3 mb-2">{{ __('tenant.package_features_excluded') }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($group['excluded_items'] as $feature)
                                                <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 text-sm">{{ $feature }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (($package['conditions'] ?? []) !== [])
                        <h4 class="text-xl font-semibold mb-4">{{ __('tenant.package_conditions') }}</h4>
                        <ul class="space-y-3 mb-10">
                            @foreach ($package['conditions'] as $condition)
                                <li>
                                    <span class="font-medium">{{ $condition['label'] ?? '—' }}:</span>
                                    <span class="text-slate-400 whitespace-pre-line">{{ $condition['text'] ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @foreach ($package['galleries'] ?? [] as $gallery)
                        @if (($gallery['images'] ?? []) === [])
                            @continue
                        @endif
                        <h4 class="text-xl font-semibold mb-4">{{ ($gallery['title'] ?? '') !== '' ? $gallery['title'] : __('tenant.package_gallery') }}</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
                            @foreach ($gallery['images'] as $image)
                                @if (blank($image['url'] ?? ''))
                                    @continue
                                @endif
                                <a href="{{ $image['url'] }}" class="lightbox block overflow-hidden rounded-md">
                                    <img src="{{ $image['url'] }}" alt="" class="w-full h-36 object-cover hover:scale-105 duration-500">
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="lg:col-span-4">
                    <div class="rounded-md shadow-sm dark:shadow-gray-700 p-6 lg:sticky lg:top-24">
                        <p class="text-slate-400 text-sm uppercase tracking-wide mb-2">{{ __('tenant.package_price') }}</p>
                        @forelse ($package['prices'] as $price)
                            <p class="text-2xl font-semibold text-red-500">{{ $price }}</p>
                        @empty
                            <p class="text-slate-400">{{ __('tenant.package_price_on_request') }}</p>
                            @if ($package['show_login'] ?? false)
                                <a href="{{ $package['login_url'] }}" class="mt-4 py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">{{ __('tenant.package_login') }}</a>
                            @endif
                        @endforelse

                        <a href="{{ $package['pdf_url'] }}" class="mt-6 py-2 px-5 inline-block tracking-wide align-middle duration-500 text-base text-center bg-red-500 text-white rounded-md w-full">
                            {{ __('tenant.package_download_pdf') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
