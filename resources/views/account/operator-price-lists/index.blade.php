@extends('layouts.base', ['title' => __('account.operator_price_lists.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <div class="page-title">
                            <h3 class="my-0">{{ __('account.operator_price_lists.heading') }}</h3>
                            <p class="mt-1 fw-medium text-muted mb-0">
                                {{ __('account.operator_price_lists.intro', ['account' => $account->commercial_name ?? $account->name ?? $account->nick]) }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('account.operator-price-lists.create') }}" class="btn btn-primary">
                                {{ __('account.operator_price_lists.create_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($priceLists->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.operator_price_lists.empty') }}
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('account.operator_price_lists.columns.name') }}</th>
                                            <th>{{ __('account.operator_price_lists.columns.currency') }}</th>
                                            <th>{{ __('account.operator_price_lists.columns.validity') }}</th>
                                            <th>{{ __('account.operator_price_lists.columns.items') }}</th>
                                            <th>{{ __('account.operator_price_lists.columns.assignments') }}</th>
                                            <th>{{ __('account.operator_price_lists.columns.active') }}</th>
                                            <th class="text-end">{{ __('account.operator_price_lists.columns.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($priceLists as $priceList)
                                            <tr>
                                                <td class="fw-semibold">{{ $priceList->name }}</td>
                                                <td>{{ $priceList->currency?->display_name ?? '—' }}</td>
                                                <td>
                                                    @if ($priceList->valid_from || $priceList->valid_to)
                                                        {{ $priceList->valid_from?->format('Y-m-d') ?? '—' }} → {{ $priceList->valid_to?->format('Y-m-d') ?? '—' }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $priceList->items_count }}</td>
                                                <td>
                                                    @php
                                                        $opCount = (int) ($priceList->agency_assignments_count ?? 0);
                                                    @endphp
                                                    <a
                                                        href="{{ route('account.operator-price-lists.assignments.edit', $priceList) }}"
                                                        class="d-inline-flex align-items-center gap-2 text-decoration-none link-primary"
                                                        title="{{ __('account.operator_price_lists.assignments_manage') }}"
                                                    >
                                                        <span class="text-decoration-underline">{{ trans_choice('account.operator_price_lists.assignments_agencies_choice', $opCount, ['count' => $opCount]) }}</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="1rem" height="1rem" fill="currentColor" class="flex-shrink-0 opacity-75" viewBox="0 0 16 16" aria-hidden="true">
                                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5V12h-.5a.5.5 0 0 1-.5.5H13.5a.5.5 0 0 1-.5-.5v-1.793l6.147-6.147z"/>
                                                        </svg>
                                                        <span class="visually-hidden">{{ __('account.operator_price_lists.assignments_manage') }}</span>
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $priceList->is_active ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' }}">
                                                        {{ $priceList->is_active ? __('account.operator_price_lists.active_yes') : __('account.operator_price_lists.active_no') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('account.operator-price-lists.edit', $priceList) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.operator_price_lists.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.operator-price-lists.destroy', $priceList) }}" onsubmit="return confirm('{{ __('account.operator_price_lists.delete_confirm') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                {{ __('account.operator_price_lists.delete_button') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            {{ $priceLists->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
