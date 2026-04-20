@extends('layouts.base', ['title' => 'Terms of service'])

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
                    <h1 class="h3 fw-semibold mb-3">Terms of service</h1>
                    <p class="text-muted mb-3">
                        <strong>Last updated:</strong> {{ now()->format('F j, Y') }}
                    </p>
                    <p class="text-muted mb-4">
                        This is a static placeholder terms of service page. It does not constitute legal advice.
                        Replace this content with terms appropriate for your business, product, and jurisdictions.
                    </p>
                    <h2 class="h5 fw-semibold mb-2">Acceptance</h2>
                    <p class="text-muted mb-4">
                        Describe how users accept these terms (e.g. by creating an account or using the service).
                    </p>
                    <h2 class="h5 fw-semibold mb-2">Use of the service</h2>
                    <p class="text-muted mb-4">
                        Set acceptable use rules, account responsibilities, and restrictions.
                    </p>
                    <h2 class="h5 fw-semibold mb-2">Limitation of liability</h2>
                    <p class="text-muted mb-0">
                        Add legally reviewed disclaimers and liability caps as applicable.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
