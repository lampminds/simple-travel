@extends('layouts.base', ['title' => __('account.availability.index_page_title')])

@section('content')
    @include('layouts.partials.dashboard-navbar', ['fixedWidth' => true, 'sticky' => false, 'topbarColor' => 'navbar-light', 'classList' => 'mx-auto'])

    <section class="position-relative p-3 bg-gradient2">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success mb-3" role="alert">{{ session('status') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <x-account-page-header
                        :title="__('account.availability.index_title')"
                        :instructions="__('account.availability.index_intro_instructions')"
                    />
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    @if ($services->isEmpty())
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-0">{{ __('account.availability.index_empty') }}</p>
                            </div>
                        </div>
                    @else
                        @foreach ($services as $service)
                            @php
                                $serviceName = trim($service->name ?? '');
                                if ($serviceName === '') {
                                    $serviceName = '#' . $service->id;
                                }
                            @endphp
                            <div class="card mb-4">
                                <div class="card-header bg-white">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                                        <div>
                                            <h5 class="mb-1">{{ $serviceName }}</h5>
                                            <div class="small text-muted">
                                                {{ __('account.availability.service_summary_counts', [
                                                    'rules' => number_format((int) $service->availability_rules_count),
                                                    'overrides' => number_format((int) $service->availability_overrides_count),
                                                    'variants' => number_format($service->serviceVariants->count()),
                                                ]) }}
                                            </div>
                                        </div>
                                        <a href="{{ route('account.availability.services.show', $service) }}" class="btn btn-sm btn-primary">
                                            {{ __('account.availability.service_manage_button') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    @if ($service->serviceVariants->isEmpty())
                                        <p class="text-muted mb-0 p-3">{{ __('account.availability.service_variants_empty') }}</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('account.availability.columns.variant') }}</th>
                                                        <th>{{ __('account.availability.columns.inventory') }}</th>
                                                        <th>{{ __('account.availability.columns.rules_count') }}</th>
                                                        <th>{{ __('account.availability.columns.overrides_count') }}</th>
                                                        <th class="text-end">{{ __('account.availability.columns.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($service->serviceVariants as $variant)
                                                        @php
                                                            $variantLabel = trim($variant->name ?? '') !== '' ? $variant->name : $variant->sku;
                                                            $inventoryLabel = __('wizard.variant_inventory.' . $variant->inventory_type);
                                                            if ($variant->inventory_type !== 'unlimited' && $variant->inventory_total !== null) {
                                                                $inventoryLabel .= ' (' . number_format((int) $variant->inventory_total) . ')';
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $variantLabel }}</td>
                                                            <td>{{ $inventoryLabel }}</td>
                                                            <td>{{ number_format((int) $variant->availability_rules_count) }}</td>
                                                            <td>{{ number_format((int) $variant->availability_overrides_count) }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('account.availability.variants.show', $variant) }}" class="btn btn-sm btn-outline-primary">
                                                                    {{ __('account.availability.variant_manage_button') }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-site-footer-simple />
@endsection
