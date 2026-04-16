@extends('layouts.base', ['title' => 'Prompt - Panel de prestador'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Panel de Prestador</h3>
                        <p class="mt-1 fw-medium">
                            {{ __('catalog.provider_intro') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 col-xl-8">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('catalog') }}" class="btn btn-primary">
                            {{ __('catalog.title') }}
                        </a>
                        <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary">
                            {{ __('catalog.back_dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
