@php
    $isEdit = $override !== null;
    $serviceName = trim($variant->service?->name ?? '');
    $variantLabel = trim($variant->name ?? '') !== '' ? $variant->name : $variant->sku;
    $dateValue = old('date', $isEdit && $override->date ? normalize_form_date_value($override->date) : '');
    $startTime = old('start_time', $isEdit && $override->start_time ? substr((string) $override->start_time, 0, 5) : '');
    $capacityValue = old('capacity', $isEdit && $override->capacity !== null ? (string) $override->capacity : '');
    $isClosed = old('closed', $isEdit ? $override->closed : false);
    $reasonValue = old('reason', $isEdit ? (string) ($override->reason ?? '') : '');
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.availability.override_edit_page_title') : __('account.availability.override_create_page_title')])

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
                        :title="$isEdit ? __('account.availability.override_edit_heading') : __('account.availability.override_create_heading')"
                        :subtitle="$serviceName !== '' ? $serviceName . ' — ' . $variantLabel : $variantLabel"
                        :instructions="__('account.availability.override_form_instructions')"
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
                                        <label for="date" class="form-label">{{ __('account.availability.fields.override_date') }}</label>
                                        <x-locale-date-input name="date" id="date" :value="$dateValue" />
                                    </div>

                                    @if ($variant->usesTimeSlotInventory())
                                        <div class="col-md-4">
                                            <label for="start_time" class="form-label">{{ __('account.availability.fields.override_start_time') }}</label>
                                            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ $startTime }}">
                                            <div class="form-text">{{ __('account.availability.fields.override_start_time_help') }}</div>
                                        </div>
                                    @endif

                                    <div class="col-md-4" id="capacity-field-wrap">
                                        <label for="capacity" class="form-label">{{ __('account.availability.fields.override_capacity') }}</label>
                                        <input type="number" min="0" name="capacity" id="capacity" class="form-control" value="{{ $capacityValue }}">
                                        <div class="form-text">{{ __('account.availability.fields.override_capacity_help') }}</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="closed" value="0">
                                            <input class="form-check-input" type="checkbox" name="closed" id="closed" value="1" @checked(filter_var($isClosed, FILTER_VALIDATE_BOOLEAN))>
                                            <label class="form-check-label" for="closed">{{ __('account.availability.fields.closed') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="reason" class="form-label">{{ __('account.availability.fields.reason') }}</label>
                                        <input type="text" name="reason" id="reason" class="form-control" maxlength="255" value="{{ $reasonValue }}">
                                    </div>
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

    <script>
        (function () {
            const closedInput = document.getElementById('closed');
            const capacityWrap = document.getElementById('capacity-field-wrap');
            const capacityInput = document.getElementById('capacity');
            if (!closedInput || !capacityWrap || !capacityInput) {
                return;
            }

            function refresh() {
                const isClosed = closedInput.checked;
                capacityWrap.style.display = isClosed ? 'none' : '';
                if (isClosed) {
                    capacityInput.value = '';
                }
            }

            closedInput.addEventListener('change', refresh);
            refresh();
        })();
    </script>

    <x-site-footer-simple />
@endsection
