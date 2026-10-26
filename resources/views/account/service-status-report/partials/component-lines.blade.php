<div class="table-responsive">
    <table class="table table-sm table-bordered align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>{{ __('account.service_status_report.col_component') }}</th>
                <th class="text-end">{{ __('account.service_status_report.col_quantity') }}</th>
                <th>{{ __('account.service_status_report.col_day') }}</th>
                <th>{{ __('account.service_status_report.col_inclusion') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lines as $line)
                <tr>
                    <td>{{ $line['label'] }}</td>
                    <td class="text-end">{{ $line['quantity'] }}</td>
                    <td>{{ $line['day'] }}</td>
                    <td>{{ $line['inclusion'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
