@extends('layouts.base', ['title' => __('account.passenger_policies.provider_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-start gap-3">
                <x-account-page-header
                    class="flex-grow-1"
                    :title="__('account.passenger_policies.provider_heading')"
                    :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                    :instructions="__('account.passenger_policies.provider_instructions')"
                />
                <a class="btn btn-primary" href="{{ route('account.passenger-policies.create') }}">
                    {{ __('account.passenger_policies.add') }}
                </a>
            </div>

            <div class="card mt-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 3rem"></th>
                                <th>{{ __('account.passenger_policies.name') }}</th>
                                <th>{{ __('account.passenger_policies.bands_count') }}</th>
                                <th>{{ __('account.passenger_policies.services_count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($policies as $policy)
                                <tr>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-body px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('account.passenger_policies.actions') }}">
                                                <i class="icon-xs" data-feather="more-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('account.passenger-policies.edit', $policy) }}">{{ __('account.passenger_policies.edit') }}</a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="submit" form="delete-policy-{{ $policy->id }}">{{ __('account.passenger_policies.delete') }}</button>
                                                </li>
                                            </ul>
                                        </div>
                                        <form id="delete-policy-{{ $policy->id }}" method="POST" action="{{ route('account.passenger-policies.destroy', $policy) }}" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                    <td>{{ $policy->name }}</td>
                                    <td>{{ $policy->rules_count }}</td>
                                    <td>{{ $policy->services_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted">{{ __('account.passenger_policies.empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
