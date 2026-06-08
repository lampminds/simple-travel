@php
    $isEdit = $vehicleType !== null;
@endphp

@extends('layouts.base', ['title' => $isEdit ? __('account.transfer_vehicle_types.edit_page_title') : __('account.transfer_vehicle_types.create_page_title')])

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
                        :title="$isEdit ? __('account.transfer_vehicle_types.edit_title') : __('account.transfer_vehicle_types.create_heading')"
                        :subtitle="$isEdit ? $vehicleType->name : null"
                        :instructions="__('account.transfer_vehicle_types.form_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-10 col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ $submitRoute }}">
                                @csrf
                                @if ($submitMethod !== 'POST')
                                    @method($submitMethod)
                                @endif

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="tvt-name">{{ __('account.transfer_vehicle_types.fields.name') }}</label>
                                        <input
                                            type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="tvt-name"
                                            name="name"
                                            value="{{ old('name', $vehicleType?->name) }}"
                                            required
                                            maxlength="255"
                                        >
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="tvt-code">{{ __('account.transfer_vehicle_types.fields.code') }}</label>
                                        <input
                                            type="text"
                                            class="form-control @error('code') is-invalid @enderror"
                                            id="tvt-code"
                                            name="code"
                                            value="{{ old('code', $vehicleType?->code) }}"
                                            maxlength="120"
                                        >
                                        <div class="form-text">{{ __('account.transfer_vehicle_types.fields.code_hint') }}</div>
                                        @error('code')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="tvt-category">{{ __('account.transfer_vehicle_types.fields.category') }}</label>
                                        <select
                                            class="form-select @error('service_transfer_vehicle_type_category_id') is-invalid @enderror"
                                            id="tvt-category"
                                            name="service_transfer_vehicle_type_category_id"
                                        >
                                            <option value="">{{ __('account.transfer_vehicle_types.fields.category_placeholder') }}</option>
                                            @foreach ($categories as $cat)
                                                <option
                                                    value="{{ (int) $cat->id }}"
                                                    @selected((string) old('service_transfer_vehicle_type_category_id', $vehicleType?->service_transfer_vehicle_type_category_id) === (string) $cat->id)
                                                >
                                                    {{ $cat->name !== '' ? $cat->name : $cat->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_transfer_vehicle_type_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label" for="tvt-max-pax">{{ __('account.transfer_vehicle_types.fields.max_passengers') }}</label>
                                        <input
                                            type="number"
                                            class="form-control @error('max_passengers') is-invalid @enderror"
                                            id="tvt-max-pax"
                                            name="max_passengers"
                                            value="{{ old('max_passengers', $vehicleType?->max_passengers) }}"
                                            min="0"
                                            max="500"
                                            step="1"
                                        >
                                        @error('max_passengers')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="tvt-max-lug">{{ __('account.transfer_vehicle_types.fields.max_luggage') }}</label>
                                        <input
                                            type="number"
                                            class="form-control @error('max_luggage') is-invalid @enderror"
                                            id="tvt-max-lug"
                                            name="max_luggage"
                                            value="{{ old('max_luggage', $vehicleType?->max_luggage) }}"
                                            min="0"
                                            max="500"
                                            step="1"
                                        >
                                        @error('max_luggage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="border rounded bg-white p-3">
                                            <div class="form-check form-switch m-0">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="tvt-active"
                                                    name="active"
                                                    value="1"
                                                    @checked(old('active', $vehicleType?->active ?? true))
                                                >
                                                <label class="form-check-label" for="tvt-active">{{ __('account.transfer_vehicle_types.fields.active') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 justify-content-between mt-4">
                                    <a href="{{ $cancelRoute }}" class="btn btn-outline-secondary">{{ __('account.transfer_vehicle_types.cancel_button') }}</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $isEdit ? __('account.transfer_vehicle_types.update_button') : __('account.transfer_vehicle_types.save_button') }}
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
