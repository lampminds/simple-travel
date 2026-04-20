@extends('layouts.base', ['title' => 'About'])

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
                    <h1 class="h3 fw-semibold mb-3">About</h1>
                    <p class="text-muted mb-4">
                        SimpleTravel helps travel businesses manage accounts, services, and day-to-day operations in one place.
                        This page is a placeholder; replace it with your company story, mission, and team when ready.
                    </p>
                    <p class="text-muted mb-0">
                        For product questions or partnerships, use the contact options from the main site when they are available.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
