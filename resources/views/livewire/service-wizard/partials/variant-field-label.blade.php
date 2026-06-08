@props([
    'fieldKey',
    'label',
    'required' => false,
    'for' => null,
    'uniqueSuffix' => null,
])

@php
    $helpHtml = $catalogVariantFieldHelpHtml[$fieldKey] ?? null;
    $triggerId = 'step4-variant-helper-'.$fieldKey.($uniqueSuffix ? '-'.$uniqueSuffix : '');
@endphp

<label
    @if ($for) for="{{ $for }}" @endif
    @class(['form-label', 'd-inline-flex', 'align-items-center', 'gap-1', 'required-label' => $required])
>
    {{ $label }}
    <x-catalog-helper-icon
        :html="$helpHtml"
        :trigger-id="$triggerId"
        :content-id="$triggerId.'-html'"
        :aria-label="__('wizard.catalog_helper.aria_label_variant_field', ['field' => $label])"
        wire:click.stop
    />
</label>
