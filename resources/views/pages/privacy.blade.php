@extends('layouts.base', ['title' => 'Privacy policy'])

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
                    <h1 class="h3 fw-semibold mb-3">Privacy policy</h1>
                    <p class="text-muted mb-3">
                        <strong>Last updated:</strong> {{ now()->format('F j, Y') }}
                    </p>
                    <p class="text-muted mb-4">
                        This is a static placeholder privacy policy. It does not constitute legal advice.
                        Replace this content with a policy drafted or reviewed by qualified counsel for your jurisdictions
                        and data processing activities.
                    </p>
                    <h2 class="h5 fw-semibold mb-2">Data we may collect</h2>
                    <p class="text-muted mb-4">
                        Typical categories can include account details, contact information, usage logs, and content you submit
                        through the platform. Describe your actual practices here.
                    </p>
                    <h2 class="h5 fw-semibold mb-2">How we use data</h2>
                    <p class="text-muted mb-4">
                        Explain purposes such as providing the service, security, support, analytics, and legal compliance.
                    </p>
                    <h2 class="h5 fw-semibold mb-2">Your rights</h2>
                    <p class="text-muted mb-0">
                        Summarize rights (access, correction, deletion, portability, objection) and how users can contact you.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
