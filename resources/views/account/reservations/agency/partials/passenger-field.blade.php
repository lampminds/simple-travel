@php
    $current = '';
    if ($passengerIndex !== null) {
        $bucket = $passengerFieldValues[$field->id] ?? $passengerFieldValues[(string) $field->id] ?? [];
        $current = is_array($bucket) ? (string) ($bucket[$passengerIndex] ?? '') : '';
    }
    $inputName = 'passenger_fields['.$field->id.'][]';
    $errorKey = $passengerIndex === null ? null : 'passenger_fields.'.$field->id.'.'.$passengerIndex;
@endphp
<div class="col-md-4">
    <label class="form-label">
        {{ $field->label }}
        @if ($field->required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @if ($field->inputType === \App\Models\ProviderBookingField::TYPE_TEXTAREA)
        <textarea
            name="{{ $inputName }}"
            rows="2"
            class="form-control {{ ($showErrors && $errorKey && $errors->has($errorKey)) ? 'is-invalid' : '' }}"
            maxlength="2000"
            @if ($field->required) required @endif
        >{{ $current }}</textarea>
    @elseif ($field->inputType === \App\Models\ProviderBookingField::TYPE_COUNTRY)
        <select
            name="{{ $inputName }}"
            class="form-select {{ ($showErrors && $errorKey && $errors->has($errorKey)) ? 'is-invalid' : '' }}"
            @if ($field->required) required @endif
        >
            <option value="">{{ __('account.booking_fields.country_placeholder') }}</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}" @selected($current !== '' && (string) $current === (string) $country->id)>{{ $country->name }}</option>
            @endforeach
        </select>
    @else
        <input
            type="{{ match ($field->inputType) {
                \App\Models\ProviderBookingField::TYPE_DATE => 'date',
                \App\Models\ProviderBookingField::TYPE_TIME => 'time',
                \App\Models\ProviderBookingField::TYPE_PHONE => 'tel',
                default => 'text',
            } }}"
            name="{{ $inputName }}"
            class="form-control {{ ($showErrors && $errorKey && $errors->has($errorKey)) ? 'is-invalid' : '' }}"
            value="{{ $current }}"
            @if ($field->inputType === \App\Models\ProviderBookingField::TYPE_PHONE) maxlength="40" @elseif ($field->inputType === \App\Models\ProviderBookingField::TYPE_TEXT) maxlength="255" @endif
            @if ($field->required) required @endif
        >
    @endif
    @if ($field->help)
        <div class="form-text">{{ $field->help }}</div>
    @endif
    @if ($showErrors && $errorKey && $errors->has($errorKey))
        <div class="invalid-feedback d-block">{{ $errors->first($errorKey) }}</div>
    @endif
</div>
