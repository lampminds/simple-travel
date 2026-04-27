@extends('layouts.base', ['title' => 'Prompt - Panel de agencia'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @php
        $account = auth()->user()?->currentAccount();

        $typeCodes = collect();
        if ($account) {
            $typeCodes = $account->categories()
                ->where('group', 'type')
                ->where('active', true)
                ->pluck('code');
        }

        if (! $typeCodes->contains('agency')) {
            return redirect()->to('/account/dashboard');
        }
    @endphp

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Panel de Agencia</h3>
                        <p class="mt-1 fw-medium">Espacio para el desarrollo del panel de agencia</p>
                    </div>
                </div>
            </div>

            @if (auth()->user()?->shouldShowBackToAccountDashboard())
            <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary mt-3">
                {{ __('catalog.back_dashboard') }}
            </a>
            @endif
        </div>
    </section>

    <x-site-footer-simple />
@endsection

