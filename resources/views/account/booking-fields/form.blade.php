@extends('layouts.base', ['title' => $field ? __('account.booking_fields.edit_page_title') : __('account.booking_fields.create_page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <x-account-page-header
                :title="$field ? __('account.booking_fields.edit_heading') : __('account.booking_fields.create_heading')"
                :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                :instructions="__('account.booking_fields.form_instructions')"
            />

            <form method="POST" action="{{ $field ? route('account.booking-fields.update', $field) : route('account.booking-fields.store') }}" class="card mt-4">
                @csrf
                @if ($field)
                    @method('PUT')
                @endif
                <div class="card-body">
                    @error('translations')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    @foreach ($languages as $language)
                        @php
                            $stored = $translations[$language->id] ?? [];
                        @endphp
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-lg-2">
                                <div class="form-label mb-lg-2">{{ $language->display_name }}</div>
                                <input type="hidden" name="translations[{{ $language->id }}][language_id]" value="{{ $language->id }}">
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label" for="field-label-{{ $language->id }}">{{ __('account.booking_fields.label') }}</label>
                                <input
                                    type="text"
                                    id="field-label-{{ $language->id }}"
                                    name="translations[{{ $language->id }}][label]"
                                    class="form-control @error('translations.'.$language->id.'.label') is-invalid @enderror"
                                    value="{{ old('translations.'.$language->id.'.label', $stored['label'] ?? '') }}"
                                    maxlength="120"
                                >
                                @error('translations.'.$language->id.'.label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label" for="field-help-{{ $language->id }}">{{ __('account.booking_fields.help') }}</label>
                                <input
                                    type="text"
                                    id="field-help-{{ $language->id }}"
                                    name="translations[{{ $language->id }}][help_text]"
                                    class="form-control @error('translations.'.$language->id.'.help_text') is-invalid @enderror"
                                    value="{{ old('translations.'.$language->id.'.help_text', $stored['help_text'] ?? '') }}"
                                    maxlength="255"
                                >
                                @error('translations.'.$language->id.'.help_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endforeach

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label" for="field-scope">{{ __('account.booking_fields.scope') }}</label>
                            <select id="field-scope" name="scope" class="form-select @error('scope') is-invalid @enderror" required>
                                @foreach (\App\Models\ProviderBookingField::scopes() as $scope)
                                    <option value="{{ $scope }}" @selected(old('scope', $field?->scope ?? \App\Models\ProviderBookingField::SCOPE_PASSENGER) === $scope)>
                                        {{ __('account.booking_fields.scope_'.$scope) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('scope')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="field-type">{{ __('account.booking_fields.input_type') }}</label>
                            <select id="field-type" name="input_type" class="form-select @error('input_type') is-invalid @enderror" required>
                                @foreach (\App\Models\ProviderBookingField::inputTypes() as $inputType)
                                    <option value="{{ $inputType }}" @selected(old('input_type', $field?->input_type ?? \App\Models\ProviderBookingField::TYPE_TEXT) === $inputType)>
                                        {{ __('account.booking_fields.type_'.$inputType) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('input_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                        <a class="btn btn-outline-secondary" href="{{ route('account.booking-fields.index') }}">{{ __('account.booking_fields.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('account.booking_fields.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
