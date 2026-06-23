@php
    /** @var string $document */
    $documentKey = 'legal.'.$document;
@endphp

@extends('layouts.base', ['title' => __($documentKey.'.title')])

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
                <div class="col-lg-8">
                    <span class="badge rounded-pill badge-soft-primary px-2 py-1 mb-3">{{ __($documentKey.'.badge') }}</span>
                    <h1 class="display-6 fw-semibold mb-3">{{ __($documentKey.'.title') }}</h1>
                    <p class="text-muted mb-4">
                        <strong>{{ __($documentKey.'.updated_label') }}</strong>
                        {{ __($documentKey.'.updated_date') }}
                    </p>
                    <p class="text-muted mb-4">{{ __($documentKey.'.intro') }}</p>
                    <p class="text-muted small mb-5">{{ __($documentKey.'.disclaimer') }}</p>

                    @foreach (__($documentKey.'.sections') as $section)
                        <h2 class="h5 fw-semibold mb-2">{{ $section['title'] }}</h2>
                        @foreach ($section['paragraphs'] as $paragraph)
                            <p @class(['text-muted', 'mb-3' => ! ($loop->parent->last && $loop->last), 'mb-0' => $loop->parent->last && $loop->last])>{{ $paragraph }}</p>
                        @endforeach
                        @unless ($loop->last)
                            <div class="mb-2"></div>
                        @endunless
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <x-site-footer />

@endsection
