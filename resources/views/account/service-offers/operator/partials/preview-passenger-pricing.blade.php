@php
    /** @var array{policy_name?: string|null, note?: string, headers?: list<string>, rows?: list<list<string>>} $pricing */
    $forPdf = $forPdf ?? false;
    $headers = is_array($pricing['headers'] ?? null) ? $pricing['headers'] : [];
    $rows = is_array($pricing['rows'] ?? null) ? $pricing['rows'] : [];
@endphp

@if ($forPdf)
    <div class="preview-section-block">
        <div class="preview-section-title">{{ __('account.service_offers.operator_passenger_title') }}</div>
        @if (($pricing['policy_name'] ?? '') !== '')
            <div class="preview-line">
                <span class="preview-label">{{ __('account.service_offers.operator_passenger_policy') }}:</span>
                <span class="preview-value">{{ $pricing['policy_name'] }}</span>
            </div>
        @endif
        @if (($pricing['note'] ?? '') !== '')
            <div class="preview-muted">{{ $pricing['note'] }}</div>
        @endif
        @if ($headers !== [] && $rows !== [])
            <table class="preview-table">
                <thead>
                    <tr>
                        @foreach ($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@else
    <div class="mb-4 pb-3 border-bottom">
        <div class="text-muted text-uppercase fs-12 mb-1">{{ __('account.service_offers.operator_passenger_title') }}</div>
        @if (($pricing['policy_name'] ?? '') !== '')
            <div class="fw-medium mb-1">{{ $pricing['policy_name'] }}</div>
        @endif
        @if (($pricing['note'] ?? '') !== '')
            <div class="small text-muted mb-2">{{ $pricing['note'] }}</div>
        @endif
        @if ($headers !== [] && $rows !== [])
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            @foreach ($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
