@extends('layouts.base', ['title' => __('account.service_status_report.page_title', ['service' => $report['general']['name']])])

@section('css')
    <style>
        @media print {
            header,
            .no-print,
            .btn-back-to-top,
            footer {
                display: none !important;
            }

            body,
            .bg-gradient2 {
                background: #fff !important;
            }

            .service-status-report .card {
                break-inside: avoid;
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }

            .service-status-report table {
                font-size: 11px;
            }

            .service-status-report tr,
            .service-status-report h2,
            .service-status-report h3 {
                break-inside: avoid;
                break-after: avoid;
            }

            .service-status-report a {
                color: inherit !important;
                text-decoration: none !important;
            }

            @page {
                margin: 1.4cm;
            }
        }
    </style>
@endsection

@section('content')
    <div class="no-print">
        @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])
    </div>

    <section class="position-relative p-3 bg-gradient2 service-status-report">
        <div class="container">
            <div class="d-none d-print-block mb-3">
                <div class="fw-semibold">{{ config('app.name') }}</div>
                <div>{{ __('account.service_status_report.generated_at', ['datetime' => $report['generated_at']]) }}</div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 no-print mb-3">
                <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary">
                    {{ __('account.service_status_report.back') }}
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    {{ __('account.service_status_report.print') }}
                </button>
            </div>

            <x-account-page-header
                :title="__('account.service_status_report.title')"
                :subtitle="$report['general']['name']"
                :instructions="__('account.service_status_report.intro')"
            />
            <p class="text-muted small no-print mb-4">{{ __('account.service_status_report.generated_at', ['datetime' => $report['generated_at']]) }}</p>

            <div class="row g-2 mb-4">
                @foreach ([
                    'variants' => count($report['variants']),
                    'bookings' => count($report['bookings']),
                    'combos' => count($report['combos']),
                    'packages' => count($report['packages']),
                ] as $summaryKey => $summaryCount)
                    <div class="col-6 col-md-3">
                        <div class="border rounded bg-white p-3 h-100">
                            <div class="text-muted small">{{ __('account.service_status_report.summary_'.$summaryKey) }}</div>
                            <div class="fs-4 fw-semibold mb-0">{{ $summaryCount }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">{{ __('account.service_status_report.section_general') }}</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_name') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['name'] }}</dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_type') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['type'] }}</dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_status') }}</dt>
                        <dd class="col-sm-9">
                            <span class="badge text-bg-{{ $report['general']['status_badge'] }}">{{ $report['general']['status_label'] }}</span>
                        </dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_city') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['city'] }}</dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_booking_mode') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['booking_mode'] }}</dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_confirmation_hours') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['confirmation_hours'] }}</dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_public') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['is_public'] }}</dd>
                        <dt class="col-sm-3">{{ __('account.service_status_report.field_featured') }}</dt>
                        <dd class="col-sm-9">{{ $report['general']['is_featured'] }}</dd>
                        @if ($report['general']['description'])
                            <dt class="col-sm-3">{{ __('account.service_status_report.field_description') }}</dt>
                            <dd class="col-sm-9">{{ $report['general']['description'] }}</dd>
                        @endif
                    </dl>

                    <h3 class="h6 mt-4">{{ __('account.service_status_report.section_variants') }}</h3>
                    @if ($report['variants'] === [])
                        <p class="text-muted mb-0">{{ __('account.service_status_report.empty_variants') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('account.service_status_report.col_sku') }}</th>
                                        <th>{{ __('account.service_status_report.col_variant') }}</th>
                                        <th>{{ __('account.service_status_report.field_status') }}</th>
                                        <th class="text-end">{{ __('account.service_status_report.col_base_price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report['variants'] as $variant)
                                        <tr>
                                            <td><code>{{ $variant['sku'] }}</code></td>
                                            <td>{{ $variant['name'] }}</td>
                                            <td><span class="badge text-bg-{{ $variant['status_badge'] }}">{{ $variant['status_label'] }}</span></td>
                                            <td class="text-end">{{ $variant['price'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            @if ($report['is_combo'])
                <div class="card mb-4">
                    <div class="card-header fw-semibold">{{ __('account.service_status_report.section_composition') }}</div>
                    <div class="card-body">
                        @if ($report['composition'] === [])
                            <p class="text-muted mb-0">{{ __('account.service_status_report.empty_composition') }}</p>
                        @else
                            @foreach ($report['composition'] as $group)
                                <h3 class="h6 @if (! $loop->first) mt-4 @endif">
                                    {{ $group['variant_label'] }}
                                    <span class="badge text-bg-{{ $group['variant_status_badge'] }}">{{ $group['variant_status_label'] }}</span>
                                </h3>
                                @include('account.service-status-report.partials.component-lines', ['lines' => $group['lines']])
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header fw-semibold">{{ __('account.service_status_report.section_bookings') }}</div>
                <div class="card-body">
                    @include('account.service-status-report.partials.booking-table', [
                        'rows' => $report['bookings'],
                        'empty' => __('account.service_status_report.empty_bookings'),
                    ])
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">{{ __('account.service_status_report.section_combos') }}</div>
                <div class="card-body">
                    @if ($report['combos'] === [])
                        <p class="text-muted mb-0">{{ __('account.service_status_report.empty_combos') }}</p>
                    @else
                        @foreach ($report['combos'] as $combo)
                            <div class="@if (! $loop->first) mt-4 pt-3 border-top @endif">
                                <h3 class="h6">
                                    {{ $combo['service_name'] }}
                                    <span class="badge text-bg-{{ $combo['service_status_badge'] }}">{{ $combo['service_status_label'] }}</span>
                                </h3>
                                <p class="mb-2">
                                    <span class="text-muted">{{ __('account.service_status_report.col_variant') }}:</span>
                                    {{ $combo['variant_label'] }}
                                    <span class="badge text-bg-{{ $combo['variant_status_badge'] }}">{{ $combo['variant_status_label'] }}</span>
                                </p>
                                @include('account.service-status-report.partials.component-lines', ['lines' => $combo['lines']])
                                <h4 class="h6 mt-3">{{ __('account.service_status_report.combo_bookings') }}</h4>
                                @include('account.service-status-report.partials.booking-table', [
                                    'rows' => $combo['bookings'],
                                    'empty' => __('account.service_status_report.empty_combo_bookings'),
                                ])
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">{{ __('account.service_status_report.section_packages') }}</div>
                <div class="card-body">
                    @include('account.service-status-report.partials.catalog-lines', [
                        'rows' => $report['packages'],
                        'empty' => __('account.service_status_report.empty_packages'),
                    ])
                </div>
            </div>

            @if ($report['direct_services'] !== [])
                <div class="card mb-4">
                    <div class="card-header fw-semibold">{{ __('account.service_status_report.section_direct_services') }}</div>
                    <div class="card-body">
                        <p class="text-muted small">{{ __('account.service_status_report.direct_services_note') }}</p>
                        @include('account.service-status-report.partials.catalog-lines', [
                            'rows' => $report['direct_services'],
                            'empty' => '',
                        ])
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="no-print">
        <x-site-footer-simple />
    </div>
@endsection
