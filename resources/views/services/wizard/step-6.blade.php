@php
    $serviceTypeLabel = $serviceType->name !== '' ? $serviceType->name : strtoupper($serviceType->code);
    $headerDisplayName = $service->name !== '' ? $service->name : __('wizard.service_unnamed');
    $stepPageTitle = __('wizard.header_title', [
        'type' => $serviceTypeLabel,
        'name' => $headerDisplayName,
        'step' => 6,
    ]);
    $step6HelperAccountTypeId = $catalogHelperAccountTypeId ?? null;
    $catalogVisibilityTabHelpHtml = \App\Support\ServiceWizardStep6CatalogHelpers::htmlMapForForm(
        $serviceType->id,
        $step6HelperAccountTypeId,
    );
    $catalogMandatoryHelpHtml = \App\Support\ServiceWizardStep6CatalogHelpers::htmlForMandatory(
        $serviceType->id,
        $step6HelperAccountTypeId,
    );
@endphp
@extends('layouts.base', ['title' => $stepPageTitle])

@section('css')
    <style>
        .popover.catalog-helper-popover {
            max-width: min(28rem, 92vw);
            border: 1px solid rgba(15, 23, 42, 0.18);
            border-radius: 0.5rem;
            box-shadow:
                0 0 0 1px rgba(15, 23, 42, 0.06),
                0 10px 15px -3px rgba(15, 23, 42, 0.14),
                0 20px 40px -12px rgba(15, 23, 42, 0.22);
            background-color: #f1f5f9;
            overflow: hidden;
        }
        .popover.catalog-helper-popover .popover-header {
            background-color: #e2e8f0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.12);
            color: #0f172a;
            font-weight: 600;
        }
        .popover.catalog-helper-popover .popover-body {
            max-height: min(70vh, 28rem);
            overflow: auto;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .popover.catalog-helper-popover .popover-body img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    @include('services.wizard.partials._steps_nav', [
        'serviceType' => $serviceType,
        'service' => $service,
        'currentStep' => 6,
    ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @include('services.wizard.partials._header', [
                'serviceType' => $serviceType,
                'service' => $service,
                'step' => 6,
                'subtitle' => __('wizard.step6_subtitle'),
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
                            <livewire:service-wizard.service-details-step
                                :service-id="$service->id"
                                :service-type-id="$serviceType->id"
                                :catalog-visibility-tab-help-html="$catalogVisibilityTabHelpHtml"
                                :catalog-mandatory-help-html="$catalogMandatoryHelpHtml"
                                :key="'service-details-'.$service->id"
                            />

                            @include('services.wizard.partials._footer', [
                                'serviceType' => $serviceType,
                                'service' => $service,
                                'currentStep' => 6,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    @include('partials.catalog-helper-popover-script')
@endsection
