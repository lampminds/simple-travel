@if ($rows === [])
    <p class="text-muted mb-0">{{ $empty }}</p>
@else
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('account.service_status_report.col_operator') }}</th>
                    <th>{{ __('account.service_status_report.col_package') }}</th>
                    <th>{{ __('account.service_status_report.col_package_status') }}</th>
                    <th>{{ __('account.service_status_report.col_variant') }}</th>
                    <th>{{ __('account.service_status_report.col_day') }}</th>
                    <th class="text-end">{{ __('account.service_status_report.col_quantity') }}</th>
                    <th>{{ __('account.service_status_report.col_inclusion') }}</th>
                    <th>{{ __('account.service_status_report.col_offer') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['operator'] }}</td>
                        <td>{{ $row['package'] }}</td>
                        <td>{{ $row['package_status'] }}</td>
                        <td>{{ $row['variant'] }}</td>
                        <td>{{ $row['day'] }}</td>
                        <td class="text-end">{{ $row['quantity'] }}</td>
                        <td>{{ $row['inclusion'] }}</td>
                        <td>{{ $row['offer'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
