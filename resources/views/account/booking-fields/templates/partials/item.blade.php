@php
    $itemId = $row['id'] ?? null;
@endphp
<div class="card mb-3 booking-order-row">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <input type="hidden" class="booking-order-input" name="items[{{ $index }}][sort_order]" value="{{ $row['sort_order'] ?? 10 }}">
                @if ($itemId !== null && $itemId !== '')
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $itemId }}">
                @endif
                @include('account.booking-fields.partials.order-buttons')
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm booking-template-item-remove">
                {{ __('account.booking_fields.templates.remove_item') }}
            </button>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="item-scope-{{ $index }}">{{ __('account.booking_fields.scope') }}</label>
                <select id="item-scope-{{ $index }}" name="items[{{ $index }}][scope]" class="form-select @error('items.'.$index.'.scope') is-invalid @enderror" required>
                    @foreach (\App\Models\ProviderBookingField::scopes() as $scope)
                        <option value="{{ $scope }}" @selected((string) ($row['scope'] ?? '') === $scope)>
                            {{ __('account.booking_fields.scope_'.$scope) }}
                        </option>
                    @endforeach
                </select>
                @error('items.'.$index.'.scope')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="item-type-{{ $index }}">{{ __('account.booking_fields.input_type') }}</label>
                <select id="item-type-{{ $index }}" name="items[{{ $index }}][input_type]" class="form-select @error('items.'.$index.'.input_type') is-invalid @enderror" required>
                    @foreach (\App\Models\ProviderBookingField::inputTypes() as $inputType)
                        <option value="{{ $inputType }}" @selected((string) ($row['input_type'] ?? '') === $inputType)>
                            {{ __('account.booking_fields.type_'.$inputType) }}
                        </option>
                    @endforeach
                </select>
                @error('items.'.$index.'.input_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="item-required-{{ $index }}"
                        name="items[{{ $index }}][is_required]"
                        value="1"
                        @checked(! empty($row['is_required']))
                    >
                    <label class="form-check-label" for="item-required-{{ $index }}">{{ __('account.booking_fields.required') }}</label>
                </div>
            </div>
        </div>

        @foreach ($languages as $language)
            @php
                $stored = $row['translations'][$language->id] ?? $row['translations'][(string) $language->id] ?? [];
            @endphp
            <div class="row g-2 align-items-end mt-2">
                <div class="col-lg-2">
                    <div class="form-label mb-lg-2">{{ $language->display_name }}</div>
                    <input type="hidden" name="items[{{ $index }}][translations][{{ $language->id }}][language_id]" value="{{ $language->id }}">
                </div>
                <div class="col-lg-5">
                    <label class="form-label" for="item-label-{{ $index }}-{{ $language->id }}">{{ __('account.booking_fields.label') }}</label>
                    <input
                        type="text"
                        id="item-label-{{ $index }}-{{ $language->id }}"
                        name="items[{{ $index }}][translations][{{ $language->id }}][label]"
                        class="form-control"
                        value="{{ $stored['label'] ?? '' }}"
                        maxlength="120"
                    >
                </div>
                <div class="col-lg-5">
                    <label class="form-label" for="item-help-{{ $index }}-{{ $language->id }}">{{ __('account.booking_fields.help') }}</label>
                    <input
                        type="text"
                        id="item-help-{{ $index }}-{{ $language->id }}"
                        name="items[{{ $index }}][translations][{{ $language->id }}][help_text]"
                        class="form-control"
                        value="{{ $stored['help_text'] ?? '' }}"
                        maxlength="255"
                    >
                </div>
            </div>
        @endforeach

        @error('items.'.$index.'.translations')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>
