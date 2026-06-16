@php
    use App\Support\WeekdayMask;

    $isEdit = $rule !== null;
    $serviceName = trim($service->name ?? '');
    if ($serviceName === '') {
        $serviceName = '#' . $service->id;
    }
    $selectedWeekdayBits = array_map('intval', $selectedWeekdayBits ?? array_keys(WeekdayMask::DAY_BITS));
    $startDate = old('start_date', $isEdit && $rule->start_date ? normalize_form_date_value($rule->start_date) : '');
    $endDate = old('end_date', $isEdit && $rule->end_date ? normalize_form_date_value($rule->end_date) : '');
    $isActive = old('active', $isEdit ? $rule->active : true);
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.availability.service_rule_edit_page_title') : __('account.availability.service_rule_create_page_title')])

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
                        :title="$isEdit ? __('account.availability.service_rule_edit_heading') : __('account.availability.service_rule_create_heading')"
                        :subtitle="$serviceName"
                        :instructions="__('account.availability.service_rule_form_instructions')"
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

    <x-site-footer-simple />
@endsection
