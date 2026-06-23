@extends('layouts.base', ['title' => __('pages.'.$page.'.title')])

@section('content')

    @include('layouts.partials.navbar', [
        'hideSearch' => true,
        'fixedWidth' => true,
        'sticky' => false,
        'topbarColor' => 'navbar-light',
        'classList' => 'ms-auto',
    ])

    <section class="position-relative py-5 bg-gradient2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center text-lg-start">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1 mb-3">{{ __('pages.placeholder_badge') }}</span>
                    <h1 class="display-6 fw-semibold mb-3">{{ __('pages.'.$page.'.title') }}</h1>
                    <p class="text-muted fs-17 mb-4">{{ __('pages.'.$page.'.lead') }}</p>
                    <p class="text-muted mb-0">{{ __('pages.placeholder_notice') }}</p>

                    @if ($showDemoCta ?? false)
                        <div class="mt-4">
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                {{ __('pages.demo.cta') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer />

@endsection
