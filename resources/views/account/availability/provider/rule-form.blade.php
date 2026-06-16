@php
    use App\Support\WeekdayMask;

    $isEdit = $rule !== null;
    $serviceName = trim($variant->service?->name ?? '');
    $variantLabel = trim($variant->name ?? '') !== '' ? $variant->name : $variant->sku;
    $selectedWeekdayBits = array_map('intval', $selectedWeekdayBits ?? array_keys(WeekdayMask::DAY_BITS));
    $startDate = old('start_date', $isEdit && $rule->start_date ? normalize_form_date_value($rule->start_date) : '');
    $endDate = old('end_date', $isEdit && $rule->end_date ? normalize_form_date_value($rule->end_date) : '');
    $isActive = old('active', $isEdit ? $rule->active : true);
    $timeSlots = is_array($timeSlots) ? $timeSlots : [];
    if ($timeSlots === [] && $variant->usesTimeSlotInventory()) {
        $timeSlots = [['start_time' => '', 'end_time' => '', 'capacity' => '', 'cutoff_minutes' => '', 'active' => true]];
    }
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.availability.rule_edit_page_title') : __('account.availability.rule_create_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="$isEdit ? __('account.availability.rule_edit_heading') : __('account.availability.rule_create_heading')"
                        :subtitle="$serviceName !== '' ? $serviceName . ' — ' . $variantLabel : $variantLabel"
                        :instructions="__('account.availability.rule_form_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ $submitRoute }}">
                                @csrf
                                @if ($submitMethod !== 'POST')
                                    @method($submitMethod)
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label">{{ __('account.availability.fields.start_date') }}</label>
                                        <x-locale-date-input name="start_date" id="start_date" :value="$startDate" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">{{ __('account.availability.fields.end_date') }}</label>
                                        <x-locale-date-input name="end_date" id="end_date" :value="$endDate" />
                                        <div class="form-text">{{ __('account.availability.fields.validity_help') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4 pt-2">
                                            <input type="hidden" name="active" value="0">
                                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" @checked(filter_var($isActive, FILTER_VALIDATE_BOOLEAN))>
                                            <label class="form-check-label" for="active">{{ __('account.availability.fields.active') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label d-block">{{ __('account.availability.fields.weekdays') }}</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach (WeekdayMask::DAY_BITS as $bit => $suffix)
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="weekday_bits[]"
                                                        id="weekday_{{ $bit }}"
                                                        value="{{ $bit }}"
                                                        @checked(in_array($bit, $selectedWeekdayBits, true))
                                                    >
                                                    <label class="form-check-label" for="weekday_{{ $bit }}">
                                                        {{ __('account.availability.weekdays.' . $suffix) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">{{ __('account.availability.fields.weekdays_help') }}</div>
                                    </div>

                                    @if ($variant->usesTimeSlotInventory())
                                        <div class="col-12">
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">{{ __('account.availability.fields.time_slots') }}</label>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="add-time-slot">
                                                    {{ __('account.availability.add_time_slot') }}
                                                </button>
                                            </div>
                                            <div id="time-slots-wrap">
                                                @foreach ($timeSlots as $index => $slot)
                                                    @php
                                                        $slot = is_array($slot) ? $slot : [];
                                                    @endphp
                                                    <div class="row g-2 align-items-end mb-2 time-slot-row" data-index="{{ $index }}">
                                                        <div class="col-md-2">
                                                            <label class="form-label small">{{ __('account.availability.fields.slot_start') }}</label>
                                                            <input type="time" name="time_slots[{{ $index }}][start_time]" class="form-control form-control-sm" value="{{ $slot['start_time'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">{{ __('account.availability.fields.slot_end') }}</label>
                                                            <input type="time" name="time_slots[{{ $index }}][end_time]" class="form-control form-control-sm" value="{{ $slot['end_time'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">{{ __('account.availability.fields.slot_capacity') }}</label>
                                                            <input type="number" min="0" name="time_slots[{{ $index }}][capacity]" class="form-control form-control-sm" value="{{ $slot['capacity'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label small">{{ __('account.availability.fields.slot_cutoff') }}</label>
                                                            <input type="number" min="0" name="time_slots[{{ $index }}][cutoff_minutes]" class="form-control form-control-sm" value="{{ $slot['cutoff_minutes'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check mb-2">
                                                                <input type="hidden" name="time_slots[{{ $index }}][active]" value="0">
                                                                <input class="form-check-input" type="checkbox" name="time_slots[{{ $index }}][active]" value="1" @checked(filter_var($slot['active'] ?? true, FILTER_VALIDATE_BOOLEAN))>
                                                                <label class="form-check-label small">{{ __('account.availability.fields.slot_active') }}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-time-slot">{{ __('account.availability.remove_time_slot') }}</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    <a class="btn btn-light" href="{{ $cancelRoute }}">{{ __('account.availability.cancel_button') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('account.availability.update_button') : __('account.availability.save_button') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($variant->usesTimeSlotInventory())
        <template id="time-slot-template">
            <div class="row g-2 align-items-end mb-2 time-slot-row">
                <div class="col-md-2">
                    <label class="form-label small">{{ __('account.availability.fields.slot_start') }}</label>
                    <input type="time" data-name="start_time" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">{{ __('account.availability.fields.slot_end') }}</label>
                    <input type="time" data-name="end_time" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">{{ __('account.availability.fields.slot_capacity') }}</label>
                    <input type="number" min="0" data-name="capacity" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">{{ __('account.availability.fields.slot_cutoff') }}</label>
                    <input type="number" min="0" data-name="cutoff_minutes" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <div class="form-check mb-2">
                        <input type="hidden" data-name="active" value="0">
                        <input class="form-check-input" type="checkbox" data-name="active" value="1" checked>
                        <label class="form-check-label small">{{ __('account.availability.fields.slot_active') }}</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-time-slot">{{ __('account.availability.remove_time_slot') }}</button>
                </div>
            </div>
        </template>
        <script>
            (function () {
                const wrap = document.getElementById('time-slots-wrap');
                const template = document.getElementById('time-slot-template');
                const addBtn = document.getElementById('add-time-slot');
                if (!wrap || !template || !addBtn) {
                    return;
                }

                function reindexRows() {
                    wrap.querySelectorAll('.time-slot-row').forEach(function (row, index) {
                        row.querySelectorAll('[data-name]').forEach(function (input) {
                            const field = input.getAttribute('data-name');
                            input.name = 'time_slots[' + index + '][' + field + ']';
                        });
                        row.querySelectorAll('[name^="time_slots["]').forEach(function (input) {
                            const match = input.name.match(/\[(\w+)\]$/);
                            if (!match) {
                                return;
                            }
                            input.name = 'time_slots[' + index + '][' + match[1] + ']';
                        });
                    });
                }

                addBtn.addEventListener('click', function () {
                    const clone = template.content.cloneNode(true);
                    wrap.appendChild(clone);
                    reindexRows();
                });

                wrap.addEventListener('click', function (event) {
                    const btn = event.target.closest('.remove-time-slot');
                    if (!btn) {
                        return;
                    }
                    const row = btn.closest('.time-slot-row');
                    if (row && wrap.querySelectorAll('.time-slot-row').length > 1) {
                        row.remove();
                        reindexRows();
                    }
                });
            })();
        </script>
    @endif

    <x-site-footer-simple />
@endsection
