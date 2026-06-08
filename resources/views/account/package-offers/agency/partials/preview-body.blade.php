@php
    /** @var array<string, mixed> $preview */
    $forPdf = $forPdf ?? false;
    $locales = $preview['locales'] ?? [];
    $items = $preview['items'] ?? [];
    $conditions = $preview['conditions'] ?? [];
    $heroImages = $preview['hero_images'] ?? [];
    $galleries = $preview['galleries'] ?? [];
@endphp

<div @class(['package-offer-preview' => ! $forPdf])>
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4 @if ($locales !== [] || $heroImages !== []) pb-3 border-bottom @endif">
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if (($preview['operator_name'] ?? '') !== '')
                <span class="text-muted">{{ $preview['operator_name'] }}</span>
            @endif
            @if (($preview['price_list_name'] ?? '') !== '')
                <span class="badge bg-light text-dark border">{{ __('account.package_offers.agency_preview_price_list') }}: {{ $preview['price_list_name'] }}</span>
            @endif
        </div>
        <div class="text-lg-end">
            <div class="text-muted text-uppercase fs-12">{{ __('account.package_offers.agency_preview_price') }}</div>
            <div class="fs-3 fw-semibold text-primary mb-0">{{ $preview['agency_price'] ?? '—' }}</div>
            @if (($preview['agency_price_usd_hint'] ?? '') !== '')
                <div class="small text-muted mt-1">{{ $preview['agency_price_usd_hint'] }}</div>
            @endif
        </div>
    </div>

    @foreach ($locales as $localeBlock)
        <div class="mb-4 @if (! $loop->last) pb-3 border-bottom @endif">
            <div class="text-muted text-uppercase fs-12 mb-1">{{ $localeBlock['language'] ?? '—' }}</div>
            @if (($localeBlock['name'] ?? '') !== '')
                <div class="fw-semibold fs-5">{{ $localeBlock['name'] }}</div>
            @endif
            @if (($localeBlock['description'] ?? '') !== '')
                <p class="text-muted mb-0 mt-2">{!! nl2br(e($localeBlock['description'])) !!}</p>
            @endif
        </div>
    @endforeach

    @include('account.service-offers.operator.partials.preview-hero-images', [
        'heroImages' => $heroImages,
        'forPdf' => $forPdf,
    ])

    <div class="mb-4">
        <h6 class="text-uppercase fs-12 text-muted mb-3">{{ __('account.package_offers.agency_preview_items_heading') }}</h6>
        @if ($items === [])
            <p class="text-muted mb-0">{{ __('account.package_offers.agency_preview_no_items') }}</p>
        @elseif ($forPdf)
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>{{ __('account.package_offers.agency_preview_col_day') }}</th>
                        <th>{{ __('account.package_offers.agency_preview_col_service') }}</th>
                        <th>{{ __('account.package_offers.agency_preview_col_provider') }}</th>
                        <th>{{ __('account.package_offers.agency_preview_col_quantity') }}</th>
                        <th>{{ __('account.package_offers.agency_preview_col_inclusion') }}</th>
                        <th>{{ __('account.package_offers.agency_preview_col_notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['day_number'] ?? '—' }}</td>
                            <td>
                                {{ $item['service_name'] ?? '—' }}
                                @if (($item['variant_sku'] ?? '') !== '')
                                    <div class="preview-muted">{{ $item['variant_sku'] }}</div>
                                @endif
                            </td>
                            <td>{{ ($item['provider_name'] ?? '') !== '' ? $item['provider_name'] : '—' }}</td>
                            <td>{{ $item['quantity'] ?? 1 }}</td>
                            <td>{{ $item['inclusion_mode_label'] ?? ($item['inclusion_mode'] ?? '—') }}</td>
                            <td>{{ ($item['notes'] ?? '') !== '' ? $item['notes'] : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('account.package_offers.agency_preview_col_day') }}</th>
                            <th>{{ __('account.package_offers.agency_preview_col_service') }}</th>
                            <th>{{ __('account.package_offers.agency_preview_col_provider') }}</th>
                            <th class="text-center">{{ __('account.package_offers.agency_preview_col_quantity') }}</th>
                            <th>{{ __('account.package_offers.agency_preview_col_inclusion') }}</th>
                            <th>{{ __('account.package_offers.agency_preview_col_notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-muted">{{ $item['day_number'] ?? '—' }}</td>
                                <td>
                                    <span class="fw-medium">{{ $item['service_name'] ?? '—' }}</span>
                                    @if (($item['variant_sku'] ?? '') !== '')
                                        <span class="text-muted small d-block">{{ $item['variant_sku'] }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ ($item['provider_name'] ?? '') !== '' ? $item['provider_name'] : '—' }}</td>
                                <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                        {{ $item['inclusion_mode_label'] ?? ($item['inclusion_mode'] ?? '—') }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ ($item['notes'] ?? '') !== '' ? $item['notes'] : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($conditions !== [])
        <div>
            <h6 class="text-uppercase fs-12 text-muted mb-2">{{ __('account.package_offers.agency_preview_conditions_heading') }}</h6>
            <ul class="list-unstyled mb-0 @if ($forPdf) preview-conditions-list @else small @endif">
                @foreach ($conditions as $condition)
                    <li class="@if ($forPdf) preview-condition-line @else mb-1 @endif">
                        <span class="fw-medium">{{ $condition['label'] ?? '—' }}:</span>
                        <span class="text-muted">{{ $condition['text'] ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($forPdf && $galleries !== [])
        @foreach ($galleries as $gallery)
            @if (($gallery['images'] ?? []) === [])
                @continue
            @endif
            <div class="preview-section-block">
                <div class="preview-section-title">{{ $gallery['title'] ?? '' }}</div>
                <div class="preview-gallery-grid">
                    @foreach ($gallery['images'] as $image)
                        @if (blank($image['url'] ?? ''))
                            @continue
                        @endif
                        <img
                            src="{{ $image['url'] ?? '' }}"
                            alt=""
                            class="preview-gallery-img"
                            @if (! empty($image['width'])) width="{{ (int) $image['width'] }}" @endif
                            @if (! empty($image['height'])) height="{{ (int) $image['height'] }}" @endif
                        >
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
