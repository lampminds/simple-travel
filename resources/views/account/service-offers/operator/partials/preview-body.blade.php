@php
    /** @var array<string, mixed> $preview */
    $forPdf = $forPdf ?? false;
    $summary = is_array($preview['summary'] ?? null) ? $preview['summary'] : [];
    $heroImages = $preview['hero_images'] ?? [];
    $bookingVariantFields = array_merge(
        $summary['status_fields'] ?? [],
        $summary['variant_fields'] ?? [],
    );
    $hasIntroColumn = $heroImages !== [] || ($summary['locales'] ?? []) !== [] || ($summary['base_fields'] ?? []) !== [];
@endphp

<div @class(['service-offer-preview' => ! $forPdf])>

@if ($forPdf)
    <div class="preview-header">
        <div class="preview-header-meta">
            @if (($preview['service_type_label'] ?? '') !== '')
                <span class="preview-badge">{{ $preview['service_type_label'] }}</span>
            @endif
            @if (($preview['provider_name'] ?? '') !== '')
                <span class="preview-provider">{{ $preview['provider_name'] }}</span>
            @endif
        </div>
        <div class="preview-price-block">
            <div class="preview-label">{{ __('account.service_offers.operator_preview_price') }}</div>
            <div class="preview-price">{{ $preview['operator_price'] ?? '—' }}</div>
            @if (($preview['operator_price_usd_hint'] ?? '') !== '')
                <div class="preview-price-hint">{{ $preview['operator_price_usd_hint'] }}</div>
            @endif
        </div>
    </div>
@else
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4 @if ($hasIntroColumn) pb-3 border-bottom @endif">
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if (($preview['service_type_label'] ?? '') !== '')
                <span class="badge badge-soft-primary rounded-pill px-2 py-1">{{ $preview['service_type_label'] }}</span>
            @endif
            @if (($preview['provider_name'] ?? '') !== '')
                <span class="text-muted">{{ $preview['provider_name'] }}</span>
            @endif
        </div>
        <div class="text-lg-end">
            <div class="text-muted text-uppercase fs-12">{{ __('account.service_offers.operator_preview_price') }}</div>
            <div class="fs-3 fw-semibold text-primary mb-0">{{ $preview['operator_price'] ?? '—' }}</div>
            @if (($preview['operator_price_usd_hint'] ?? '') !== '')
                <div class="small text-muted mt-1">{{ $preview['operator_price_usd_hint'] }}</div>
            @endif
        </div>
    </div>
@endif

@if ($hasIntroColumn)
    <div class="@if ($forPdf) preview-intro @else mb-4 pb-3 border-bottom @endif">
        @foreach ($summary['base_fields'] ?? [] as $field)
            @if ($forPdf)
                <div class="preview-line">
                    <span class="preview-label">{{ $field['label'] ?? '' }}:</span>
                    <span class="preview-value">{{ $field['value'] ?? '—' }}</span>
                </div>
            @else
                <div class="mb-2">
                    <span class="text-muted text-uppercase fs-12">{{ $field['label'] ?? '' }}:</span>
                    <span class="fw-medium ms-1">{{ $field['value'] ?? '—' }}</span>
                </div>
            @endif
        @endforeach

        @include('account.service-offers.operator.partials.preview-hero-images', [
            'heroImages' => $heroImages,
            'forPdf' => $forPdf,
        ])

        @if (is_array($preview['price_list'] ?? null))
            @include('account.service-offers.operator.partials.preview-price-list', [
                'priceList' => $preview['price_list'],
                'forPdf' => $forPdf,
            ])
        @endif

        @foreach ($summary['locales'] ?? [] as $localeBlock)
            @if ($forPdf)
                <div class="preview-locale">
                    <div class="preview-label">{{ $localeBlock['language'] ?? '—' }}</div>
                    @if (($localeBlock['name'] ?? '') !== '')
                        <div class="preview-locale-title">{{ $localeBlock['name'] }}</div>
                    @endif
                    @if (($localeBlock['description'] ?? '') !== '')
                        <p class="preview-locale-desc">{!! nl2br(e($localeBlock['description'])) !!}</p>
                    @endif
                </div>
            @else
                <div class="mt-3 @if (! $loop->last) pb-3 border-bottom @endif">
                    <div class="text-muted text-uppercase fs-12 mb-1">{{ $localeBlock['language'] ?? '—' }}</div>
                    @if (($localeBlock['name'] ?? '') !== '')
                        <div class="fw-semibold fs-5">{{ $localeBlock['name'] }}</div>
                    @endif
                    @if (($localeBlock['description'] ?? '') !== '')
                        <p class="text-muted mb-0 mt-2">{!! nl2br(e($localeBlock['description'])) !!}</p>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
@elseif (is_array($preview['price_list'] ?? null))
    @include('account.service-offers.operator.partials.preview-price-list', [
        'priceList' => $preview['price_list'],
        'forPdf' => $forPdf,
        'wrapperClass' => $forPdf ? null : 'alert alert-light border mb-4 py-2 px-3',
    ])
@endif

@if ($bookingVariantFields !== [])
    <div class="@if ($forPdf) preview-card @else card border shadow-none mb-4 @endif">
        @if (! $forPdf)
            <div class="card-body">
        @endif
        @include('account.service-offers.operator.partials.preview-meta-grid', [
            'fields' => $bookingVariantFields,
            'forPdf' => $forPdf,
            'columnClass' => 'col-sm-6 col-xl-4',
        ])
        @if (! $forPdf)
            </div>
        @endif
    </div>
@endif

@if (is_array($preview['advanced_summary'] ?? null))
    <div class="@if ($forPdf) preview-card @else card border shadow-none mb-3 @endif">
        @if (! $forPdf)
            <div class="card-body">
        @endif
        @include('account.service-offers.operator.partials.preview-section', [
            'section' => $preview['advanced_summary'],
            'sectionKey' => 'advanced',
            'forPdf' => $forPdf,
        ])
        @if (! $forPdf)
            </div>
        @endif
    </div>
@endif

@if (($preview['sections'] ?? []) === [])
    @if ($heroImages === [] && $bookingVariantFields === [] && ! is_array($preview['advanced_summary'] ?? null))
        <p class="@if ($forPdf) preview-muted @else text-muted mb-0 @endif">{{ __('account.service_offers.operator_preview_no_data') }}</p>
    @endif
@elseif ($forPdf)
    @foreach ($preview['sections'] as $index => $section)
        <div class="preview-section-block">
            <h2 class="preview-section-title">{{ $section['title'] ?? '' }}</h2>
            @include('account.service-offers.operator.partials.preview-section', [
                'section' => $section,
                'sectionKey' => 'pdf-section-'.$index,
                'forPdf' => true,
            ])
        </div>
    @endforeach
@else
    <div class="accordion custom-accordionwitharrow" id="service-offer-preview-accordion">
        @foreach ($preview['sections'] as $index => $section)
            @php
                $sectionId = 'preview-section-'.$index;
                $isOpen = $index === 0;
            @endphp
            <div @class(['card mb-1 border shadow-none', 'mb-0' => $loop->last])>
                <a
                    href=""
                    @class(['text-dark', 'collapsed' => ! $isOpen])
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{ $sectionId }}"
                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                    aria-controls="collapse-{{ $sectionId }}"
                >
                    <div class="card-header bg-white" id="heading-{{ $sectionId }}">
                        <h5 class="my-1 fw-medium">
                            {{ $section['title'] ?? '' }}
                            <i class="icon-xs accordion-arrow" data-feather="chevron-down"></i>
                        </h5>
                    </div>
                </a>
                <div
                    id="collapse-{{ $sectionId }}"
                    @class(['collapse', 'show' => $isOpen])
                    aria-labelledby="heading-{{ $sectionId }}"
                    data-bs-parent="#service-offer-preview-accordion"
                >
                    <div class="card-body pt-2">
                        @include('account.service-offers.operator.partials.preview-section', [
                            'section' => $section,
                            'sectionKey' => $sectionId,
                            'forPdf' => false,
                        ])
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

</div>
