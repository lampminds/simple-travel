<div class="border rounded p-3 mb-2 passenger-age-row">
    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
            <label class="form-label">{{ __('account.reservations.field_age') }}</label>
            <input
                type="number"
                name="ages[]"
                class="form-control {{ ($showErrors && ($errors->has('ages.'.$passengerIndex) || $errors->has('ages'))) ? 'is-invalid' : '' }}"
                value="{{ $ageValue }}"
                min="0"
                max="120"
                required
            >
            @if ($showErrors && $errors->has('ages.'.$passengerIndex))
                <div class="invalid-feedback">{{ $errors->first('ages.'.$passengerIndex) }}</div>
            @endif
        </div>
        @foreach ($passengerFields as $field)
            @include('account.reservations.agency.partials.passenger-field', [
                'field' => $field,
                'passengerIndex' => $showErrors ? $passengerIndex : null,
                'passengerFieldValues' => $passengerFieldValues,
                'countries' => $countries,
                'showErrors' => $showErrors,
            ])
        @endforeach
        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary remove-passenger-age">
                {{ __('account.reservations.remove_passenger') }}
            </button>
        </div>
    </div>
</div>
