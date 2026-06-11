@if ($breakdown['has_lines'] ?? false)
    <div class="card mt-3">
        <div class="card-header bg-white p-0">
            <button
                class="btn btn-link text-decoration-none text-dark w-100 text-start px-3 py-3 d-flex align-items-center justify-content-between"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#booking-price-breakdown"
                aria-expanded="false"
                aria-controls="booking-price-breakdown"
            >
                <span class="fw-medium">{{ __('account.reservations.price_breakdown.heading') }}</span>
                <i class="icon-xs" data-feather="chevron-down"></i>
            </button>
        </div>
        <div id="booking-price-breakdown" class="collapse">
            <div class="card-body pt-0">
                <p class="text-muted small mb-3">{{ __('account.reservations.price_breakdown.intro') }}</p>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('account.reservations.price_breakdown.col_component') }}</th>
                                <th>{{ __('account.reservations.price_breakdown.col_pricing') }}</th>
                                <th class="text-end">{{ __('account.reservations.price_breakdown.col_unit') }}</th>
                                <th>{{ __('account.reservations.price_breakdown.col_quantity') }}</th>
                                <th class="text-end">{{ __('account.reservations.price_breakdown.col_total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($breakdown['lines'] as $line)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $line['label'] }}</div>
                                        @if ($line['provider_name'])
                                            <div class="text-muted small">{{ $line['provider_name'] }}</div>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $line['pricing_type_label'] }}</td>
                                    <td class="text-end text-nowrap">{{ $line['unit_price_formatted'] }}</td>
                                    <td class="small">{{ $line['quantity_explanation'] }}</td>
                                    <td class="text-end text-nowrap fw-medium">{{ $line['line_total_formatted'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr>
                                <td colspan="4" class="text-end">{{ __('account.reservations.price_breakdown.lines_subtotal') }}</td>
                                <td class="text-end text-nowrap">{{ $breakdown['lines_subtotal_formatted'] }}</td>
                            </tr>
                            @if ($breakdown['adjustment'] !== null)
                                <tr>
                                    <td colspan="4" class="text-end text-muted small">
                                        {{ $breakdown['adjustment']['label'] }}
                                    </td>
                                    <td class="text-end text-nowrap text-muted small">
                                        {{ $breakdown['adjustment']['amount_formatted'] }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end fw-medium">{{ __('account.reservations.price_breakdown.grand_total') }}</td>
                                <td class="text-end text-nowrap fw-medium">{{ $breakdown['grand_total_formatted'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
