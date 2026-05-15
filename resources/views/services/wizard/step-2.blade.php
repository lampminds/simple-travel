@php
    $serviceTypeLabel = $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code);
    $headerDisplayName = $service->name !== '' ? $service->name : __('wizard.service_unnamed');
    $stepPageTitle = __('wizard.header_title', [
        'type' => $serviceTypeLabel,
        'name' => $headerDisplayName,
        'step' => 2,
    ]);
@endphp
@extends('layouts.base', ['title' => $stepPageTitle])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @include('services.wizard.partials._steps_nav', [
        'serviceType' => $serviceType,
        'service' => $service,
        'currentStep' => 2,
    ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @include('services.wizard.partials._header', [
                'serviceType' => $serviceType,
                'service' => $service,
                'step' => 2,
                'subtitle' => __('wizard.step2_subtitle'),
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
                            <livewire:service-wizard.service-status-step
                                :service-id="$service->id"
                                :service-type-id="$serviceType->id"
                                :key="'service-status-'.$service->id"
                            />

                            @include('services.wizard.partials._footer', [
                                'serviceType' => $serviceType,
                                'service' => $service,
                                'currentStep' => 2,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
