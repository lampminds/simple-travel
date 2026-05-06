@extends('layouts.base', ['title' => __('account.relationships.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false,'topbarColor' => 'navbar-light', 'classList' => 'mx-auto' ])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title">
                        <h3 class="my-0">{{ __('account.relationships.heading') }}</h3>
                        <p class="mt-1 fw-medium text-muted mb-0">
                            {{ __('account.relationships.intro', ['account' => $account->commercial_name ?? $account->name ?? $account->nick]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($relationships->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.relationships.empty') }}
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('account.relationships.columns.role') }}</th>
                                            <th>{{ __('account.relationships.columns.counterpart') }}</th>
                                            <th>{{ __('account.relationships.columns.status') }}</th>
                                            <th>{{ __('account.relationships.columns.created_via') }}</th>
                                            <th>{{ __('account.relationships.columns.approved_at') }}</th>
                                            <th class="text-end">{{ __('account.relationships.columns.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($relationships as $relationship)
                                            @php
                                                $isOperatorSide = (int) $relationship->operator_account_id === (int) $account->id;
                                                $counterpart = $isOperatorSide ? $relationship->providerAccount : $relationship->operatorAccount;
                                                $roleLabel = $isOperatorSide
                                                    ? __('account.relationships.role.operator')
                                                    : __('account.relationships.role.provider');
                                            @endphp
                                            <tr>
                                                <td>{{ $roleLabel }}</td>
                                                <td>{{ $counterpart?->commercial_name ?? $counterpart?->name ?? ('#'.($counterpart?->id ?? '—')) }}</td>
                                                <td>
                                                    <span class="badge {{
                                                        $relationship->status === 'approved'
                                                            ? 'bg-success-subtle text-success-emphasis border border-success-subtle'
                                                            : ($relationship->status === 'suspended'
                                                                ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
                                                                : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle')
                                                    }}">
                                                        {{ __('account.relationships.status.'.$relationship->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ __('account.relationships.created_via.'.$relationship->created_via) }}</td>
                                                <td>{{ $relationship->approved_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                                <td class="text-end text-nowrap">
                                                    @if ($relationship->status === \App\Models\AccountRelationship::STATUS_APPROVED)
                                                        @if (! $isOperatorSide && $relationship->operatorAccount)
                                                            <a href="{{ route('account.service-offers.operators.edit', $relationship->operatorAccount) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.relationships.actions.manage_offers') }}
                                                            </a>
                                                        @elseif ($isOperatorSide && $relationship->providerAccount)
                                                            <a href="{{ route('account.service-offers.index', ['as' => 'operator']) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.service_offers_nav') }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            {{ $relationships->links() }}
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('account.invitations.company') }}" class="btn btn-primary">
                            {{ __('invitations.section_title_company') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection

