@extends('layouts.base', ['title' => __('account.relationships.page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">{{ $errors->first() }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-md-between gap-2">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.relationships.heading')"
                            :subtitle="$accountLabel"
                            :instructions="__('account.relationships.intro_unified_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                            @if ($canInviteProvider ?? false)
                                <a href="{{ route('account.invitations.company', ['as' => 'provider']) }}" class="btn btn-primary btn-sm">
                                    {{ __('account.provider_supply.invite') }}
                                </a>
                            @endif
                            @if ($canInviteOperator ?? false)
                                <a href="{{ route('account.invitations.company') }}" class="btn btn-primary btn-sm">
                                    {{ __('account.relationships.invite_company') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (($pendingIncomingInvitations ?? collect())->isNotEmpty() || ($pendingOutgoingInvitations ?? collect())->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card border-warning border-opacity-50 shadow-sm">
                            <div class="card-body">
                                <h4 class="h6 fw-semibold mb-3">{{ __('account.relationships.pending_invitations_heading_all') }}</h4>
                                <p class="text-muted small mb-3">{{ __('account.relationships.pending_invitations_intro_all') }}</p>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 3rem;"></th>
                                                <th>{{ __('account.relationships.columns.counterpart') }}</th>
                                                <th>{{ __('account.relationships.columns.type') }}</th>
                                                <th>{{ __('account.relationships.pending_invitations_contact') }}</th>
                                                <th>{{ __('account.relationships.pending_invitations_expires_label') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pendingIncomingInvitations as $incoming)
                                                @php
                                                    $fromAccount = $incoming->accountInviting ?? $incoming->account;
                                                    $fromLabel = $fromAccount
                                                        ? ($fromAccount->commercial_name ?? $fromAccount->name ?? '#'.$fromAccount->id)
                                                        : '—';
                                                    $incomingIsSupply = $incoming->purpose === \App\Models\UserInvitation::PURPOSE_PROVIDER_SUPPLY;
                                                @endphp
                                                <tr>
                                                    <td class="text-nowrap">
                                                        <div class="dropdown">
                                                            <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                            </a>
                                                            <div class="dropdown-menu">
                                                                <form method="post" action="{{ route('account.relationships.incoming.accept', $incoming) }}">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">{{ __('account.relationships.pending_invitations_accept') }}</button>
                                                                </form>
                                                                <form method="post" action="{{ route('account.relationships.incoming.decline', $incoming) }}" onsubmit="return confirm(@js(__('account.relationships.pending_invitations_decline_confirm')));">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">{{ __('account.relationships.pending_invitations_decline') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="fw-medium">{{ $fromLabel }}</td>
                                                    <td>{{ $incomingIsSupply ? __('account.relationships.link_type.supply_buyer') : __('account.relationships.link_type.operator') }}</td>
                                                    <td class="text-muted small">
                                                        {{ $incoming->name }}<br>
                                                        <span class="text-body-secondary">{{ $incoming->email }}</span>
                                                    </td>
                                                    <td class="text-muted small text-nowrap">
                                                        @if ($incoming->expires_at)
                                                            {{ locale_date($incoming->expires_at->timezone(config('app.timezone'))) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @foreach ($pendingOutgoingInvitations ?? [] as $outgoing)
                                                <tr>
                                                    <td class="text-nowrap">
                                                        <div class="dropdown">
                                                            <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                            </a>
                                                            <div class="dropdown-menu">
                                                                <form method="post" action="{{ route('account.provider-supply.outgoing.revoke', $outgoing) }}" onsubmit="return confirm(@js(__('account.provider_supply.revoke_confirm')));">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-danger">{{ __('account.provider_supply.action_revoke') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="fw-medium">{{ $outgoing->company_name ?: '—' }}</td>
                                                    <td>{{ __('account.relationships.link_type.supply_supplier') }} <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">{{ __('account.relationships.pending_direction_out') }}</span></td>
                                                    <td class="text-muted small">
                                                        {{ $outgoing->name }}<br>
                                                        <span class="text-body-secondary">{{ $outgoing->email }}</span>
                                                    </td>
                                                    <td class="text-muted small text-nowrap">
                                                        @if ($outgoing->expires_at)
                                                            {{ locale_date($outgoing->expires_at->timezone(config('app.timezone'))) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            @if ($rows->isEmpty())
                                <p class="text-muted mb-2">{{ __('account.relationships.empty') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @if ($canInviteProvider ?? false)
                                        <a href="{{ route('account.invitations.company', ['as' => 'provider']) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('account.provider_supply.invite') }}
                                        </a>
                                    @endif
                                    @if ($canInviteOperator ?? false)
                                        <a href="{{ route('account.invitations.company') }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('account.relationships.invite_company') }}
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 3rem;"></th>
                                                <th>{{ __('account.relationships.columns.counterpart') }}</th>
                                                <th>{{ __('account.relationships.columns.type') }}</th>
                                                <th>{{ __('account.relationships.columns.status') }}</th>
                                                <th>{{ __('account.relationships.columns.created_via') }}</th>
                                                <th>{{ __('account.relationships.columns.approved_at') }}</th>
                                                @if ($showCatalogCoverage ?? false)
                                                    <th class="text-center">
                                                        {{ __('account.relationships.columns.catalog_coverage') }}
                                                        <div class="small fw-normal text-muted">{{ __('account.relationships.catalog_coverage_ratio') }}</div>
                                                    </th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $row)
                                                @php
                                                    $relationship = $row['relationship'];
                                                    $status = (string) $relationship->status;
                                                    $linkKind = (string) ($row['link_kind'] ?? '');
                                                    $statusClass = match ($status) {
                                                        'approved' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
                                                        'suspended' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                        default => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
                                                    };
                                                    $typeKey = match ($linkKind) {
                                                        'sells_to_agency' => 'agency',
                                                        'buys_from_provider' => 'provider',
                                                        'supply_supplier' => 'supply_supplier',
                                                        'supply_buyer' => 'supply_buyer',
                                                        default => 'operator',
                                                    };
                                                @endphp
                                                <tr>
                                                    <td class="text-nowrap">
                                                        @if ($status === 'approved')
                                                            <div class="dropdown">
                                                                <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                                </a>
                                                                <div class="dropdown-menu">
                                                                    @if ($linkKind === 'sells_to_operator')
                                                                        <a class="dropdown-item" href="{{ route('account.service-offers.operators.edit', $row['counterpart']) }}">
                                                                            {{ __('account.relationships.actions.manage_offers') }}
                                                                        </a>
                                                                    @endif
                                                                    @if ($linkKind === 'sells_as_agency' || ($linkKind === 'sells_to_operator' && ($row['also_agency'] ?? false)))
                                                                        <a class="dropdown-item" href="{{ route('account.package-offers.index', ['as' => 'agency']) }}">
                                                                            {{ __('account.relationships.actions.view_package_offers') }}
                                                                        </a>
                                                                    @endif
                                                                    @if ($linkKind === 'buys_from_provider')
                                                                        <a class="dropdown-item" href="{{ route('account.service-offers.index', ['as' => 'operator']) }}">
                                                                            {{ __('account.relationships.actions.view_offers') }}
                                                                        </a>
                                                                    @endif
                                                                    @if ($linkKind === 'sells_to_agency')
                                                                        <a class="dropdown-item" href="{{ route('account.package-offers.agencies.edit', $row['counterpart']) }}">
                                                                            {{ __('account.relationships.actions.manage_package_offers') }}
                                                                        </a>
                                                                    @endif
                                                                    @if ($linkKind === 'supply_supplier')
                                                                        <a class="dropdown-item" href="{{ route('account.provider-supply.offers.review', $row['counterpart']) }}">
                                                                            {{ __('account.provider_supply.action_review_offers') }}
                                                                        </a>
                                                                    @endif
                                                                    @if ($linkKind === 'supply_buyer')
                                                                        <a class="dropdown-item" href="{{ route('account.provider-supply.offers.edit', $row['counterpart']) }}">
                                                                            {{ __('account.provider_supply.action_offer') }}
                                                                        </a>
                                                                        <a class="dropdown-item" href="{{ route('account.provider-supply.price-list.edit', $row['counterpart']) }}">
                                                                            {{ __('account.provider_supply.action_price_list') }}
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="fw-medium">
                                                        {{ $row['counterpart_label'] }}
                                                        @if (($row['pending_offers'] ?? 0) > 0)
                                                            <div class="small text-warning-emphasis">{{ __('account.relationships.pending_offers_count', ['count' => $row['pending_offers']]) }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ __('account.relationships.link_type.'.$typeKey) }}</td>
                                                    <td>
                                                        <span class="badge {{ $statusClass }}">
                                                            {{ __('account.relationships.status.' . $status) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-muted small">
                                                        {{ __('account.relationships.created_via.' . $relationship->created_via) }}
                                                    </td>
                                                    <td class="text-muted small">
                                                        @if ($relationship->approved_at)
                                                            {{ locale_datetime($relationship->approved_at->timezone(config('app.timezone'))) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    @if ($showCatalogCoverage ?? false)
                                                        <td class="text-center text-nowrap">
                                                            @if ($linkKind === 'buys_from_provider')
                                                                @php
                                                                    $catalogAccepted = (int) ($row['catalog_accepted'] ?? 0);
                                                                    $catalogTotal = (int) ($row['catalog_total'] ?? 0);
                                                                @endphp
                                                                <span title="{{ __('account.relationships.catalog_coverage_hint', ['accepted' => $catalogAccepted, 'total' => $catalogTotal]) }}">
                                                                    {{ $catalogAccepted }} / {{ $catalogTotal }}
                                                                </span>
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
