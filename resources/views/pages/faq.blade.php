@extends('layouts.base', ['title' => __('faq.page_title')])

@section('content')

    @include('layouts.partials.navbar', [
        'hideSearch' => true,
        'fixedWidth' => true,
        'sticky' => false,
        'topbarColor' => 'navbar-light',
        'classList' => 'ms-auto',
    ])

    <section class="section py-6 pt-sm-6 pb-sm-7 position-relative bg-light">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1">{{ __('faq.badge') }}</span>
                    <h1 class="display-5 fw-semibold">{{ __('faq.heading') }}</h1>
                    <p class="text-muted mx-auto">
                        {{ __('faq.subtitle') }}
                    </p>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-10 col-lg-8">
                    @if (count($faqItems) > 0)
                        <x-faq-accordion :items="$faqItems" />
                    @else
                        <p class="text-muted text-center mb-0">{{ __('faq.empty') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
