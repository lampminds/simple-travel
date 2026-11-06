@php
    $rowCategories = is_array($row['categories'] ?? null) ? $row['categories'] : [];
    $minError = 'pax_bands.'.$variant->id.'.rows.'.$index.'.min_pax';
    $maxError = 'pax_bands.'.$variant->id.'.rows.'.$index.'.max_pax';
    $priceError = 'pax_bands.'.$variant->id.'.rows.'.$index.'.price';
@endphp
<tr data-pax-row>
    <td>
        <label class="visually-hidden" for="pax-min-{{ $variant->id }}-{{ $index }}">{{ __('account.passenger_policies.pax_min') }}</label>
        <input
            type="number"
            min="1"
            step="1"
            class="form-control @error($minError) is-invalid @enderror"
            id="pax-min-{{ $variant->id }}-{{ $index }}"
            name="pax_bands[{{ $variant->id }}][rows][{{ $index }}][min_pax]"
            value="{{ $row['min_pax'] ?? '' }}"
        >
        @error($minError)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </td>
    <td>
        <label class="visually-hidden" for="pax-max-{{ $variant->id }}-{{ $index }}">{{ __('account.passenger_policies.pax_max') }}</label>
        <input
            type="number"
            min="1"
            step="1"
            class="form-control @error($maxError) is-invalid @enderror"
            id="pax-max-{{ $variant->id }}-{{ $index }}"
            name="pax_bands[{{ $variant->id }}][rows][{{ $index }}][max_pax]"
            value="{{ $row['max_pax'] ?? '' }}"
            placeholder="{{ __('account.passenger_policies.pax_max_placeholder') }}"
        >
        @error($maxError)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </td>
    <td>
        <label class="visually-hidden" for="pax-price-{{ $variant->id }}-{{ $index }}">{{ __('account.passenger_policies.pax_price') }}</label>
        <input
            type="number"
            min="0"
            step="0.01"
            class="form-control @error($priceError) is-invalid @enderror"
            id="pax-price-{{ $variant->id }}-{{ $index }}"
            name="pax_bands[{{ $variant->id }}][rows][{{ $index }}][price]"
            value="{{ $row['price'] ?? '' }}"
        >
        @error($priceError)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </td>
    @foreach ($policyRules as $policyRule)
        @php
            $categoryId = (int) $policyRule->passenger_category_id;
            $cellError = 'pax_bands.'.$variant->id.'.rows.'.$index.'.categories.'.$categoryId;
            $cell = $rowCategories[$categoryId] ?? ($rowCategories[(string) $categoryId] ?? '');
        @endphp
        <td>
            <label class="visually-hidden" for="pax-cell-{{ $variant->id }}-{{ $index }}-{{ $categoryId }}">{{ $policyRule->category?->name }}</label>
            <input
                type="number"
                min="0"
                step="0.01"
                class="form-control @error($cellError) is-invalid @enderror"
                id="pax-cell-{{ $variant->id }}-{{ $index }}-{{ $categoryId }}"
                name="pax_bands[{{ $variant->id }}][rows][{{ $index }}][categories][{{ $categoryId }}]"
                value="{{ $cell }}"
                placeholder="{{ __('account.passenger_policies.regular_price') }}"
            >
            @error($cellError)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </td>
    @endforeach
    <td class="text-end">
        <button type="button" class="btn btn-light btn-sm" data-pax-remove>{{ __('account.passenger_policies.pax_remove') }}</button>
    </td>
</tr>
