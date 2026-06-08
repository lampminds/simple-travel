@php
    $clientService = app(\App\Services\AgencyClientService::class);
    $typeTabs = [
        'all' => __('account.clients.filter_all'),
        'person' => __('account.clients.filter_person'),
        'organization' => __('account.clients.filter_organization'),
    ];
@endphp

@extends('layouts.base', ['title' => __('account.clients.page_title')])

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
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.clients.heading')"
                            :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                            :instructions="__('account.clients.intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                            <a href="{{ route('account.clients.persons.create') }}" class="btn btn-outline-primary">
                                {{ __('account.clients.create_person_button') }}
                            </a>
                            <a href="{{ route('account.clients.organizations.create') }}" class="btn btn-primary">
                                {{ __('account.clients.create_organization_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="GET" action="{{ route('account.clients.index') }}" class="row g-3 align-items-end">
                                <input type="hidden" name="type" value="{{ $type }}">
                                <div class="col-md-8">
                                    <label for="client-search" class="form-label">{{ __('account.clients.search_label') }}</label>
                                    <input
                                        type="search"
                                        id="client-search"
                                        name="search"
                                        class="form-control"
                                        value="{{ $search }}"
                                        placeholder="{{ __('account.clients.search_placeholder') }}"
                                    >
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">{{ __('account.clients.search_button') }}</button>
                                    @if ($search !== '')
                                        <a href="{{ route('account.clients.index', ['type' => $type]) }}" class="btn btn-light">
                                            {{ __('account.clients.search_clear') }}
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <ul class="nav nav-pills mb-3 gap-2">
                        @foreach ($typeTabs as $tabKey => $tabLabel)
                            <li class="nav-item">
                                <a
                                    class="nav-link @if ($type === $tabKey) active @endif"
                                    href="{{ route('account.clients.index', array_filter(['type' => $tabKey, 'search' => $search !== '' ? $search : null])) }}"
                                >
                                    {{ $tabLabel }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if (in_array($type, ['all', 'person'], true))
                        <div class="card mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">{{ __('account.clients.section_persons') }}</h5>
                            </div>
                            @if ($personClients === null || $personClients->isEmpty())
                                <div class="card-body text-muted">
                                    {{ __('account.clients.empty_persons') }}
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.clients.columns.name') }}</th>
                                                <th>{{ __('account.clients.columns.organization') }}</th>
                                                <th>{{ __('account.clients.columns.email') }}</th>
                                                <th>{{ __('account.clients.columns.phone') }}</th>
                                                <th class="text-end">{{ __('account.clients.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($personClients as $person)
                                                <tr>
                                                    <td class="fw-semibold">{{ $person->resolveFullName() }}</td>
                                                    <td class="text-muted">{{ $clientService->organizationLabelForPersonClient($person, (int) $account->id) ?? '—' }}</td>
                                                    <td class="text-muted">{{ $clientService->primaryEmailForPerson($person) ?? '—' }}</td>
                                                    <td class="text-muted">{{ $clientService->primaryPhoneForPerson($person) ?? '—' }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-end gap-2">
                                                            <a href="{{ route('account.clients.persons.edit', $person) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.clients.edit_button') }}
                                                            </a>
                                                            <form
                                                                method="POST"
                                                                action="{{ route('account.clients.persons.destroy', $person) }}"
                                                                onsubmit="return confirm(@json(__('account.clients.delete_person_confirm')))"
                                                            >
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    {{ __('account.clients.delete_button') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($type !== 'all' && $personClients->hasPages())
                                    <div class="card-body pt-0">
                                        {{ $personClients->links() }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    @if (in_array($type, ['all', 'organization'], true))
                        <div class="card">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">{{ __('account.clients.section_organizations') }}</h5>
                            </div>
                            @if ($organizationClients === null || $organizationClients->isEmpty())
                                <div class="card-body text-muted">
                                    {{ __('account.clients.empty_organizations') }}
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('account.clients.columns.legal_name') }}</th>
                                                <th>{{ __('account.clients.columns.trade_name') }}</th>
                                                <th>{{ __('account.clients.columns.city') }}</th>
                                                <th>{{ __('account.clients.columns.website') }}</th>
                                                <th class="text-end">{{ __('account.clients.columns.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($organizationClients as $organization)
                                                <tr>
                                                    <td class="fw-semibold">{{ $organization->legal_name }}</td>
                                                    <td class="text-muted">{{ $organization->trade_name ?? '—' }}</td>
                                                    <td class="text-muted">{{ $clientService->billingCityLabelForOrganization($organization) ?? '—' }}</td>
                                                    <td class="text-muted">
                                                        @if ($organization->website)
                                                            <a href="{{ $organization->website }}" target="_blank" rel="noopener noreferrer">
                                                                {{ $organization->website }}
                                                            </a>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-end gap-2">
                                                            <a href="{{ route('account.clients.organizations.edit', $organization) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('account.clients.edit_button') }}
                                                            </a>
                                                            <form
                                                                method="POST"
                                                                action="{{ route('account.clients.organizations.destroy', $organization) }}"
                                                                onsubmit="return confirm(@json(__('account.clients.delete_organization_confirm')))"
                                                            >
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    {{ __('account.clients.delete_button') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($type !== 'all' && $organizationClients->hasPages())
                                    <div class="card-body pt-0">
                                        {{ $organizationClients->links() }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />

@endsection
