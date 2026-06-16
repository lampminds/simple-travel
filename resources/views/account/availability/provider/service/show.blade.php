@php
    use App\Support\WeekdayMask;
@endphp
@extends('layouts.base', ['title' => __('account.availability.service_show_page_title', ['service' => $service->name ?: ('#' . $service->id)])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            @php
                $serviceName = trim($service->name ?? '');
                if ($serviceName === '') {
                    $serviceName = '#' . $service->id;
                }
            @endphp

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.availability.service_show_title')"
                            :subtitle="$serviceName"
                            :instructions="__('account.availability.service_show_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.availability.index') }}" class="btn btn-light">
                                {{ __('account.availability.back_to_services') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="alert alert-light border mb-4" role="note">
                        {{ __('account.availability.service_scope_note') }}
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-white">
                            <h6 class="mb-0">{{ __('account.availability.service_rules_heading') }}</h6>
                            <a href="{{ route('account.availability.service-rules.create', $service) }}" class="btn btn-sm btn-primary">
                                {{ __('account.availability.service_rules_create_button') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($service->availabilityRules->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.availability.service_rules_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.availability.rules_columns.validity') }}</th>
                                                <th>{{ __('account.availability.rules_columns.weekdays') }}</th>
                                                <th>{{ __('account.availability.rules_columns.active') }}</th>
                                                <th class="text-end">{{ __('account.availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($service->availabilityRules as $rule)
                                                <tr>
                                                    <td>
                                                        @include('account.availability.provider.partials.date-range', [
                                                            'start' => $rule->start_date,
                                                            'end' => $rule->end_date,
                                                        ])
                                                    </td>
                                                    <td>{{ WeekdayMask::label($rule->weekday_mask) }}</td>
                                                    <td>
                                                        @if ($rule->active)
                                                            <span class="badge bg-success-subtle text-success">{{ __('account.availability.active_yes') }}</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary">{{ __('account.availability.active_no') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('account.availability.service-rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.availability.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.availability.service-rules.destroy', $rule) }}" class="d-inline" onsubmit="return confirm(@json(__('account.availability.service_rules_delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.availability.delete_button') }}</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-white">
                            <h6 class="mb-0">{{ __('account.availability.service_overrides_heading') }}</h6>
                            <a href="{{ route('account.availability.service-overrides.create', $service) }}" class="btn btn-sm btn-primary">
                                {{ __('account.availability.service_overrides_create_button') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($service->availabilityOverrides->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.availability.service_overrides_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.availability.service_overrides_columns.period') }}</th>
                                                <th>{{ __('account.availability.service_overrides_columns.status') }}</th>
                                                <th>{{ __('account.availability.service_overrides_columns.reason') }}</th>
                                                <th class="text-end">{{ __('account.availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($service->availabilityOverrides as $override)
                                                <tr>
                                                    <td>
                                                        @if ($override->end_date && $override->end_date->toDateString() !== $override->date->toDateString())
                                                            {{ locale_date($override->date) }} — {{ locale_date($override->end_date) }}
                                                        @else
                                                            {{ locale_date($override->date) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger-subtle text-danger">{{ __('account.availability.closed_badge') }}</span>
                                                    </td>
                                                    <td>{{ $override->reason ?: '—' }}</td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('account.availability.service-overrides.edit', $override) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.availability.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.availability.service-overrides.destroy', $override) }}" class="d-inline" onsubmit="return confirm(@json(__('account.availability.service_overrides_delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.availability.delete_button') }}</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($service->serviceVariants->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">{{ __('account.availability.service_variants_heading') }}</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.availability.columns.variant') }}</th>
                                                <th class="text-end">{{ __('account.availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($service->serviceVariants as $variant)
                                                @php
                                                    $variantLabel = trim($variant->name ?? '') !== '' ? $variant->name : $variant->sku;
                                                @endphp
                                                <tr>
                                                    <td>{{ $variantLabel }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('account.availability.variants.show', $variant) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.availability.variant_manage_button') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <x-site-footer-simple />
@endsection
