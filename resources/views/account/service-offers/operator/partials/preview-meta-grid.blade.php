@php
    /** @var list<array{label: string, value: string, html?: bool}> $fields */
    $fields = $fields ?? [];
    $forPdf = $forPdf ?? false;
    $columnClass = $columnClass ?? 'col-sm-6 col-lg-4';
    $rowClass = $rowClass ?? 'row g-3';
@endphp

@if ($fields !== [])
    <div class="@if ($forPdf) preview-meta-grid @else {{ $rowClass }} @endif">
        @foreach ($fields as $field)
            <div class="@if (! $forPdf) {{ $columnClass }} @else preview-meta-item @endif">
                <div class="@if ($forPdf) preview-label @else text-muted text-uppercase fs-12 mb-1 @endif">{{ $field['label'] ?? '' }}</div>
                <div class="@if (! $forPdf) fw-medium @else preview-value @endif">
                    @if (! empty($field['html']))
                        {!! $field['value'] ?? '—' !!}
                    @else
                        {{ $field['value'] ?? '—' }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
