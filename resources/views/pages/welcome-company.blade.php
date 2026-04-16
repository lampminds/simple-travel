@extends('layouts.base', ['title' => __('welcome_company.page_title')])

@php
    $welcomeRobot = file_exists(public_path('images/robots/welcome.png'))
        ? asset('images/robots/welcome.png')
        : asset('images/robots/welcome-small.png');
@endphp

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
            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row align-items-center gy-5">
                <div class="col-lg-6 order-2 order-lg-1">
                    <h1 class="display-6 fw-semibold mb-3">{{ __('welcome_company.headline') }}</h1>
                    <p class="text-muted fs-16 mb-4">{{ __('welcome_company.intro') }}</p>

                    <h2 class="h5 fw-semibold mb-3">{{ __('welcome_company.todo_heading') }}</h2>
                    <ol class="ps-3 mb-4 text-muted">
                        @foreach (__('welcome_company.todo') as $item)
                            <li class="mb-2">{{ $item }}</li>
                        @endforeach
                    </ol>

                    <a href="{{ route('account.dashboard') }}" class="btn btn-primary">
                        {{ __('welcome_company.cta') }}
                    </a>
                </div>

                <div class="col-lg-6 order-1 order-lg-2 text-center text-lg-end">
                    <img src="{{ $welcomeRobot }}" alt="" class="img-fluid" style="max-height: 420px; width: auto;" loading="lazy"/>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
