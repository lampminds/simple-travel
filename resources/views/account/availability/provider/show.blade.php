@php
    use App\Support\WeekdayMask;
@endphp
@extends('layouts.base', ['title' => __('account.availability.show_page_title', ['variant' => $variant->sku])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            @php
                $serviceName = trim($variant->service?->name ?? '');
                $variantLabel = trim($variant->name ?? '') !== '' ? $variant->name : $variant->sku;
            @endphp

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.availability.show_title')"
                            :subtitle="$serviceName !== '' ? $serviceName . ' — ' . $variantLabel : $variantLabel"
                            :instructions="__('account.availability.show_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.availability.index') }}" class="btn btn-light">
                                {{ __('account.availability.back_to_services') }}
                            </a>
                            @if ($variant->service)
                                <a href="{{ route('account.availability.services.show', $variant->service) }}" class="btn btn-outline-primary">
                                    {{ __('account.availability.service_manage_button') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row g-3 small">
                                <div class="col-md-3">
                                    <span class="text-muted d-block">{{ __('account.availability.summary.inventory_type') }}</span>
                                    <span class="fw-medium">{{ __('wizard.variant_inventory.' . $variant->inventory_type) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted d-block">{{ __('account.availability.summary.inventory_total') }}</span>
                                    <span class="fw-medium">
                                        @if ($variant->inventory_type === 'unlimited')
                                            —
                                        @else
                                            {{ $variant->inventory_total !== null ? number_format((int) $variant->inventory_total) : '—' }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted d-block">{{ __('account.availability.summary.note') }}</span>
                                    <span>{{ __('account.availability.summary.note_text') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-white">
                            <h6 class="mb-0">{{ __('account.availability.rules_heading') }}</h6>
                            <a href="{{ route('account.availability.rules.create', $variant) }}" class="btn btn-sm btn-primary">
                                {{ __('account.availability.rules_create_button') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($variant->availabilityRules->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.availability.rules_empty') }}</p>
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
                                            @foreach ($variant->availabilityRules as $rule)
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
                                                        <a href="{{ route('account.availability.rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.availability.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.availability.rules.destroy', $rule) }}" class="d-inline" onsubmit="return confirm(@json(__('account.availability.rules_delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.availability.delete_button') }}</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @if ($variant->usesTimeSlotInventory() && $rule->timeSlots->isNotEmpty())
                                                    <tr>
                                                        <td colspan="4" class="pt-0 pb-3">
                                                            <ul class="list-unstyled small text-muted mb-0 ps-2 border-start">
                                                                @foreach ($rule->timeSlots as $slot)
                                                                    <li>
                                                                        {{ substr((string) $slot->start_time, 0, 5) }}–{{ substr((string) $slot->end_time, 0, 5) }}
                                                                        @if ($slot->capacity !== null)
                                                                            · {{ __('account.availability.slot_capacity', ['count' => number_format((int) $slot->capacity)]) }}
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endif
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
                            <h6 class="mb-0">{{ __('account.availability.overrides_heading') }}</h6>
                            <a href="{{ route('account.availability.overrides.create', $variant) }}" class="btn btn-sm btn-primary">
                                {{ __('account.availability.overrides_create_button') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($variant->availabilityOverrides->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.availability.overrides_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.availability.overrides_columns.date') }}</th>
                                                <th>{{ __('account.availability.overrides_columns.time') }}</th>
                                                <th>{{ __('account.availability.overrides_columns.capacity') }}</th>
                                                <th class="text-end">{{ __('account.availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($variant->availabilityOverrides as $override)
                                                <tr>
                                                    <td>{{ $override->date ? locale_date($override->date) : '—' }}</td>
                                                    <td>
                                                        @if ($override->start_time)
                                                            {{ substr((string) $override->start_time, 0, 5) }}
                                                        @else
                                                            <span class="text-muted">{{ __('account.availability.whole_day') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($override->closed)
                                                            <span class="badge bg-danger-subtle text-danger">{{ __('account.availability.closed_badge') }}</span>
                                                        @elseif ($override->capacity !== null)
                                                            {{ number_format((int) $override->capacity) }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('account.availability.overrides.edit', $override) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.availability.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.availability.overrides.destroy', $override) }}" class="d-inline" onsubmit="return confirm(@json(__('account.availability.overrides_delete_confirm')));">
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
        </div>
    </section>

    <x-site-footer-simple />
@endsection
