@php
    $serviceTypeLabel = $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code);
    $headerDisplayName = $service->name !== '' ? $service->name : __('wizard.service_unnamed');
    $stepPageTitle = __('wizard.header_title', [
        'type' => $serviceTypeLabel,
        'name' => $headerDisplayName,
        'step' => 3,
    ]);
@endphp
@extends('layouts.base', ['title' => $stepPageTitle])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @include('services.wizard.partials._steps_nav', [
        'serviceType' => $serviceType,
        'service' => $service,
        'currentStep' => 3,
    ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @include('services.wizard.partials._header', [
                'serviceType' => $serviceType,
                'service' => $service,
                'step' => 3,
                'subtitle' => __('wizard.step3_subtitle'),
            ])

            @if (session('status'))
                <div class="alert alert-success mt-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <livewire:service-wizard.service-features-step
                                :service-id="$service->id"
                                :service-type-id="$serviceType->id"
                                :key="'service-features-'.$service->id"
                            />

                            @include('services.wizard.partials._footer', [
                                'serviceType' => $serviceType,
                                'service' => $service,
                                'currentStep' => 3,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
