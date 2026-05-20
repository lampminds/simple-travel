@php
    $index = $index ?? 0;
    $item = $item ?? [];
    $packageItemOptions = $packageItemOptions ?? [];
    $selectedPackageItemId = (string) ($item['operator_package_item_id'] ?? '');
    $pricingMode = (string) ($item['pricing_mode'] ?? 'direct');
@endphp
<tr class="price-list-item-row" data-role="price-list-item-row">
    <td>
        <select
            name="items[{{ $index }}][operator_package_item_id]"
            class="form-select @error('items.'.$index.'.operator_package_item_id') is-invalid @enderror"
            data-role="package-item-select"
            required
        >
            <option value="">{{ __('account.operator_price_lists.fields.package_item_placeholder') }}</option>
            @foreach ($packageItemOptions as $optionId => $optionLabel)
                <option value="{{ $optionId }}" @selected($selectedPackageItemId === (string) $optionId)>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
        @error('items.'.$index.'.operator_package_item_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <div class="alert alert-warning py-1 px-2 mt-1 mb-0 small d-none" data-role="item-warning" role="alert"></div>
    </td>
    <td>
        <select
            name="items[{{ $index }}][pricing_mode]"
            class="form-select @error('items.'.$index.'.pricing_mode') is-invalid @enderror"
            data-role="pricing-mode-select"
            required
        >
            <option value="percentage" @selected($pricingMode === 'percentage')>{{ __('account.operator_price_lists.item_pricing_mode.percentage') }}</option>
            <option value="fixed_delta" @selected($pricingMode === 'fixed_delta')>{{ __('account.operator_price_lists.item_pricing_mode.fixed_delta') }}</option>
            <option value="direct" @selected($pricingMode === 'direct')>{{ __('account.operator_price_lists.item_pricing_mode.direct') }}</option>
        </select>
        @error('items.'.$index.'.pricing_mode')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td>
        <input
            type="number"
            name="items[{{ $index }}][price]"
            class="form-control @error('items.'.$index.'.price') is-invalid @enderror"
            data-role="item-price"
            step="{{ $priceStep }}"
            required
            value="{{ $item['price'] ?? '' }}"
        >
        @error('items.'.$index.'.price')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted" data-role="item-price-help"></small>
    </td>
    <td class="text-end" data-role="provider-cost-cell">—</td>
    <td class="text-end fw-semibold" data-role="final-price-cell">—</td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
            {{ __('account.price_lists.remove_item_button') }}
        </button>
    </td>
</tr>
