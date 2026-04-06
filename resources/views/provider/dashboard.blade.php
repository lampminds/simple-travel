@extends('layouts.base', ['title' => 'Prompt - Provider Dashboard'])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">Dashboard de Proveedor</h3>
                        <p class="mt-1 fw-medium">Inicia la creación de un nuevo servicio</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <h4 class="h5 mb-2">{{ __('wizard.provider_services_title') }}</h4>
                    <p class="text-muted small mb-3">{{ __('wizard.provider_services_subtitle') }}</p>

                    @if ($services->isEmpty())
                        <div class="alert alert-light border mb-0" role="status">
                            {{ __('wizard.provider_services_empty') }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle bg-white rounded border">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="text-center" style="width: 76px;">{{ __('wizard.provider_services_col_thumb') }}</th>
                                        <th scope="col">{{ __('wizard.provider_services_col_name') }}</th>
                                        <th scope="col">{{ __('wizard.provider_services_col_type') }}</th>
                                        <th scope="col">{{ __('wizard.provider_services_col_status') }}</th>
                                        <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $svc)
                                        <tr>
                                            <td class="text-center align-middle">
                                                @if ($url = $svc->mainImageUrl(\App\Models\Service::MEDIA_CONVERSION_THUMBNAIL))
                                                    <img src="{{ $url }}" alt="" class="rounded border" style="width: 56px; height: 56px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $svc->name !== '' ? $svc->name : ('#'.$svc->id) }}</td>
                                            <td>{{ $svc->serviceType?->name ?: strtoupper($svc->serviceType?->code ?? '') }}</td>
                                            <td>
                                                @php $st = $svc->status ?? ''; @endphp
                                                {{ $st !== '' ? __('filament.resources.service_status.'.$st) : '—' }}
                                            </td>
                                            <td class="text-end text-nowrap">
                                                @if ($svc->serviceType)
                                                    <a
                                                        href="{{ route('services.wizard.step1.edit', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                        class="btn btn-sm btn-outline-primary me-1"
                                                    >
                                                        {{ __('wizard.provider_services_action_step1') }}
                                                    </a>
                                                    <a
                                                        href="{{ route('services.wizard.step2', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                        class="btn btn-sm btn-outline-secondary"
                                                    >
                                                        {{ __('wizard.provider_services_action_step2') }}
                                                    </a>
                                                    <a
                                                        href="{{ route('services.wizard.step3', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                        class="btn btn-sm btn-outline-secondary me-1"
                                                    >
                                                        {{ __('wizard.provider_services_action_step3') }}
                                                    </a>
                                                    <a
                                                        href="{{ route('services.wizard.step4', ['serviceType' => $svc->serviceType->code, 'service' => $svc->id]) }}"
                                                        class="btn btn-sm btn-outline-secondary"
                                                    >
                                                        {{ __('wizard.provider_services_action_step4') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mt-4 g-3">
                @forelse($serviceTypes as $serviceType)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">{{ $serviceType->name ?: strtoupper($serviceType->code) }}</h5>
                                <p class="text-muted mb-3">Crear un nuevo servicio de este tipo.</p>
                                <a
                                    href="{{ route('services.wizard.step1', ['serviceType' => $serviceType->code]) }}"
                                    class="btn btn-primary mt-auto"
                                >
                                    Nuevo servicio
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12">
                        <div class="alert alert-warning mb-0" role="alert">
                            No hay tipos de servicio activos configurados.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                <a href="{{ url('/account/dashboard') }}" class="btn btn-outline-primary">
                    Volver a la selección de dashboard
                </a>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
