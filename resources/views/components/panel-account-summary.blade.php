@props(['stats', 'catalogMode' => 'provider'])

@php
    $s = is_array($stats) ? $stats : [];
    $emp = (int) ($s['invitations_pending_employee'] ?? 0);
    $co = (int) ($s['invitations_pending_company'] ?? 0);
    $items = (int) ($s['catalog_service_count'] ?? 0);
    $vars = (int) ($s['catalog_variant_count'] ?? 0);
    $packages = (int) ($s['operator_package_count'] ?? 0);
    $pendingOffers = (int) ($s['service_offers_pending_count'] ?? 0);
    $isOperatorCatalog = $catalogMode === 'operator';
@endphp

<div {{ $attributes->class('panel-account-summary') }}>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
        <div class="col">
            <div class="card h-100 shadow-sm border">
                <div class="card-body d-flex flex-column">
                    <p class="text-uppercase text-muted small mb-2 mb-md-3">{{ __('panel_stats.section_invitations') }}</p>
                    <div class="d-flex align-items-start">
                        <div class="icon icon-with-bg rounded-sm bg-soft-primary me-3 flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                             style="width: 3rem; height: 3rem;">
                            <i class="icon-dual-primary" data-feather="user-plus" aria-hidden="true"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="mt-0 mb-1">{{ $emp }}</h3>
                            <p class="text-muted small mb-0 text-break">
                                {{ __('panel_stats.card_employee_invites') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('account.invitations.employee', ['status' => \App\Models\UserInvitation::STATUS_PENDING]) }}"
                       class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                        {{ __('panel_stats.go_to') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm border">
                <div class="card-body d-flex flex-column">
                    <p class="text-uppercase text-muted small mb-2 mb-md-3">{{ __('panel_stats.section_invitations') }}</p>
                    <div class="d-flex align-items-start">
                        <div class="icon icon-with-bg rounded-sm bg-soft-info me-3 flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                             style="width: 3rem; height: 3rem;">
                            <i class="icon-dual-info" data-feather="briefcase" aria-hidden="true"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="mt-0 mb-1">{{ $co }}</h3>
                            <p class="text-muted small mb-0 text-break">
                                {{ __('panel_stats.card_company_invites') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('account.invitations.company', ['status' => \App\Models\UserInvitation::STATUS_PENDING]) }}"
                       class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                        {{ __('panel_stats.go_to') }}
                    </a>
                </div>
            </div>
        </div>

        @if ($isOperatorCatalog)
            <div class="col">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body d-flex flex-column">
                        <p class="text-uppercase text-muted small mb-2 mb-md-3">{{ __('panel_stats.section_service_offers') }}</p>
                        <div class="d-flex align-items-start">
                            <div class="icon icon-with-bg rounded-sm bg-soft-warning me-3 flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                                 style="width: 3rem; height: 3rem;">
                                <i class="icon-dual-warning" data-feather="inbox" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $pendingOffers }}</h3>
                                <p class="text-muted small mb-0 text-break">
                                    {{ __('panel_stats.card_service_offers_pending') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('account.service-offers.index', ['as' => 'operator', 'status' => 'pending']) }}"
                           class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                            {{ __('panel_stats.go_to') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body d-flex flex-column">
                        <p class="text-uppercase text-muted small mb-2 mb-md-3">{{ __('panel_stats.section_packages') }}</p>
                        <div class="d-flex align-items-start">
                            <div class="icon icon-with-bg rounded-sm bg-soft-success me-3 flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                                 style="width: 3rem; height: 3rem;">
                                <i class="icon-dual-success" data-feather="package" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $packages }}</h3>
                                <p class="text-muted small mb-0 text-break">
                                    {{ __('panel_stats.card_operator_packages') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('account.operator-packages.index') }}" class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                            {{ __('panel_stats.go_to') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="col">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body d-flex flex-column">
                        <p class="text-uppercase text-muted small mb-2 mb-md-3">{{ __('panel_stats.section_catalog') }}</p>
                        <div class="d-flex align-items-start">
                            <div class="icon icon-with-bg rounded-sm bg-soft-success me-3 flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                                 style="width: 3rem; height: 3rem;">
                                <i class="icon-dual-success" data-feather="package" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $items }}</h3>
                                <p class="text-muted small mb-0 text-break">
                                    {{ __('panel_stats.card_catalog_items') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('catalog') }}" class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                            {{ __('panel_stats.go_to') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body d-flex flex-column">
                        <p class="text-uppercase text-muted small mb-2 mb-md-3">{{ __('panel_stats.section_catalog') }}</p>
                        <div class="d-flex align-items-start">
                            <div class="icon icon-with-bg rounded-sm bg-soft-warning me-3 flex-shrink-0 d-inline-flex align-items-center justify-content-center"
                                 style="width: 3rem; height: 3rem;">
                                <i class="icon-dual-warning" data-feather="layers" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $vars }}</h3>
                                <p class="text-muted small mb-0 text-break">
                                    {{ __('panel_stats.card_catalog_variants') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('catalog') }}" class="btn btn-outline-primary btn-sm mt-3 align-self-start">
                            {{ __('panel_stats.go_to') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
