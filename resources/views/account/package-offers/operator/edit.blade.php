@extends('layouts.base', ['title' => __('account.package_offers.operator_edit_page_title', ['agency' => $agency->commercial_name ?? $agency->name ?? $agency->nick])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.package_offers.operator_edit_title')"
                        :subtitle="$agency->commercial_name ?? $agency->name ?? $agency->nick"
                        :instructions="__('account.package_offers.operator_edit_intro')"
                    />
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="{{ route('account.package-offers.index', ['as' => 'operator']) }}" class="btn btn-sm btn-outline-secondary">
                            {{ __('account.package_offers.operator_edit_cancel') }}
                        </a>
                        <a href="{{ route('account.package-availability.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('account.package_offers.operator_edit_manage_availability') }}
                        </a>
                        @if ($packages->contains(fn ($package): bool => ($package->offer_status ?? 'none') === 'accepted'))
                            <a href="{{ route('account.package-allocations.agencies.index', $agency) }}" class="btn btn-sm btn-outline-primary">
                                {{ __('account.package_offers.operator_edit_manage_allocations') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <form id="operator-package-offer-proposals-form" method="POST" action="{{ route('account.package-offers.agencies.update', $agency) }}">
                @csrf
                @method('PUT')

                @if ($packages->isEmpty())
                    <div class="alert alert-light border">{{ __('account.package_offers.operator_edit_empty_packages') }}</div>
                @else
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('account.package_offers.operator_edit_col_package') }}</th>
                                            <th>{{ __('account.package_offers.operator_edit_col_items') }}</th>
                                            <th>{{ __('account.package_offers.operator_edit_col_offer_state') }}</th>
                                            <th>{{ __('account.package_offers.operator_edit_col_price_list') }}</th>
                                            <th class="text-end">{{ __('account.package_offers.operator_edit_col_agency_price') }}</th>
                                            <th class="text-center">{{ __('account.package_offers.operator_edit_col_propose') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($packages as $package)
                                            @php
                                                $status = $package->offer_status ?? 'none';
                                                $eligibleLists = $package->eligible_price_lists ?? collect();
                                                $selectedListId = (int) ($package->selected_price_list_id ?? 0);
                                                $agencyPrice = $package->agency_price ?? ['has_amount' => false, 'formatted' => '—'];
                                                $proposeSelectable = (bool) ($package->propose_selectable ?? false);
                                                $packageLabel = $package->displayLabel() ?: ('Package #' . $package->id);
                                            @endphp
                                            <tr>
                                                <td class="fw-medium">
                                                    {{ $packageLabel }}
                                                    <div class="small mt-1 d-flex flex-wrap gap-2">
                                                        <a href="{{ route('account.package-availability.catalogs.show', $package) }}" class="link-secondary text-decoration-none">
                                                            {{ __('account.package_offers.operator_edit_link_availability') }}
                                                        </a>
                                                        @if ($status === 'accepted')
                                                            <a href="{{ route('account.package-allocations.agencies.create', ['agency' => $agency, 'catalog' => $package->id]) }}" class="link-secondary text-decoration-none">
                                                                {{ __('account.package_offers.operator_edit_link_allocations') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-muted">{{ (int) $package->items_count }}</td>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">
                                                        {{ __('account.package_offers.operator_edit_state_' . $status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($eligibleLists->isEmpty())
                                                        <span class="text-danger">{{ __('account.package_offers.operator_edit_price_list_none') }}</span>
                                                        @if (($package->ineligibility_messages ?? []) !== [])
                                                            <ul class="small text-danger mb-0 ps-3 mt-1">
                                                                @foreach ($package->ineligibility_messages as $message)
                                                                    <li>{{ $message }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    @else
                                                        <select
                                                            name="price_list[{{ $package->id }}]"
                                                            class="form-select form-select-sm js-package-price-list"
                                                            data-package-id="{{ $package->id }}"
                                                            style="min-width: 12rem;"
                                                            @disabled(in_array($status, ['pending', 'accepted'], true))
                                                        >
                                                            @foreach ($eligibleLists as $list)
                                                                <option value="{{ $list->id }}" @selected((int) $list->id === $selectedListId)>{{ $list->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </td>
                                                <td class="text-end @if ($agencyPrice['has_amount'] ?? false) fw-medium @else text-muted @endif">
                                                    {{ $agencyPrice['formatted'] ?? '—' }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($status === 'accepted')
                                                        <span class="text-muted">—</span>
                                                    @elseif ($status === 'pending')
                                                        <input
                                                            type="checkbox"
                                                            class="form-check-input"
                                                            checked
                                                            disabled
                                                            title="{{ __('account.package_offers.operator_edit_pending_locked_hint') }}"
                                                        >
                                                        @if ($package->offer_id)
                                                            <button
                                                                type="submit"
                                                                form="revoke-package-offer-{{ $package->offer_id }}"
                                                                class="btn btn-sm btn-outline-danger mt-1 d-block mx-auto"
                                                                onclick="return confirm(@js(__('account.package_offers.operator_edit_revoke_confirm')))"
                                                            >
                                                                {{ __('account.package_offers.operator_edit_revoke') }}
                                                            </button>
                                                        @endif
                                                    @else
                                                        <input
                                                            type="checkbox"
                                                            class="form-check-input js-propose-package"
                                                            @if ($proposeSelectable) name="propose[]" @endif
                                                            value="{{ $package->id }}"
                                                            data-package-label="{{ $packageLabel }}"
                                                            data-has-price-list="{{ $eligibleLists->isNotEmpty() ? '1' : '0' }}"
                                                            @disabled(! $proposeSelectable)
                                                            @if (! $proposeSelectable)
                                                                title="{{ __('account.package_offers.operator_edit_zero_price_hint') }}"
                                                            @endif
                                                        >
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">{{ __('account.package_offers.operator_edit_save') }}</button>
                    </div>
                @endif
            </form>

            @foreach ($packages as $package)
                @if (($package->offer_status ?? 'none') === 'pending' && $package->offer_id)
                    <form
                        id="revoke-package-offer-{{ $package->offer_id }}"
                        method="POST"
                        action="{{ route('account.package-offers.agencies.revoke', ['agency' => $agency, 'offer' => $package->offer_uuid]) }}"
                        class="d-none"
                    >
                        @csrf
                    </form>
                @endif
            @endforeach
        </div>
    </section>

    <x-site-footer-simple />
@endsection

@section('script-bottom')
    <script>
        window.addEventListener('load', function () {
            var form = document.getElementById('operator-package-offer-proposals-form');
            if (! form) {
                return;
            }

            form.addEventListener('submit', function (event) {
                var missingLabels = [];
                form.querySelectorAll('.js-propose-package:checked').forEach(function (checkbox) {
                    if (checkbox.getAttribute('data-has-price-list') !== '1') {
                        missingLabels.push(checkbox.getAttribute('data-package-label') || '—');
                    }
                });

                if (missingLabels.length === 0) {
                    return;
                }

                var message = @js(__('account.package_offers.operator_edit_no_price_list_confirm')).replace(':packages', missingLabels.join(', '));
                if (! window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
