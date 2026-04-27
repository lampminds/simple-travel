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
            <a href="{{ route('account.invitations.employee', ['status' => \App\Models\UserInvitation::STATUS_PENDING]) }}"
               class="text-reset text-decoration-none d-block h-100">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm icon icon-with-bg icon-xs rounded-sm bg-soft-primary me-3 flex-shrink-0">
                                <i class="icon-dual-primary" data-feather="user-plus" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $emp }}</h3>
                                <p class="text-muted small mb-1 text-break">
                                    {{ __('panel_stats.card_employee_invites') }}
                                </p>
                                <span class="text-primary small d-inline-block">{{ __('panel_stats.go_to') }} →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('account.invitations.company', ['status' => \App\Models\UserInvitation::STATUS_PENDING]) }}"
               class="text-reset text-decoration-none d-block h-100">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm icon icon-with-bg icon-xs rounded-sm bg-soft-info me-3 flex-shrink-0">
                                <i class="icon-dual-info" data-feather="briefcase" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $co }}</h3>
                                <p class="text-muted small mb-1 text-break">
                                    {{ __('panel_stats.card_company_invites') }}
                                </p>
                                <span class="text-primary small d-inline-block">{{ __('panel_stats.go_to') }} →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 pt-1">
            <h2 class="h6 text-uppercase text-muted mb-0">{{ __('panel_stats.section_catalog') }}</h2>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('catalog') }}" class="text-reset text-decoration-none d-block h-100">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm icon icon-with-bg icon-xs rounded-sm bg-soft-success me-3 flex-shrink-0">
                                <i class="icon-dual-success" data-feather="package" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $items }}</h3>
                                <p class="text-muted small mb-1 text-break">
                                    {{ __('panel_stats.card_catalog_items') }}
                                </p>
                                <span class="text-primary small d-inline-block">{{ __('panel_stats.go_to') }} →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('catalog') }}" class="text-reset text-decoration-none d-block h-100">
                <div class="card h-100 shadow-sm border">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm icon icon-with-bg icon-xs rounded-sm bg-soft-warning me-3 flex-shrink-0">
                                <i class="icon-dual-warning" data-feather="layers" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h3 class="mt-0 mb-1">{{ $vars }}</h3>
                                <p class="text-muted small mb-1 text-break">
                                    {{ __('panel_stats.card_catalog_variants') }}
                                </p>
                                <span class="text-primary small d-inline-block">{{ __('panel_stats.go_to') }} →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
