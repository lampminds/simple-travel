@php
    $operatorLabel = $operator->commercial_name ?? $operator->name ?? ('#' . $operator->id);
@endphp

@extends('layouts.base', ['title' => __('account.allocations.index_page_title', ['operator' => $operatorLabel])])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                        <x-account-page-header
                            class="flex-grow-1"
                            :title="__('account.allocations.index_title')"
                            :subtitle="$operatorLabel"
                            :instructions="__('account.allocations.index_intro_instructions')"
                        />
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('account.allocations.index') }}" class="btn btn-light">
                                {{ __('account.allocations.back_to_operators') }}
                            </a>
                            <a href="{{ route('account.allocations.operators.create', $operator) }}" class="btn btn-primary">
                                {{ __('account.allocations.create_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($allocations->isEmpty())
                        <div class="card">
                            <div class="card-body text-muted">
                                {{ __('account.allocations.empty') }}
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('account.allocations.columns.target') }}</th>
                                            <th>{{ __('account.allocations.columns.type') }}</th>
                                            <th>{{ __('account.allocations.columns.capacity') }}</th>
                                            <th>{{ __('account.allocations.columns.validity') }}</th>
                                            <th>{{ __('account.allocations.columns.active') }}</th>
                                            <th class="text-end">{{ __('account.allocations.columns.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allocations as $allocation)
                                            <tr>
                                                <td class="fw-medium">{{ $allocation->target_label }}</td>
                                                <td>{{ __('account.allocations.types.' . $allocation->allocation_type) }}</td>
                                                <td>
                                                    @if ($allocation->allocation_type === \App\Models\Allocation::TYPE_FREE_SALE)
                                                        <span class="text-muted">—</span>
                                                    @else
                                                        {{ number_format((int) $allocation->capacity) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($allocation->start_date || $allocation->end_date)
                                                        {{ locale_date_range(
                                                            $allocation->start_date,
                                                            $allocation->end_date,
                                                            null,
                                                            __('account.allocations.validity_open'),
                                                        ) }}
                                                    @else
                                                        <span class="text-muted">{{ __('account.allocations.validity_open') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($allocation->active)
                                                        <span class="badge bg-success-subtle text-success">{{ __('account.allocations.active_yes') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ __('account.allocations.active_no') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                                        <a href="{{ route('account.allocations.edit', $allocation) }}" class="btn btn-sm btn-outline-primary">
                                                            {{ __('account.allocations.edit_button') }}
                                                        </a>
                                                        <form method="POST" action="{{ route('account.allocations.destroy', $allocation) }}" class="d-inline" onsubmit="return confirm(@json(__('account.allocations.delete_confirm')));">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                {{ __('account.allocations.delete_button') }}
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
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
