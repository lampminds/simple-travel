@php
    /** @var array<string, mixed> $section */
    $sectionKey = $sectionKey ?? 'summary';
    $forPdf = $forPdf ?? false;
@endphp

@if (! empty($section['galleries']))
    @foreach ($section['galleries'] as $galleryIndex => $gallery)
        @php
            $carouselId = 'preview-gallery-'.$sectionKey.'-'.$galleryIndex;
            $images = $gallery['images'] ?? [];
        @endphp
        @if ($images !== [])
            <div class="@if (! $loop->last && ! $forPdf) mb-4 @elseif (! $loop->last && $forPdf) preview-block @endif">
                <div class="@if ($forPdf) preview-subtitle @else fw-medium mb-2 @endif">{{ $gallery['title'] ?? '' }}</div>
                @if ($forPdf)
                    <div class="preview-gallery-grid">
                        @foreach ($images as $image)
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
                @elseif (count($images) === 1)
                    <div class="rounded overflow-hidden border">
                        <img src="{{ $images[0]['url'] ?? '' }}" alt="" class="w-100 object-fit-cover" style="max-height: 320px;">
                    </div>
                @else
                    <div id="{{ $carouselId }}" class="carousel slide border rounded overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-indicators mb-0">
                            @foreach ($images as $slideIndex => $image)
                                <button
                                    type="button"
                                    data-bs-target="#{{ $carouselId }}"
                                    data-bs-slide-to="{{ $slideIndex }}"
                                    @class(['active' => $slideIndex === 0])
                                    @if ($slideIndex === 0) aria-current="true" @endif
                                    aria-label="{{ __('account.service_offers.operator_preview_gallery_slide', ['n' => $slideIndex + 1]) }}"
                                ></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($images as $slideIndex => $image)
                                <div @class(['carousel-item', 'active' => $slideIndex === 0])>
                                    <img src="{{ $image['url'] ?? '' }}" alt="" class="d-block w-100 object-fit-cover" style="max-height: 320px;">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">{{ __('account.service_offers.operator_preview_gallery_prev') }}</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">{{ __('account.service_offers.operator_preview_gallery_next') }}</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif
    @endforeach
@endif

@if (! empty($section['groups']))
    @foreach ($section['groups'] as $group)
        <div class="@if (! $loop->last) @if ($forPdf) preview-block @else mb-3 pb-3 border-bottom @endif @endif">
            @if (($group['title'] ?? '') !== '')
                <div class="@if ($forPdf) preview-label @else text-muted text-uppercase fs-12 mb-2 @endif">{{ $group['title'] }}</div>
            @endif
            @if (($group['items'] ?? []) === [])
                <p class="@if ($forPdf) preview-muted @else text-muted mb-0 @endif">{{ __('account.service_offers.operator_preview_no_data') }}</p>
            @elseif ($forPdf)
                <p class="preview-tags">{{ implode(' · ', $group['items']) }}</p>
            @else
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($group['items'] as $item)
                        <span class="badge badge-soft-primary rounded-pill px-2 py-1">{{ $item }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
@endif

@if (! empty($section['fields']))
    @include('account.service-offers.operator.partials.preview-meta-grid', [
        'fields' => $section['fields'],
        'forPdf' => $forPdf,
        'rowClass' => $forPdf ? null : 'row service-offer-preview-meta',
    ])
@endif

@if (! empty($section['details']))
    <table class="@if ($forPdf) preview-table @else table table-sm table-hover align-middle mb-0 @endif">
        <thead>
            <tr>
                <th>{{ __('account.service_offers.operator_preview_detail_context') }}</th>
                <th>{{ __('wizard.step6_col_mandatory') }}</th>
                <th>{{ __('wizard.step6_description') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($section['details'] as $detail)
                <tr>
                    <td>{{ $detail['context'] ?? '—' }}</td>
                    <td>
                        @if ($detail['mandatory'] ?? false)
                            {{ __('wizard.step6_mandatory_yes') }}
                        @else
                            {{ __('account.service_offers.operator_preview_no') }}
                        @endif
                    </td>
                    <td class="@if (! $forPdf) text-muted @endif">{!! nl2br(e($detail['description'] ?? '')) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if (! empty($section['tables']))
    @foreach ($section['tables'] as $table)
        <div @class([
            'service-offer-preview-table-block' => ! $forPdf,
            'preview-block' => $forPdf && ! $loop->last,
        ])>
            @if (($table['title'] ?? '') !== '')
                <div @class([
                    'preview-label' => $forPdf,
                    'text-muted text-uppercase fs-12 service-offer-preview-table-title' => ! $forPdf,
                ])>{{ $table['title'] }}</div>
            @endif
            <table class="@if ($forPdf) preview-table @else table table-sm table-hover align-middle mb-0 @endif">
                <thead>
                    <tr>
                        @foreach ($table['headers'] ?? [] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($table['rows'] ?? [] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
@endif
