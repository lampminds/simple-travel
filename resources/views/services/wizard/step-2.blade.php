@extends('layouts.base', ['title' => __('wizard.step2_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('wizard.step2_title') }}</h3>
                        <p class="mt-1 fw-medium">{{ __('wizard.step2_subtitle') }}</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success mt-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                {{ $serviceType->name ?: strtoupper($serviceType->code) }}
                                — {{ $service->name !== '' ? $service->name : __('wizard.service_unnamed') }}
                            </p>
                            <livewire:service-wizard.service-features-step
                                :service-id="$service->id"
                                :service-type-id="$serviceType->id"
                                :key="'service-features-'.$service->id"
                            />

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary">@lang('wizard.nav_dashboard')</a>
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a
                                        href="{{ route('services.wizard.step1.edit', ['serviceType' => $serviceType->code, 'service' => $service->id]) }}"
                                        class="btn btn-outline-primary"
                                    >@lang('wizard.nav_to_step1')</a>
                                    <a
                                        href="{{ route('services.wizard.step3', ['serviceType' => $serviceType->code, 'service' => $service->id]) }}"
                                        class="btn btn-outline-primary"
                                    >@lang('wizard.nav_to_step3')</a>
                                    <a
                                        href="{{ route('services.wizard.step4', ['serviceType' => $serviceType->code, 'service' => $service->id]) }}"
                                        class="btn btn-outline-primary"
                                    >@lang('wizard.nav_to_step4')</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
