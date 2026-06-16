@php
    $isEdit = $override !== null;
    $serviceName = trim($service->name ?? '');
    if ($serviceName === '') {
        $serviceName = '#' . $service->id;
    }
    $dateValue = old('date', $isEdit && $override->date ? normalize_form_date_value($override->date) : '');
    $endDateValue = old('end_date', $isEdit && $override->end_date ? normalize_form_date_value($override->end_date) : '');
    $reasonValue = old('reason', $isEdit ? (string) ($override->reason ?? '') : '');
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.availability.service_override_edit_page_title') : __('account.availability.service_override_create_page_title')])

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
                        :title="$isEdit ? __('account.availability.service_override_edit_heading') : __('account.availability.service_override_create_heading')"
                        :subtitle="$serviceName"
                        :instructions="__('account.availability.service_override_form_instructions')"
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

                                <input type="hidden" name="closed" value="1">

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="date" class="form-label">{{ __('account.availability.fields.override_start_date') }}</label>
                                        <x-locale-date-input name="date" id="date" :value="$dateValue" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">{{ __('account.availability.fields.override_end_date') }}</label>
                                        <x-locale-date-input name="end_date" id="end_date" :value="$endDateValue" />
                                        <div class="form-text">{{ __('account.availability.fields.override_end_date_help') }}</div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <p class="small text-muted mb-2">{{ __('account.availability.service_closure_only_hint') }}</p>
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

    <x-site-footer-simple />
@endsection
