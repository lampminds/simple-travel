<div class="border rounded p-3 client-org-tax-row" data-tax-row>
    @if (! empty($row['id']))
        <input type="hidden" name="tax_ids[{{ $idx }}][id]" value="{{ $row['id'] }}">
    @endif

    <div class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">{{ __('account.clients.fields.tax_id_type') }}</label>
            <select
                class="form-select @error('tax_ids.'.$idx.'.document_id') is-invalid @enderror"
                name="tax_ids[{{ $idx }}][document_id]"
            >
                <option value="">{{ __('account.clients.fields.tax_id_type_placeholder') }}</option>
                @foreach ($taxIdCategories as $category)
                    <option value="{{ $category->id }}" @selected((string) ($row['document_id'] ?? '') === (string) $category->id)>
                        {{ $category->name !== '' ? $category->name : $category->getRawOriginal('code') }}
                    </option>
                @endforeach
            </select>
            @error('tax_ids.'.$idx.'.document_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-5">
            <label class="form-label">{{ __('account.clients.fields.tax_id_value') }}</label>
            <input
                type="text"
                class="form-control @error('tax_ids.'.$idx.'.value') is-invalid @enderror"
                name="tax_ids[{{ $idx }}][value]"
                value="{{ $row['value'] ?? '' }}"
                maxlength="255"
            >
            @error('tax_ids.'.$idx.'.value')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-2">
            <div class="form-check mb-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="tax-delete-{{ $idx }}"
                    name="tax_ids[{{ $idx }}][delete]"
                    value="1"
                    @checked((bool) ($row['delete'] ?? false))
                >
                <label class="form-check-label" for="tax-delete-{{ $idx }}">
                    {{ __('account.clients.remove_tax_id') }}
                </label>
            </div>
        </div>
    </div>
</div>
