@php
    $serviceTypeLabel = $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code);
    $headerDisplayName = $service->name !== '' ? $service->name : __('wizard.service_unnamed');
    $stepPageTitle = __('wizard.header_title', [
        'type' => $serviceTypeLabel,
        'name' => $headerDisplayName,
        'step' => 8,
    ]);
    $advancedStepSubtitle = match ($serviceType->code) {
        'gastronomy' => __('wizard.step7_subtitle'),
        'accomodation' => __('wizard.step7_hotel_subtitle'),
        'activity', 'event' => __('wizard.step7_activity_subtitle'),
        'transfer' => __('wizard.step7_transfer_subtitle'),
        default => '',
    };
@endphp
@extends('layouts.base', ['title' => $stepPageTitle])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @include('services.wizard.partials._steps_nav', [
        'serviceType' => $serviceType,
        'service' => $service,
        'currentStep' => 8,
    ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @include('services.wizard.partials._header', [
                'serviceType' => $serviceType,
                'service' => $service,
                'step' => 8,
                'subtitle' => $advancedStepSubtitle,
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
                            @if ($serviceType->code === 'gastronomy')
                                <livewire:service-wizard.service-gastronomy-advanced-step
                                    :service-id="$service->id"
                                    :service-type-id="$serviceType->id"
                                    :key="'service-gastronomy-advanced-'.$service->id"
                                />
                            @elseif ($serviceType->code === 'accomodation')
                                <livewire:service-wizard.service-hotel-advanced-step
                                    :service-id="$service->id"
                                    :service-type-id="$serviceType->id"
                                    :key="'service-hotel-advanced-'.$service->id"
                                />
                            @elseif (in_array($serviceType->code, ['activity', 'event'], true))
                                <livewire:service-wizard.service-activity-advanced-step
                                    :service-id="$service->id"
                                    :service-type-id="$serviceType->id"
                                    :key="'service-activity-advanced-'.$service->id"
                                />
                            @elseif ($serviceType->code === 'transfer')
                                <livewire:service-wizard.service-transfer-advanced-step
                                    :service-id="$service->id"
                                    :service-type-id="$serviceType->id"
                                    :key="'service-transfer-advanced-'.$service->id"
                                />
                            @endif

                            @include('services.wizard.partials._footer', [
                                'serviceType' => $serviceType,
                                'service' => $service,
                                'currentStep' => 8,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
