@extends('layouts.base', ['title' => __('public_profile.page_title')])

@section('css')
    @vite('resources/js/public-profile-editor.js')
    <style>
        .about-editor-wrap .ql-editor {
            min-height: 140px;
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

                                <div class="mb-4">
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

                                <div class="mb-4">
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

                                <div class="mb-4">
                                    <label class="form-label" for="home_background">{{ __('public_profile.home_background') }}</label>
                                    @if ($homeBackgroundUrl)
                                        <div class="mb-2">
                                            <img src="{{ $homeBackgroundUrl }}" alt="" class="img-fluid rounded" style="max-height: 160px;">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="remove_home_background" id="remove_home_background" value="1" @checked(old('remove_home_background'))>
                                            <label class="form-check-label" for="remove_home_background">{{ __('public_profile.home_background_remove') }}</label>
                                        </div>
                                    @endif
                                    <input
                                        type="file"
                                        class="form-control @error('home_background') is-invalid @enderror"
                                        id="home_background"
                                        name="home_background"
                                        accept="image/jpeg,image/png,image/webp,image/avif"
                                    >
                                    <div class="form-text">{{ __('public_profile.home_background_help') }}</div>
                                </div>

                                <div class="row g-2 mb-2 d-none d-lg-flex text-muted small">
                                    <div class="col-lg-2">{{ __('public_profile.language') }}</div>
                                    <div class="col-lg-5">{{ __('public_profile.title') }}</div>
                                    <div class="col-lg-5">{{ __('public_profile.subtitle') }}</div>
                                </div>

                                @foreach ($languages as $language)
                                    @php
                                        $prefix = 'translations.'.$language->id;
                                        $current = $translations[$language->id] ?? ['title' => '', 'subtitle' => '', 'about' => ''];
                                        $aboutHtml = \App\Models\AccountPublicProfile::renderAbout(old($prefix.'.about', $current['about']));
                                    @endphp
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="row g-2 align-items-start">
                                            <div class="col-lg-2">
                                                <div class="fw-semibold pt-lg-2">{{ $language->display_name }}</div>
                                            </div>
                                            <div class="col-lg-5">
                                                <label class="form-label d-lg-none" for="title_{{ $language->id }}">{{ __('public_profile.title') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-control @error($prefix.'.title') is-invalid @enderror"
                                                    id="title_{{ $language->id }}"
                                                    name="translations[{{ $language->id }}][title]"
                                                    value="{{ old($prefix.'.title', $current['title']) }}"
                                                    maxlength="255"
                                                >
                                            </div>
                                            <div class="col-lg-5">
                                                <label class="form-label d-lg-none" for="subtitle_{{ $language->id }}">{{ __('public_profile.subtitle') }}</label>
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
                                        <div class="mt-2">
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

                                <button type="submit" class="btn btn-primary">{{ __('public_profile.save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
