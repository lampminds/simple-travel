@php
    $summary = \App\Support\BookingPassengersSnapshot::formatSummary($booking->passengers_snapshot);
@endphp
<dt class="col-sm-4">{{ __('account.reservations.show_passengers') }}</dt>
<dd class="col-sm-8">
    @if ($fieldAnswers['has_passenger_details'])
        <ol class="mb-0 ps-3">
            @foreach ($fieldAnswers['passengers'] as $passenger)
                <li class="mb-2">
                    <span class="fw-medium">
                        @if ($passenger['age'] !== null)
                            {{ __('account.reservations.show_passenger_age', ['age' => $passenger['age']]) }}
                        @else
                            {{ __('account.reservations.show_passenger_unknown_age') }}
                        @endif
                    </span>
                    @if ($passenger['fields'] !== [])
                        <ul class="mb-0">
                            @foreach ($passenger['fields'] as $field)
                                <li>{{ $field['label'] }}: {{ $field['display'] }}</li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ol>
    @else
        {{ $summary }}
    @endif
</dd>
@if ($fieldAnswers['booking'] !== [])
    <dt class="col-sm-4">{{ __('account.reservations.show_booking_fields') }}</dt>
    <dd class="col-sm-8">
        <ul class="mb-0 ps-3">
            @foreach ($fieldAnswers['booking'] as $field)
                <li>{{ $field['label'] }}: {{ $field['display'] }}</li>
            @endforeach
        </ul>
    </dd>
@endif
