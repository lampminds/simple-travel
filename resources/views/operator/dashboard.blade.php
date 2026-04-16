@extends('layouts.base', ['title' => 'Prompt - Panel de operador'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('operator_dashboard.title') }}</h3>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary">
                    {{ __('catalog.back_dashboard') }}
                </a>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
