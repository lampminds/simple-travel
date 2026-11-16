@extends('layouts.base', ['title' => __('account.provider_supply.review_page_title', ['company' => $counterpartLabel])])

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

            <x-account-page-header
                :title="__('account.provider_supply.review_heading')"
                :subtitle="$counterpartLabel"
                :instructions="__('account.provider_supply.review_intro')"
            />

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    @if ($offers->isEmpty())
                        <p class="text-muted mb-0">{{ __('account.provider_supply.review_empty') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ __('account.provider_supply.col_variant') }}</th>
                                        <th>{{ __('account.provider_supply.col_offer_status') }}</th>
                                        <th>{{ __('account.provider_supply.col_availability') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offers as $offer)
                                        <tr>
                                            <td class="text-nowrap" style="width: 3rem;">
                                                @if ($offer->status === 'pending')
                                                    <div class="dropdown">
                                                        <a class="btn-link text-muted dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="icon icon-xs" data-feather="more-horizontal"></i>
                                                        </a>
                                                        <div class="dropdown-menu">
                                                            <form method="post" action="{{ route('account.provider-supply.offers.accept', [$counterpart, $offer]) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">{{ __('account.provider_supply.action_accept_offer') }}</button>
                                                            </form>
                                                            <form method="post" action="{{ route('account.provider-supply.offers.reject', [$counterpart, $offer]) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">{{ __('account.provider_supply.action_reject_offer') }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $offer->serviceVariant?->dropdownLabel() ?? ('#'.$offer->service_variant_id) }}</td>
                                            <td>{{ __('account.provider_supply.offer_status.'.$offer->status) }}</td>
                                            <td>{{ __('account.provider_supply.availability.'.$offer->availability) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('account.relationships.index') }}" class="btn btn-outline-secondary">{{ __('account.provider_supply.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
