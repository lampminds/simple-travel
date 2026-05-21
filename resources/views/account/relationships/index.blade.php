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
                    <div class="page-title d-flex flex-column flex-md-row align-items-md-start justify-content-md-between gap-2">
                        <div>
                            <h3 class="my-0">{{ __('account.relationships.heading') }}</h3>
                            <p class="mt-1 fw-medium text-muted mb-0">
                                {{ $perspective === 'provider'
                                    ? __('account.relationships.intro_provider', ['account' => $accountLabel])
                                    : __('account.relationships.intro_operator', ['account' => $accountLabel]) }}
                            </p>
                        </div>
                        @if ($showHubBack ?? false)
                            <a href="{{ route('account.relationships.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
                                {{ __('account.relationships.hub_back') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            @if ($rows->isEmpty())
                                <p class="text-muted mb-2">{{ __('account.relationships.empty') }}</p>
                                <a href="{{ route('account.invitations.company') }}" class="btn btn-outline-primary btn-sm">
                                    {{ __('account.relationships.empty_invite') }}
                                </a>
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
                                                            {{ $relationship->approved_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($status === 'approved' && $row['viewer_role'] === 'provider')
                                                            <a href="{{ route('account.service-offers.operators.edit', $row['counterpart']) }}" class="btn btn-sm btn-primary">
                                                                {{ __('account.relationships.actions.manage_offers') }}
                                                            </a>
                                                        @elseif ($status === 'approved' && $row['viewer_role'] === 'operator')
                                                            <a href="{{ route('account.service-offers.index', ['as' => 'operator']) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.relationships.actions.view_offers') }}
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
