@props([
    'html' => null,
    'triggerId',
    'contentId',
    'ariaLabel',
])

@if (filled($html))
    <button
        type="button"
        id="{{ $triggerId }}"
        data-catalog-helper-trigger
        data-catalog-helper-content="{{ $contentId }}"
        aria-label="{{ $ariaLabel }}"
        {{ $attributes->class(['btn btn-link btn-sm text-muted p-0 align-baseline']) }}
    >
        <i data-feather="help-circle" class="icon icon-xs"></i>
    </button>
    <div id="{{ $contentId }}" class="d-none" aria-hidden="true">
        {!! $html !!}
    </div>
@endif
