@php
    $index = $index ?? 0;
    $item = $item ?? [];
    $providerId = (string) ($item['provider_id'] ?? '');
    $offerId = (string) ($item['service_offer_id'] ?? '');
    $offers = $offersByProvider[$providerId] ?? $offersByProvider[(int) $providerId] ?? [];
@endphp
<tr class="package-item-row">
    <td>
        <select name="items[{{ $index }}][provider_id]" class="form-select package-item-provider" required>
            <option value="">{{ __('account.operator_packages.fields.provider_placeholder') }}</option>
            @foreach ($providerOptions as $id => $label)
                <option value="{{ $id }}" @selected($providerId === (string) $id)>{{ $label }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="items[{{ $index }}][service_offer_id]" class="form-select package-item-offer" required>
            <option value="">{{ __('account.operator_packages.fields.offer_placeholder') }}</option>
            @foreach ($offers as $offer)
                <option
                    value="{{ $offer['offer_id'] }}"
                    data-service-id="{{ $offer['service_id'] }}"
                    data-variant-id="{{ $offer['service_variant_id'] ?? '' }}"
                    @selected($offerId === (string) $offer['offer_id'])
                >{{ $offer['label'] }}</option>
            @endforeach
        </select>
        <input type="hidden" name="items[{{ $index }}][service_id]" class="package-item-service-id" value="{{ $item['service_id'] ?? '' }}">
        <input type="hidden" name="items[{{ $index }}][service_variant_id]" class="package-item-variant-id" value="{{ $item['service_variant_id'] ?? '' }}">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][day_number]" class="form-control" min="1" max="999" value="{{ $item['day_number'] ?? '' }}">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][sort_order]" class="form-control" min="0" max="9999" value="{{ $item['sort_order'] ?? 9999 }}">
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][quantity]" class="form-control" min="1" max="9999" value="{{ $item['quantity'] ?? 1 }}">
    </td>
    <td>
        <select name="items[{{ $index }}][inclusion_mode]" class="form-select" required>
            @foreach (['included', 'optional', 'upgrade'] as $mode)
                <option value="{{ $mode }}" @selected(($item['inclusion_mode'] ?? 'included') === $mode)>
                    {{ __('account.operator_packages.inclusion_mode.'.$mode) }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" name="items[{{ $index }}][notes]" class="form-control" maxlength="5000" value="{{ $item['notes'] ?? '' }}">
    </td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger package-item-remove" title="{{ __('account.operator_packages.remove_item_button') }}">×</button>
    </td>
</tr>
