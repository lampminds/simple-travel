@props([
    'title',
    'subtitle' => null,
    'instructions' => null,
])

@php
    $instructionLines = match (true) {
        is_array($instructions) => array_values(array_filter($instructions, fn ($line) => filled($line))),
        filled($instructions) => [$instructions],
        default => [],
    };
@endphp

<div {{ $attributes->merge(['class' => 'page-title account-page-header']) }}>
    <h2 class="account-page-header__title">{{ $title }}</h2>

    @if (filled($subtitle))
        <p class="account-page-header__subtitle">{{ $subtitle }}</p>
    @endif

    @foreach ($instructionLines as $line)
        <p class="account-page-header__instructions">{{ $line }}</p>
    @endforeach

    {{ $slot }}
</div>
