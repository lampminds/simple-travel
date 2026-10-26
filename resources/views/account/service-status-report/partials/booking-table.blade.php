@if ($rows === [])
    <p class="text-muted mb-0">{{ $empty }}</p>
@else
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('account.service_status_report.col_booking_code') }}</th>
                    <th>{{ __('account.service_status_report.col_service_dates') }}</th>
                    <th>{{ __('account.service_status_report.col_travel_dates') }}</th>
                    <th>{{ __('account.service_status_report.col_operator') }}</th>
                    <th>{{ __('account.service_status_report.col_agency') }}</th>
                    <th>{{ __('account.service_status_report.col_package') }}</th>
                    <th>{{ __('account.service_status_report.col_variant') }}</th>
                    <th class="text-end">{{ __('account.service_status_report.col_quantity') }}</th>
                    <th>{{ __('account.service_status_report.col_passengers') }}</th>
                    <th>{{ __('account.service_status_report.col_booking_status') }}</th>
                    <th>{{ __('account.service_status_report.col_item_status') }}</th>
                    <th>{{ __('account.service_status_report.col_reference') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="text-nowrap"><code>{{ $row['code'] }}</code></td>
                        <td class="text-nowrap">{{ $row['service_dates'] }}</td>
                        <td class="text-nowrap">{{ $row['travel_dates'] }}</td>
                        <td>{{ $row['operator'] }}</td>
                        <td>{{ $row['agency'] }}</td>
                        <td>{{ $row['package'] }}</td>
                        <td>{{ $row['variant'] }}</td>
                        <td class="text-end">{{ $row['quantity'] }}</td>
                        <td>{{ $row['passengers'] }}</td>
                        <td>{{ $row['booking_status'] }}</td>
                        <td>{{ $row['item_status'] }}</td>
                        <td>{{ $row['reference'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
