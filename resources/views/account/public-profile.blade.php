@extends('layouts.base', ['title' => __('public_profile.page_title')])

@section('css')
    @vite('resources/js/public-profile-editor.js')
    <style>
        .about-editor-wrap .ql-editor {
            min-height: 140px;
        }
        .website-language-tabs {
            background-color: var(--bs-tertiary-bg);
            border-radius: var(--bs-border-radius);
            padding: 0.5rem 0.5rem 0;
        }
        .website-language-tabs .nav-tabs {
            border-bottom: none;
            gap: 0.25rem;
        }
        .website-language-tabs .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-radius: 0.375rem 0.375rem 0 0;
            color: var(--bs-secondary-color);
            background-color: rgba(var(--bs-emphasis-color-rgb), 0.06);
            margin-bottom: 0;
        }
        .website-language-tabs .nav-tabs .nav-link:hover:not(.active) {
            color: var(--bs-body-color);
            background-color: rgba(var(--bs-emphasis-color-rgb), 0.09);
        }
        .website-language-tabs .nav-tabs .nav-link.active {
            color: var(--bs-primary);
            font-weight: 600;
            background-color: var(--bs-body-bg);
            border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-body-bg);
            box-shadow: 0 -0.2rem 0 0 var(--bs-primary);
        }
        .website-language-tabs .nav-tabs .nav-link.text-danger {
            color: var(--bs-danger);
        }
        .website-language-tabs .nav-tabs .nav-link.active.text-danger {
            color: var(--bs-danger);
            box-shadow: 0 -0.2rem 0 0 var(--bs-danger);
        }
        /* Keep inactive panes in the layout so the rich text editor measures a real width. */
        .website-language-panes.tab-content > .tab-pane {
            display: block;
            height: 0;
            overflow: hidden;
            visibility: hidden;
        }
        .website-language-panes.tab-content > .tab-pane.active {
            height: auto;
            overflow: visible;
            visibility: visible;
        }
    </style>
@endsection

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('public_profile.heading') }}</h3>
                        <p class="mt-1 fw-medium mb-0">{{ __('public_profile.subtitle_help') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <div class="fw-semibold">{{ __('public_profile.validation_heading') }}</div>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('account.public-profile.update') }}" enctype="multipart/form-data" novalidate>
                                @csrf
                                @method('PUT')

                                @if ($publicWebsiteUrl)
                                    <div class="mb-4">
                                        <div class="form-label mb-1">{{ __('public_profile.public_url') }}</div>
                                        <a href="{{ $publicWebsiteUrl }}" target="_blank" rel="noopener noreferrer">{{ $publicWebsiteUrl }}</a>
                                        <div class="form-text">{{ __('public_profile.public_url_help') }}</div>
                                    </div>
                                @endif

                                <div class="row g-3 mb-4">
                                    <div class="col-lg-6">
                                        <label class="form-label" for="portal_logo">{{ __('public_profile.logo') }}</label>
                                        @if ($logoUrl)
                                            <div class="mb-2">
                                                <img src="{{ $logoUrl }}" alt="" class="img-fluid" style="max-height: 64px;">
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="remove_portal_logo" id="remove_portal_logo" value="1" @checked(old('remove_portal_logo'))>
                                                <label class="form-check-label" for="remove_portal_logo">{{ __('public_profile.logo_remove') }}</label>
                                            </div>
                                        @endif
                                        <input
                                            type="file"
                                            class="form-control @error('portal_logo') is-invalid @enderror"
                                            id="portal_logo"
                                            name="portal_logo"
                                            accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml"
                                        >
                                        <div class="form-text">{{ __('public_profile.logo_help') }}</div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label" for="portal_logo_on_white">{{ __('public_profile.logo_on_white') }}</label>
                                        @if ($logoOnWhiteUrl)
                                            <div class="mb-2 p-2 bg-white border rounded d-inline-block">
                                                <img src="{{ $logoOnWhiteUrl }}" alt="" class="img-fluid" style="max-height: 64px;">
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="remove_portal_logo_on_white" id="remove_portal_logo_on_white" value="1" @checked(old('remove_portal_logo_on_white'))>
                                                <label class="form-check-label" for="remove_portal_logo_on_white">{{ __('public_profile.logo_on_white_remove') }}</label>
                                            </div>
                                        @endif
                                        <input
                                            type="file"
                                            class="form-control @error('portal_logo_on_white') is-invalid @enderror"
                                            id="portal_logo_on_white"
                                            name="portal_logo_on_white"
                                            accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml"
                                        >
                                        <div class="form-text">{{ __('public_profile.logo_on_white_help') }}</div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-lg-6">
                                        <label class="form-label" for="business_hours">{{ __('public_profile.business_hours') }}</label>
                                        <input
                                            type="text"
                                            class="form-control @error('business_hours') is-invalid @enderror"
                                            id="business_hours"
                                            name="business_hours"
                                            value="{{ old('business_hours', $businessHours) }}"
                                            maxlength="120"
                                            placeholder="{{ __('public_profile.business_hours_placeholder') }}"
                                        >
                                        <div class="form-text">{{ __('public_profile.business_hours_help') }}</div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label" for="video_url">{{ __('public_profile.video_url') }}</label>
                                        <input
                                            type="url"
                                            class="form-control @error('video_url') is-invalid @enderror"
                                            id="video_url"
                                            name="video_url"
                                            value="{{ old('video_url', $videoUrl) }}"
                                            maxlength="500"
                                            placeholder="https://"
                                        >
                                        <div class="form-text">{{ __('public_profile.video_url_help') }}</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label" for="home_background">{{ __('public_profile.home_background') }}</label>
                                    @if ($homeBackgroundSlides !== [])
                                        @php
                                            $removedSlideIds = array_map('intval', (array) old('remove_home_background', []));
                                        @endphp
                                        <ul id="hero-slides" class="list-unstyled mb-3">
                                            @foreach ($homeBackgroundSlides as $slide)
                                                <li class="d-flex align-items-center gap-3 border rounded p-2 mb-2" data-hero-slide>
                                                    <img src="{{ $slide['url'] }}" alt="" class="rounded flex-shrink-0" style="width: 144px; height: 81px; object-fit: cover;">
                                                    <input type="hidden" name="home_background_order[]" value="{{ $slide['id'] }}">
                                                    <span class="text-muted small" data-hero-position></span>
                                                    <div class="ms-auto d-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-hero-move="up" aria-label="{{ __('public_profile.home_background_move_up') }}">↑</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-hero-move="down" aria-label="{{ __('public_profile.home_background_move_down') }}">↓</button>
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input" type="checkbox" name="remove_home_background[]" id="remove_slide_{{ $slide['id'] }}" value="{{ $slide['id'] }}" @checked(in_array($slide['id'], $removedSlideIds, true))>
                                                            <label class="form-check-label" for="remove_slide_{{ $slide['id'] }}">{{ __('public_profile.home_background_remove') }}</label>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <input
                                        type="file"
                                        class="form-control @if ($errors->has('home_background') || $errors->has('home_background.0')) is-invalid @endif"
                                        id="home_background"
                                        name="home_background[]"
                                        accept="image/jpeg,image/png,image/webp,image/avif"
                                        multiple
                                    >
                                    <div class="form-text">{{ __('public_profile.home_background_help', ['max' => \App\Models\Account::HOME_BACKGROUND_MAX_SLIDES]) }}</div>
                                </div>

                                @php
                                    $activeLanguageId = null;
                                    $currentLocale = app()->getLocale();
                                    foreach ($languages as $language) {
                                        $prefix = 'translations.'.$language->id;
                                        if ($errors->has($prefix.'.title') || $errors->has($prefix.'.subtitle') || $errors->has($prefix.'.about')) {
                                            $activeLanguageId = $language->id;
                                            break;
                                        }
                                    }
                                    if ($activeLanguageId === null) {
                                        foreach ($languages as $language) {
                                            if (\App\Models\Locale::primaryTagMatches($language->locale, $currentLocale)) {
                                                $activeLanguageId = $language->id;
                                                break;
                                            }
                                        }
                                    }
                                    $activeLanguageId ??= $languages->first()?->id;
                                @endphp

                                @if ($languages->isNotEmpty())
                                    <div class="website-language-tabs mb-3">
                                        <ul class="nav nav-tabs flex-wrap" id="website-language-tabs" role="tablist">
                                            @foreach ($languages as $language)
                                                @php
                                                    $prefix = 'translations.'.$language->id;
                                                    $languageHasErrors = $errors->has($prefix.'.title')
                                                        || $errors->has($prefix.'.subtitle')
                                                        || $errors->has($prefix.'.about');
                                                    $isActiveLanguage = (int) $activeLanguageId === (int) $language->id;
                                                    $language->loadMissing('locale');
                                                @endphp
                                                <li class="nav-item" role="presentation">
                                                    <button
                                                        class="nav-link @if ($isActiveLanguage) active @endif @if ($languageHasErrors) text-danger @endif"
                                                        id="website-lang-tab-{{ $language->id }}"
                                                        data-bs-toggle="tab"
                                                        data-bs-target="#website-lang-pane-{{ $language->id }}"
                                                        type="button"
                                                        role="tab"
                                                        aria-controls="website-lang-pane-{{ $language->id }}"
                                                        aria-selected="{{ $isActiveLanguage ? 'true' : 'false' }}"
                                                    >
                                                        <span class="me-1" aria-hidden="true">{{ $language->locale?->flagEmoji() ?? '🌐' }}</span>{{ $language->display_name }}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="tab-content website-language-panes mb-3">
                                        @foreach ($languages as $language)
                                            @php
                                                $prefix = 'translations.'.$language->id;
                                                $current = $translations[$language->id] ?? ['title' => '', 'subtitle' => '', 'about' => ''];
                                                $aboutHtml = \App\Models\AccountPublicProfile::renderAbout(old($prefix.'.about', $current['about']));
                                                $isActiveLanguage = (int) $activeLanguageId === (int) $language->id;
                                            @endphp
                                            <div
                                                class="tab-pane fade @if ($isActiveLanguage) show active @endif"
                                                id="website-lang-pane-{{ $language->id }}"
                                                role="tabpanel"
                                                aria-labelledby="website-lang-tab-{{ $language->id }}"
                                                tabindex="0"
                                            >
                                                <div class="row g-3">
                                                    <div class="col-lg-6">
                                                        <label class="form-label" for="title_{{ $language->id }}">{{ __('public_profile.title') }}</label>
                                                        <input
                                                            type="text"
                                                            class="form-control @error($prefix.'.title') is-invalid @enderror"
                                                            id="title_{{ $language->id }}"
                                                            name="translations[{{ $language->id }}][title]"
                                                            value="{{ old($prefix.'.title', $current['title']) }}"
                                                            maxlength="255"
                                                        >
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="form-label" for="subtitle_{{ $language->id }}">{{ __('public_profile.subtitle') }}</label>
                                                        <input
                                                            type="text"
                                                            class="form-control @error($prefix.'.subtitle') is-invalid @enderror"
                                                            id="subtitle_{{ $language->id }}"
                                                            name="translations[{{ $language->id }}][subtitle]"
                                                            value="{{ old($prefix.'.subtitle', $current['subtitle']) }}"
                                                            maxlength="255"
                                                        >
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <label class="form-label" for="about_{{ $language->id }}">{{ __('public_profile.about') }}</label>
                                                    <div class="about-editor-wrap border rounded @error($prefix.'.about') border-danger @enderror">
                                                        <div data-about-editor="about_{{ $language->id }}">{!! $aboutHtml !!}</div>
                                                    </div>
                                                    <textarea
                                                        class="d-none"
                                                        id="about_{{ $language->id }}"
                                                        name="translations[{{ $language->id }}][about]"
                                                    >{{ $aboutHtml }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-primary">{{ __('public_profile.save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    <script>
        (function () {
            var list = document.getElementById('hero-slides');
            if (!list) {
                return;
            }

            function refresh() {
                var items = list.querySelectorAll('[data-hero-slide]');
                items.forEach(function (item, index) {
                    var label = item.querySelector('[data-hero-position]');
                    if (label) {
                        label.textContent = String(index + 1);
                    }
                    var up = item.querySelector('[data-hero-move="up"]');
                    var down = item.querySelector('[data-hero-move="down"]');
                    if (up) {
                        up.disabled = index === 0;
                    }
                    if (down) {
                        down.disabled = index === items.length - 1;
                    }
                });
            }

            list.addEventListener('click', function (event) {
                var button = event.target.closest('[data-hero-move]');
                if (!button) {
                    return;
                }
                var item = button.closest('[data-hero-slide]');
                if (!item) {
                    return;
                }
                if (button.getAttribute('data-hero-move') === 'up' && item.previousElementSibling) {
                    list.insertBefore(item, item.previousElementSibling);
                }
                if (button.getAttribute('data-hero-move') === 'down' && item.nextElementSibling) {
                    list.insertBefore(item.nextElementSibling, item);
                }
                refresh();
            });

            refresh();
        })();
    </script>
@endsection
