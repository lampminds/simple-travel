@php
    /** @var list<array{url: string, label: string}> $heroImages */
    $forPdf = $forPdf ?? false;
@endphp

@if ($heroImages !== [])
    <div class="@if ($forPdf) preview-hero-row @else row g-2 my-3 @endif">
        @foreach ($heroImages as $image)
            @if ($forPdf && blank($image['url'] ?? ''))
                @continue
            @endif
            @if ($forPdf)
                <div class="preview-hero-item">
                    <img
                        src="{{ $image['url'] ?? '' }}"
                        alt=""
                        class="preview-hero-img"
                        @if (! empty($image['width'])) width="{{ (int) $image['width'] }}" @endif
                        @if (! empty($image['height'])) height="{{ (int) $image['height'] }}" @endif
                    >
                    <div class="preview-hero-label">{{ $image['label'] ?? '' }}</div>
                </div>
            @else
                <div @class(['col-12' => count($heroImages) === 1, 'col-sm-6' => count($heroImages) > 1])>
                    <div class="rounded overflow-hidden border bg-light">
                        <img
                            src="{{ $image['url'] ?? '' }}"
                            alt=""
                            class="w-100 object-fit-cover"
                            style="height: 180px;"
                        >
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
