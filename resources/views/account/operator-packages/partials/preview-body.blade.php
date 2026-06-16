@php
    /** @var array<string, mixed> $preview */
    $locales = $preview['locales'] ?? [];
    $items = $preview['items'] ?? [];
    $conditions = $preview['conditions'] ?? [];
    $heroImages = $preview['hero_images'] ?? [];
@endphp

<div class="operator-package-preview">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4 pb-3 border-bottom">
        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
            {{ $preview['status_label'] ?? ($preview['status'] ?? '—') }}
        </span>
        @if ($preview['is_featured'] ?? false)
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                {{ __('account.operator_packages.fields.is_featured') }}
            </span>
        @endif
        @if ($preview['is_public'] ?? false)
            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                {{ __('account.operator_packages.fields.is_public') }}
            </span>
        @endif
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
        'forPdf' => false,
    ])

    <div class="mb-4">
        <h6 class="text-uppercase fs-12 text-muted mb-3">{{ __('account.operator_packages.preview_items_heading') }}</h6>
        @if ($items === [])
            <p class="text-muted mb-0">{{ __('account.operator_packages.preview_no_items') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('account.operator_packages.preview_col_day') }}</th>
                            <th>{{ __('account.operator_packages.preview_col_service') }}</th>
                            <th>{{ __('account.operator_packages.preview_col_provider') }}</th>
                            <th class="text-center">{{ __('account.operator_packages.preview_col_quantity') }}</th>
                            <th>{{ __('account.operator_packages.preview_col_inclusion') }}</th>
                            <th>{{ __('account.operator_packages.preview_col_notes') }}</th>
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
            <h6 class="text-uppercase fs-12 text-muted mb-1">{{ __('account.operator_packages.preview_conditions_heading') }}</h6>
            <p class="text-muted small mb-2">{{ __('account.operator_packages.preview_conditions_help') }}</p>
            <ul class="list-unstyled mb-0 small">
                @foreach ($conditions as $condition)
                    <li class="mb-2">
                        <span class="fw-medium">{{ $condition['label'] ?? '—' }}:</span>
                        <span class="text-muted">{!! nl2br(e($condition['text'] ?? '')) !!}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
