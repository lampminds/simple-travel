@extends('layouts.base', ['title' => __('account.relationships.page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-md-between gap-2">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.relationships.heading')"
                            :subtitle="$accountLabel"
                            :instructions="match ($perspective) {
                                'agency' => __('account.relationships.intro_agency_instructions'),
                                'provider' => __('account.relationships.intro_provider_instructions'),
                                default => __('account.relationships.intro_operator_instructions'),
                            }"
                        />
                        <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                            @if (($perspective ?? '') === 'operator')
                                <a href="{{ route('account.invitations.company') }}" class="btn btn-primary btn-sm">
                                    {{ __('account.relationships.invite_company') }}
                                </a>
                            @endif
                            @if ($showHubBack ?? false)
                                <a href="{{ route('account.relationships.index') }}" class="btn btn-outline-secondary btn-sm">
                                    {{ __('account.relationships.hub_back') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (in_array($perspective ?? '', ['provider', 'agency'], true) && ($pendingIncomingInvitations ?? collect())->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card border-warning border-opacity-50 shadow-sm">
                            <div class="card-body">
                                <h4 class="h6 fw-semibold mb-3">{{ __('account.relationships.pending_invitations_heading') }}</h4>
                                <p class="text-muted small mb-3">{{ __('account.relationships.pending_invitations_intro') }}</p>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.relationships.pending_invitations_operator') }}</th>
                                                <th>{{ __('account.relationships.pending_invitations_contact') }}</th>
                                                <th>{{ __('account.relationships.pending_invitations_expires_label') }}</th>
                                                <th class="text-end">{{ __('account.relationships.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pendingIncomingInvitations as $incoming)
                                                @php
                                                    $operatorAccount = $incoming->accountInviting ?? $incoming->account;
                                                    $operatorLabel = $operatorAccount
                                                        ? ($operatorAccount->commercial_name ?? $operatorAccount->name ?? '#'.$operatorAccount->id)
                                                        : '—';
                                                @endphp
                                                <tr>
                                                    <td class="fw-medium">{{ $operatorLabel }}</td>
                                                    <td class="text-muted small">
                                                        {{ $incoming->name }}<br>
                                                        <span class="text-body-secondary">{{ $incoming->email }}</span>
                                                    </td>
                                                    <td class="text-muted small text-nowrap">
                                                        @if ($incoming->expires_at)
                                                            {{ __('account.relationships.pending_invitations_expires', ['date' => locale_date($incoming->expires_at->timezone(config('app.timezone')))]) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        @php $relationshipsAs = request('as'); @endphp
                                                        <form method="post" action="{{ route('account.relationships.incoming.accept', $incoming) }}{{ $relationshipsAs ? '?as='.urlencode((string) $relationshipsAs) : '' }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('account.relationships.pending_invitations_accept') }}</button>
                                                        </form>
                                                        <form method="post" action="{{ route('account.relationships.incoming.decline', $incoming) }}{{ $relationshipsAs ? '?as='.urlencode((string) $relationshipsAs) : '' }}" class="d-inline ms-1"
                                                              onsubmit="return confirm(@js(__('account.relationships.pending_invitations_decline_confirm')));">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('account.relationships.pending_invitations_decline') }}</button>
                                                        </form>
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

            @if (($perspective ?? '') === 'operator' && ($operatorTab ?? null) !== null)
                @php
                    $relationshipsTabQuery = array_filter([
                        'as' => request('as'),
                    ]);
                @endphp
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <ul class="nav nav-pills mb-0 gap-2">
                            <li class="nav-item">
                                <a
                                    class="nav-link @if ($operatorTab === 'providers') active @endif"
                                    href="{{ route('account.relationships.index', array_merge($relationshipsTabQuery, ['tab' => 'providers'])) }}"
                                >
                                    {{ __('account.relationships.tab_providers') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link @if ($operatorTab === 'agencies') active @endif"
                                    href="{{ route('account.relationships.index', array_merge($relationshipsTabQuery, ['tab' => 'agencies'])) }}"
                                >
                                    {{ __('account.relationships.tab_agencies') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            @if ($rows->isEmpty())
                                <p class="text-muted mb-2">
                                    {{ ($perspective ?? '') === 'operator' && ($operatorTab ?? null) === 'agencies'
                                        ? __('account.relationships.empty_agencies')
                                        : (($perspective ?? '') === 'operator' && ($operatorTab ?? null) === 'providers'
                                            ? __('account.relationships.empty_providers')
                                            : __('account.relationships.empty')) }}
                                </p>
                                @if (($perspective ?? '') === 'operator')
                                    <a href="{{ route('account.invitations.company') }}" class="btn btn-outline-primary btn-sm">
                                        {{ __('account.relationships.invite_company') }}
                                    </a>
                                @elseif (($perspective ?? '') !== 'agency')
                                    <a href="{{ route('account.invitations.company') }}" class="btn btn-outline-primary btn-sm">
                                        {{ __('account.relationships.empty_invite') }}
                                    </a>
                                @endif
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                @if ($showRoleColumn)
                                                    <th>{{ __('account.relationships.columns.role') }}</th>
                                                @endif
                                                <th>{{ __('account.relationships.columns.counterpart') }}</th>
                                                <th>{{ __('account.relationships.columns.status') }}</th>
                                                <th>{{ __('account.relationships.columns.created_via') }}</th>
                                                <th>{{ __('account.relationships.columns.approved_at') }}</th>
                                                <th class="text-end">{{ __('account.relationships.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $row)
                                                @php
                                                    $relationship = $row['relationship'];
                                                    $status = (string) $relationship->status;
                                                    $statusClass = match ($status) {
                                                        'approved' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
                                                        'suspended' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                        default => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
                                                    };
                                                @endphp
                                                <tr>
                                                    @if ($showRoleColumn)
                                                        <td>{{ __('account.relationships.role.' . $row['viewer_role']) }}</td>
                                                    @endif
                                                    <td class="fw-medium">{{ $row['counterpart_label'] }}</td>
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
                                                    <td class="text-end">
                                                        @if ($status === 'approved' && $row['viewer_role'] === 'provider')
                                                            <a href="{{ route('account.service-offers.operators.edit', $row['counterpart']) }}" class="btn btn-sm btn-primary">
                                                                {{ __('account.relationships.actions.manage_offers') }}
                                                            </a>
                                                        @elseif ($status === 'approved' && $row['viewer_role'] === 'operator' && ($row['counterpart_kind'] ?? '') === 'provider')
                                                            <a href="{{ route('account.service-offers.index', ['as' => 'operator']) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.relationships.actions.view_offers') }}
                                                            </a>
                                                        @elseif ($status === 'approved' && $row['viewer_role'] === 'operator' && ($row['counterpart_kind'] ?? '') === 'agency')
                                                            <a href="{{ route('account.package-offers.agencies.edit', $row['counterpart']) }}" class="btn btn-sm btn-primary">
                                                                {{ __('account.relationships.actions.manage_package_offers') }}
                                                            </a>
                                                        @elseif ($status === 'approved' && $row['viewer_role'] === 'agency')
                                                            <a href="{{ route('account.package-offers.index', ['as' => 'agency']) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.relationships.actions.view_package_offers') }}
                                                            </a>
                                                        @endif
                                                    </td>
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
