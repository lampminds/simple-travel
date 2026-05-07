{{--
    Operator catalog: accepted provider offers (one row per variant/offer).
    Expects: $linkedCatalog — collection of [ 'provider' => Account|null, 'items' => Collection of [ 'offer', 'service', 'variant' ] ]
--}}
<div class="row mt-5">
    <div class="col-lg-12">
        <h4 class="h5 mb-2">{{ __('catalog.operator_linked_heading') }}</h4>
        <p class="text-muted small mb-3">{{ __('catalog.operator_linked_intro') }}</p>

        @foreach ($linkedCatalog as $group)
            @php
                $provider = $group['provider'] ?? null;
                $providerLabel = $provider?->commercial_name ?? $provider?->name ?? $provider?->nick ?? __('catalog.operator_linked_unknown_provider');
            @endphp
            <div class="card border mb-4">
                <div class="card-header py-2 fw-semibold">
                    {{ __('catalog.operator_linked_provider_label') }}: {{ $providerLabel }}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('catalog.operator_linked_col_service') }}</th>
                                    <th>{{ __('catalog.operator_linked_col_type') }}</th>
                                    <th>{{ __('catalog.operator_linked_col_service_status') }}</th>
                                    <th>{{ __('catalog.operator_linked_col_variant') }}</th>
                                    <th>{{ __('catalog.operator_linked_col_variant_status') }}</th>
                                    <th>{{ __('catalog.operator_linked_col_operator_availability') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['items'] as $item)
                                    @php
                                        $offer = $item['offer'];
                                        $svc = $item['service'];
                                        $v = $item['variant'];
                                        $vst = (string) ($v->status ?? '');
                                    @endphp
                                    <tr>
                                        <td class="fw-medium">{{ $svc->name !== '' ? $svc->name : ('#'.$svc->id) }}</td>
                                        <td>{{ $svc->serviceType?->name ?: strtoupper($svc->serviceType?->code ?? '') }}</td>
                                        <td>
                                            @include('catalog.partials.catalog-status-badge', [
                                                'presentation' => \App\Support\ServiceCatalogStatus::forService($svc->status ?? null),
                                            ])
                                        </td>
                                        <td>
                                            @if ($v->name !== '')
                                                {{ $v->name }}
                                                @if ($v->sku !== '')
                                                    <span class="text-muted small">({{ $v->sku }})</span>
                                                @endif
                                            @else
                                                <code class="small">{{ $v->sku }}</code>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($vst !== '' && in_array($vst, ['active', 'inactive', 'hidden', 'suspended', 'discontinued'], true))
                                                @include('catalog.partials.catalog-status-badge', [
                                                    'presentation' => \App\Support\ServiceCatalogStatus::forVariant($vst),
                                                ])
                                            @elseif ($vst !== '')
                                                @include('catalog.partials.catalog-status-badge', [
                                                    'presentation' => [
                                                        'badge' => 'secondary',
                                                        'icon' => 'help-circle',
                                                        'label' => $vst,
                                                    ],
                                                ])
                                            @else
                                                @include('catalog.partials.catalog-status-badge', [
                                                    'presentation' => \App\Support\ServiceCatalogStatus::forVariant(null),
                                                ])
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('account.service-offers.linked-availability', $offer) }}" class="d-flex flex-wrap align-items-center gap-2">
                                                @csrf
                                                <select name="availability" class="form-select form-select-sm" style="min-width: 11rem;" aria-label="{{ __('catalog.operator_linked_col_operator_availability') }}">
                                                    @foreach (['active', 'suspended', 'discontinued'] as $av)
                                                        <option value="{{ $av }}" @selected($offer->availability === $av)>
                                                            {{ __('catalog.operator_offer_availability.'.$av) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">{{ __('catalog.operator_linked_save') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
