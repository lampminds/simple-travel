@extends('layouts.base', ['title' => __('account.provider_supply.page_title')])

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

            <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-md-between gap-2">
                <x-account-page-header
                    class="flex-grow-1"
                    :title="__('account.provider_supply.heading')"
                    :subtitle="$accountLabel"
                    :instructions="__('account.provider_supply.intro')"
                />
                <a href="{{ route('account.provider-supply.invite') }}" class="btn btn-primary btn-sm flex-shrink-0">
                    {{ __('account.provider_supply.invite') }}
                </a>
            </div>

            @if ($pendingIncoming->isNotEmpty())
                <div class="card border-warning border-opacity-50 shadow-sm mt-4">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">{{ __('account.provider_supply.pending_in_heading') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('account.provider_supply.col_company') }}</th>
                                        <th>{{ __('account.provider_supply.col_contact') }}</th>
                                        <th>{{ __('account.provider_supply.col_expires') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingIncoming as $incoming)
                                        @php
                                            $buyerAccount = $incoming->accountInviting ?? $incoming->account;
                                            $buyerLabel = $buyerAccount
                                                ? ($buyerAccount->commercial_name ?? $buyerAccount->name ?? '#'.$buyerAccount->id)
                                                : '—';
                                        @endphp
                                        <tr>
                                            <td class="text-nowrap" style="width: 3rem;">
                                                <div class="dropdown">
                                                    <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <form method="post" action="{{ route('account.provider-supply.incoming.accept', $incoming) }}">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">{{ __('account.relationships.pending_invitations_accept') }}</button>
                                                        </form>
                                                        <form method="post" action="{{ route('account.provider-supply.incoming.decline', $incoming) }}" onsubmit="return confirm(@js(__('account.relationships.pending_invitations_decline_confirm')));">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">{{ __('account.relationships.pending_invitations_decline') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-medium">{{ $buyerLabel }}</td>
                                            <td class="text-muted small">{{ $incoming->name }}<br>{{ $incoming->email }}</td>
                                            <td class="text-muted small text-nowrap">
                                                {{ $incoming->expires_at ? locale_date($incoming->expires_at->timezone(config('app.timezone'))) : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h6 fw-semibold mb-3">{{ __('account.provider_supply.suppliers_heading') }}</h2>
                    @if ($suppliers->isEmpty())
                        <p class="text-muted mb-0">{{ __('account.provider_supply.empty_suppliers') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('account.provider_supply.col_supplier') }}</th>
                                        <th>{{ __('account.relationships.columns.status') }}</th>
                                        <th>{{ __('account.provider_supply.col_pending_offers') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suppliers as $relationship)
                                        @php
                                            $supplier = $relationship->supplierAccount;
                                            $label = $supplier ? ($supplier->commercial_name ?? $supplier->name ?? '#'.$supplier->id) : '—';
                                            $pending = (int) ($pendingOfferCounts[$relationship->supplier_account_id] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="text-nowrap" style="width: 3rem;">
                                                @if ($supplier && $relationship->status === 'approved')
                                                    <div class="dropdown">
                                                        <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                        </a>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('account.provider-supply.offers.review', $supplier) }}">
                                                                {{ __('account.provider_supply.action_review_offers') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="fw-medium">{{ $label }}</td>
                                            <td>{{ __('account.relationships.status.'.$relationship->status) }}</td>
                                            <td>{{ $pending }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h6 fw-semibold mb-3">{{ __('account.provider_supply.buyers_heading') }}</h2>
                    <p class="text-muted small">{{ __('account.provider_supply.buyers_intro') }}</p>
                    @if ($buyers->isEmpty())
                        <p class="text-muted mb-0">{{ __('account.provider_supply.empty_buyers') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('account.provider_supply.col_buyer') }}</th>
                                        <th>{{ __('account.relationships.columns.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($buyers as $relationship)
                                        @php
                                            $buyer = $relationship->buyerAccount;
                                            $label = $buyer ? ($buyer->commercial_name ?? $buyer->name ?? '#'.$buyer->id) : '—';
                                        @endphp
                                        <tr>
                                            <td class="text-nowrap" style="width: 3rem;">
                                                @if ($buyer && $relationship->status === 'approved')
                                                    <div class="dropdown">
                                                        <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                        </a>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="{{ route('account.provider-supply.offers.edit', $buyer) }}">
                                                                {{ __('account.provider_supply.action_offer') }}
                                                            </a>
                                                            <a class="dropdown-item" href="{{ route('account.provider-supply.price-list.edit', $buyer) }}">
                                                                {{ __('account.provider_supply.action_price_list') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="fw-medium">{{ $label }}</td>
                                            <td>{{ __('account.relationships.status.'.$relationship->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            @if ($pendingOutgoing->isNotEmpty())
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h2 class="h6 fw-semibold mb-3">{{ __('account.provider_supply.pending_out_heading') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('account.provider_supply.col_company') }}</th>
                                        <th>{{ __('account.provider_supply.col_contact') }}</th>
                                        <th>{{ __('account.provider_supply.col_expires') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingOutgoing as $outgoing)
                                        <tr>
                                            <td class="text-nowrap" style="width: 3rem;">
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
                                            <td>{{ $outgoing->company_name ?: '—' }}</td>
                                            <td class="text-muted small">{{ $outgoing->name }}<br>{{ $outgoing->email }}</td>
                                            <td class="text-muted small text-nowrap">
                                                {{ $outgoing->expires_at ? locale_date($outgoing->expires_at->timezone(config('app.timezone'))) : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
