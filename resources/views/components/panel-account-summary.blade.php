@props(['stats'])

@php
    $s = is_array($stats) ? $stats : [];
    $emp = (int) ($s['invitations_pending_employee'] ?? 0);
    $co = (int) ($s['invitations_pending_company'] ?? 0);
    $items = (int) ($s['catalog_service_count'] ?? 0);
    $vars = (int) ($s['catalog_variant_count'] ?? 0);
@endphp

<div {{ $attributes->class('panel-account-summary') }}>
    <div class="row g-3">
        <div class="col-12">
            <h2 class="h6 text-uppercase text-muted mb-0">{{ __('panel_stats.section_invitations') }}</h2>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm border">
                <div class="card-body d-flex flex-column">
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
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm border">
                <div class="card-body d-flex flex-column">
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

        <div class="col-12 pt-1">
            <h2 class="h6 text-uppercase text-muted mb-0">{{ __('panel_stats.section_catalog') }}</h2>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm border">
                <div class="card-body d-flex flex-column">
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
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm border">
                <div class="card-body d-flex flex-column">
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
    </div>
</div>
