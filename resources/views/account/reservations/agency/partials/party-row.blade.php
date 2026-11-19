@php
    $selectedAge = (string) ($partyRow['age'] ?? '');
    $selectedQuantity = (int) ($partyRow['quantity'] ?? 1);
    if ($selectedQuantity < 1) {
        $selectedQuantity = 1;
    }
@endphp
<div class="border rounded p-3 mb-2">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="party-age-{{ $index }}">{{ __('account.reservations.field_age') }}</label>
            <select
                id="party-age-{{ $index }}"
                name="party_rows[{{ $index }}][age]"
                class="form-select @error('party_rows.'.$index.'.age') is-invalid @enderror"
            >
                <option value="">{{ __('account.reservations.age_placeholder') }}</option>
                @for ($age = 0; $age <= \App\Support\BookingPassengersSnapshot::PARTY_AGE_MAX; $age++)
                    <option value="{{ $age }}" @selected($selectedAge !== '' && (int) $selectedAge === $age)>{{ $age }}</option>
                @endfor
            </select>
            @error('party_rows.'.$index.'.age')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label" for="party-quantity-{{ $index }}">{{ __('account.reservations.field_quantity') }}</label>
            <select
                id="party-quantity-{{ $index }}"
                name="party_rows[{{ $index }}][quantity]"
                class="form-select @error('party_rows.'.$index.'.quantity') is-invalid @enderror"
            >
                @for ($quantity = 1; $quantity <= \App\Support\BookingPassengersSnapshot::MAX_PASSENGERS; $quantity++)
                    <option value="{{ $quantity }}" @selected($selectedQuantity === $quantity)>{{ $quantity }}</option>
                @endfor
            </select>
            @error('party_rows.'.$index.'.quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @if (is_array($rowQuote))
            <div class="col-md-5">
                <div class="small text-muted">{{ __('account.reservations.row_base') }}</div>
                <div>{{ $rowQuote['base_formatted'] }}</div>
                @if ($rowQuote['has_discount'] ?? false)
                    <div class="small text-muted mt-1">{{ __('account.reservations.row_discount') }}</div>
                    <div>−{{ $rowQuote['discount_formatted'] }}</div>
                @endif
            </div>
        @endif
    </div>
</div>
