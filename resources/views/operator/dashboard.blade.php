@extends('layouts.base', ['title' => 'Prompt - Operator Dashboard'])

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

        $allowed = $typeCodes->contains('wholesaler') || $typeCodes->contains('tour_operator');
        if (! $allowed) {
            return redirect()->to('/account/dashboard');
        }
    @endphp

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Dashboard de Operador / Mayorista</h3>
                        <p class="mt-1 fw-medium">Espacio para el desarrollo del panel operador/mayorista</p>
                    </div>
                </div>
            </div>

            <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary mt-3">
                Volver a la selección de dashboard
            </a>
        </div>
    </section>

    <x-site-footer-simple />
@endsection

