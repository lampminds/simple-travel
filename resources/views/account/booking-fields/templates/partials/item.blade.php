@php
    $selectedId = (string) ($row['provider_booking_field_id'] ?? '');
    $selected = $fields->first(static fn ($field): bool => (string) $field->id === $selectedId);
@endphp
<div class="card mb-3 booking-order-row">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <input type="hidden" class="booking-order-input" name="items[{{ $index }}][sort_order]" value="{{ $row['sort_order'] ?? 10 }}">
                @include('account.booking-fields.partials.order-buttons')
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm booking-template-item-remove">
                {{ __('account.booking_fields.templates.remove_item') }}
            </button>
        </div>

        <div class="row g-3 align-items-end">
            <div class="col-lg-7">
                <label class="form-label" for="item-field-{{ $index }}">{{ __('account.booking_fields.col_label') }}</label>
                <select
                    id="item-field-{{ $index }}"
                    name="items[{{ $index }}][provider_booking_field_id]"
                    class="form-select booking-template-field @error('items.'.$index.'.provider_booking_field_id') is-invalid @enderror"
                    required
                >
                    <option value="">{{ __('account.booking_fields.templates.choose_field') }}</option>
                    @foreach ($fields as $field)
                        @php
                            $optionLabel = $field->labelForDisplay();
                            $optionLabel = $optionLabel !== '' ? $optionLabel : __('account.booking_fields.untitled');
                        @endphp
                        <option
                            value="{{ $field->id }}"
                            data-summary="{{ $field->inputTypeLabel() }} · {{ $field->scopeLabel() }}"
                            @selected($selectedId === (string) $field->id)
                        >
                            {{ $optionLabel }}@if (! $field->is_active) ({{ __('account.booking_fields.inactive') }})@endif
                        </option>
                    @endforeach
                </select>
                @error('items.'.$index.'.provider_booking_field_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-lg-3">
                <p class="form-text mb-2 booking-template-inherited">
                    @if ($selected)
                        {{ $selected->inputTypeLabel() }} · {{ $selected->scopeLabel() }}
                    @endif
                </p>
            </div>
            <div class="col-lg-2">
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
    </div>
</div>
