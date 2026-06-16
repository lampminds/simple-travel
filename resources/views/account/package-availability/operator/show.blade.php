@php
    use App\Support\WeekdayMask;
@endphp
@extends('layouts.base', ['title' => __('account.package_availability.show_page_title', ['package' => $catalog->displayLabel()])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.package_availability.show_title')"
                            :subtitle="$catalog->displayLabel()"
                            :instructions="__('account.package_availability.show_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.package-availability.index') }}" class="btn btn-light">
                                {{ __('account.package_availability.back_to_packages') }}
                            </a>
                            <a href="{{ route('account.package-offers.index', ['as' => 'operator']) }}" class="btn btn-outline-primary">
                                {{ __('account.package_availability.manage_offers_link') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="POST" action="{{ route('account.package-availability.catalogs.inventory.update', $catalog) }}" class="row g-3 align-items-end">
                                @csrf
                                @method('PUT')
                                <div class="col-md-4">
                                    <label for="inventory_type" class="form-label small text-muted">{{ __('account.package_availability.summary.inventory_type') }}</label>
                                    <select name="inventory_type" id="inventory_type" class="form-select form-select-sm">
                                        @foreach (['unlimited', 'per_day', 'per_timeslot', 'per_departure'] as $type)
                                            <option value="{{ $type }}" @selected(old('inventory_type', $catalog->inventory_type ?? 'unlimited') === $type)>
                                                {{ __('wizard.variant_inventory.' . $type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="inventory_total" class="form-label small text-muted">{{ __('account.package_availability.summary.inventory_total') }}</label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="inventory_total"
                                        id="inventory_total"
                                        class="form-control form-control-sm"
                                        value="{{ old('inventory_total', $catalog->inventory_total !== null ? (string) $catalog->inventory_total : '') }}"
                                    >
                                </div>
                                <div class="col-md-5">
                                    <p class="small text-muted mb-2">{{ __('account.package_availability.summary.note_text') }}</p>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('account.package_availability.update_inventory_button') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 bg-white">
                            <h6 class="mb-0">{{ __('account.package_availability.rules_heading') }}</h6>
                            <a href="{{ route('account.package-availability.rules.create', $catalog) }}" class="btn btn-sm btn-primary">
                                {{ __('account.package_availability.rules_create_button') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($catalog->availabilityRules->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.package_availability.rules_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.package_availability.rules_columns.validity') }}</th>
                                                <th>{{ __('account.package_availability.rules_columns.weekdays') }}</th>
                                                <th>{{ __('account.package_availability.rules_columns.active') }}</th>
                                                <th class="text-end">{{ __('account.package_availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($catalog->availabilityRules as $rule)
                                                <tr>
                                                    <td>
                                                        @include('account.package-availability.operator.partials.date-range', [
                                                            'start' => $rule->start_date,
                                                            'end' => $rule->end_date,
                                                        ])
                                                    </td>
                                                    <td>{{ WeekdayMask::label($rule->weekday_mask) }}</td>
                                                    <td>
                                                        @if ($rule->active)
                                                            <span class="badge bg-success-subtle text-success">{{ __('account.package_availability.active_yes') }}</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary">{{ __('account.package_availability.active_no') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('account.package-availability.rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.package_availability.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.package-availability.rules.destroy', $rule) }}" class="d-inline" onsubmit="return confirm(@json(__('account.package_availability.rules_delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.package_availability.delete_button') }}</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @if ($catalog->usesTimeSlotInventory() && $rule->timeSlots->isNotEmpty())
                                                    <tr>
                                                        <td colspan="4" class="pt-0 pb-3">
                                                            <ul class="list-unstyled small text-muted mb-0 ps-2 border-start">
                                                                @foreach ($rule->timeSlots as $slot)
                                                                    <li>
                                                                        {{ substr((string) $slot->start_time, 0, 5) }}–{{ substr((string) $slot->end_time, 0, 5) }}
                                                                        @if ($slot->capacity !== null)
                                                                            · {{ __('account.package_availability.slot_capacity', ['count' => number_format((int) $slot->capacity)]) }}
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
                            <h6 class="mb-0">{{ __('account.package_availability.overrides_heading') }}</h6>
                            <a href="{{ route('account.package-availability.overrides.create', $catalog) }}" class="btn btn-sm btn-primary">
                                {{ __('account.package_availability.overrides_create_button') }}
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($catalog->availabilityOverrides->isEmpty())
                                <p class="text-muted mb-0">{{ __('account.package_availability.overrides_empty') }}</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.package_availability.overrides_columns.date') }}</th>
                                                <th>{{ __('account.package_availability.overrides_columns.time') }}</th>
                                                <th>{{ __('account.package_availability.overrides_columns.capacity') }}</th>
                                                <th class="text-end">{{ __('account.package_availability.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($catalog->availabilityOverrides as $override)
                                                <tr>
                                                    <td>{{ $override->date ? locale_date($override->date) : '—' }}</td>
                                                    <td>
                                                        @if ($override->start_time)
                                                            {{ substr((string) $override->start_time, 0, 5) }}
                                                        @else
                                                            <span class="text-muted">{{ __('account.package_availability.whole_day') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($override->closed)
                                                            <span class="badge bg-danger-subtle text-danger">{{ __('account.package_availability.closed_badge') }}</span>
                                                        @elseif ($override->capacity !== null)
                                                            {{ number_format((int) $override->capacity) }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('account.package-availability.overrides.edit', $override) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.package_availability.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.package-availability.overrides.destroy', $override) }}" class="d-inline" onsubmit="return confirm(@json(__('account.package_availability.overrides_delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('account.package_availability.delete_button') }}</button>
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
