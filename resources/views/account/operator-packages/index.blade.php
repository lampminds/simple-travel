@extends('layouts.base', ['title' => __('account.operator_packages.page_title')])

@section('content')

    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">
                    {{ session('status') }}
                </div>
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
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.operator_packages.heading')"
                            :subtitle="$account->commercial_name ?? $account->name ?? $account->nick"
                            :instructions="__('account.operator_packages.intro_instructions')"
                        />
                        <div>
                            <a href="{{ route('account.operator-packages.create') }}" class="btn btn-primary">
                                {{ __('account.operator_packages.create_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($packages->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.operator_packages.empty') }}
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('account.operator_packages.columns.name') }}</th>
                                            <th>{{ __('account.operator_packages.columns.status') }}</th>
                                            <th>{{ __('account.operator_packages.columns.items') }}</th>
                                            <th>{{ __('account.operator_packages.columns.featured') }}</th>
                                            <th>{{ __('account.operator_packages.columns.public') }}</th>
                                            <th class="text-end">{{ __('account.operator_packages.columns.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($packages as $package)
                                            <tr>
                                                <td class="fw-semibold">{{ $package->displayLabel() }}</td>
                                                <td>{{ __('account.operator_packages.status.'.$package->status) }}</td>
                                                <td>{{ $package->items_count }}</td>
                                                <td>{{ $package->is_featured ? '✓' : '—' }}</td>
                                                <td>{{ $package->is_public ? '✓' : '—' }}</td>
                                                <td class="text-end text-nowrap">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-secondary me-1"
                                                        onclick="Livewire.dispatch('open-operator-package-preview', { packageUuid: @js($package->uuid) })"
                                                    >
                                                        {{ __('account.operator_packages.preview_button') }}
                                                    </button>
                                                    <a href="{{ route('account.operator-packages.edit', $package) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ __('account.operator_packages.edit_button') }}
                                                    </a>
                                                    <form method="POST" action="{{ route('account.operator-packages.destroy', $package) }}" class="d-inline" onsubmit="return confirm(@json(__('account.operator_packages.delete_confirm')))">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            {{ __('account.operator_packages.delete_button') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-3">
                            {{ $packages->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <livewire:account.operator-package-preview-modal />

@endsection
