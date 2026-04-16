@extends('layouts.base', ['title' => __('catalog.title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('catalog.title') }}</h3>
                        <p class="mt-1 fw-medium">
                            @if ($mode === 'provider')
                                {{ __('catalog.provider_intro') }}
                            @elseif ($mode === 'agency')
                                {{ __('catalog.agency_intro') }}
                            @else
                                {{ __('catalog.wholesaler_intro') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if ($mode === 'provider')
                @include('catalog.partials.provider-services', ['services' => $services, 'serviceTypes' => $serviceTypes])
            @elseif ($mode === 'agency')
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <p class="text-muted mb-0">{{ __('catalog.agency_placeholder') }}</p>
                    </div>
                </div>
            @else
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <h4 class="h5 mb-3">{{ __('catalog.wholesaler_section_linked') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle bg-white rounded border">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">{{ __('catalog.wholesaler_col_provider') }}</th>
                                        <th scope="col">{{ __('catalog.wholesaler_col_service') }}</th>
                                        <th scope="col">{{ __('catalog.wholesaler_col_type') }}</th>
                                        <th scope="col">{{ __('catalog.wholesaler_col_status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wholesalerApprovedRows as $row)
                                        <tr>
                                            <td>{{ $row['provider'] }}</td>
                                            <td>{{ $row['name'] }}</td>
                                            <td>{{ $row['type'] }}</td>
                                            <td><span class="badge text-bg-success">{{ $row['status'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-12 col-xl-8">
                        <div class="card border border-dashed">
                            <div class="card-body">
                                <h4 class="h6 mb-2">{{ __('catalog.wholesaler_section_own') }}</h4>
                                <p class="text-muted small mb-3">{{ __('catalog.wholesaler_own_hint') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" disabled title="{{ __('catalog.wholesaler_own_hint') }}">
                                        {{ __('catalog.wholesaler_btn_create') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="{{ __('catalog.wholesaler_own_hint') }}">
                                        {{ __('catalog.wholesaler_btn_manage') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary">
                    {{ __('catalog.back_dashboard') }}
                </a>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
