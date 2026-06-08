@props([
    'name',
    'value' => '',
    'placeholder' => null,
    'id' => null,
])

@php
    $jsFormat = locale_date_input_js_format();
    $placeholder ??= $jsFormat['placeholder'];
    $isoValue = normalize_form_date_value($value);
    $displayValue = $isoValue !== '' ? format_date_for_input($isoValue) : '';
    $inputClass = trim('js-locale-date-display form-control '.($attributes->get('class') ?? ''));
    $displayId = $id ?? null;
@endphp

<div
    class="locale-date-input"
    data-locale-date-wrap
    data-date-pattern="{{ $jsFormat['pattern'] }}"
>
    <input
        type="text"
        @if ($displayId) id="{{ $displayId }}" @endif
        class="{{ $inputClass }}"
        value="{{ $displayValue }}"
        placeholder="{{ $placeholder }}"
        inputmode="numeric"
        autocomplete="off"
        maxlength="10"
        @disabled($attributes->get('disabled'))
    >
    <input type="hidden" name="{{ $name }}" value="{{ $isoValue }}" class="js-locale-date-iso">
</div>
