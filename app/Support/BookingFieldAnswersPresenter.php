<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Booking;
use App\Models\BookingFieldAnswer;
use App\Models\LmpCountry;
use App\Models\ProviderBookingField;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Groups stored booking answers for the agency and operator detail pages.
 */
final class BookingFieldAnswersPresenter
{
    /**
     * @return array{
     *     has_passenger_details: bool,
     *     passengers: list<array{age: int|null, fields: list<array{label: string, display: string}>}>,
     *     booking: list<array{label: string, display: string}>
     * }
     */
    public static function present(Booking $booking): array
    {
        $booking->loadMissing('fieldAnswers');

        $ages = [];
        $snapshot = is_array($booking->passengers_snapshot) ? $booking->passengers_snapshot : [];
        if (isset($snapshot['ages']) && is_array($snapshot['ages'])) {
            foreach ($snapshot['ages'] as $age) {
                $ages[] = (int) $age;
            }
        }

        $answers = $booking->fieldAnswers;
        $countries = self::countryNames($answers);

        $passengers = [];
        foreach ($ages as $index => $age) {
            $passengers[$index] = [
                'age' => $age,
                'fields' => [],
            ];
        }

        $bookingRows = [];
        $hasPassengerDetails = false;

        foreach ($answers as $answer) {
            if ($answer->scope_snapshot === ProviderBookingField::SCOPE_PASSENGER) {
                $index = $answer->passenger_index;
                if ($index === null) {
                    continue;
                }

                $hasPassengerDetails = true;
                if (! isset($passengers[$index])) {
                    $passengers[$index] = [
                        'age' => $ages[$index] ?? null,
                        'fields' => [],
                    ];
                }

                $passengers[$index]['fields'][] = [
                    'label' => (string) $answer->label_snapshot,
                    'display' => self::displayValue($answer, $countries),
                ];

                continue;
            }

            $bookingRows[] = [
                'label' => (string) $answer->label_snapshot,
                'display' => self::displayValue($answer, $countries),
            ];
        }

        ksort($passengers);

        return [
            'has_passenger_details' => $hasPassengerDetails,
            'passengers' => array_values($passengers),
            'booking' => $bookingRows,
        ];
    }

    /**
     * @param  Collection<int, BookingFieldAnswer>  $answers
     * @return Collection<int|string, string>
     */
    private static function countryNames(Collection $answers): Collection
    {
        $ids = [];
        foreach ($answers as $answer) {
            if ($answer->input_type_snapshot !== ProviderBookingField::TYPE_COUNTRY) {
                continue;
            }

            if (preg_match('/^\d+$/', (string) $answer->value) === 1) {
                $ids[] = (int) $answer->value;
            }
        }

        if ($ids === []) {
            return collect();
        }

        return LmpCountry::query()->whereIn('id', array_values(array_unique($ids)))->pluck('name', 'id');
    }

    /**
     * @param  Collection<int|string, string>  $countries
     */
    private static function displayValue(BookingFieldAnswer $answer, Collection $countries): string
    {
        $value = (string) $answer->value;

        if ($answer->input_type_snapshot === ProviderBookingField::TYPE_COUNTRY) {
            $name = $countries[(int) $value] ?? null;

            return is_string($name) && $name !== '' ? $name : $value;
        }

        if ($answer->input_type_snapshot === ProviderBookingField::TYPE_DATE) {
            try {
                return locale_date(Carbon::parse($value));
            } catch (\Throwable) {
                return $value;
            }
        }

        if ($answer->input_type_snapshot === ProviderBookingField::TYPE_TIME && preg_match('/^\d{2}:\d{2}/', $value) === 1) {
            return substr($value, 0, 5);
        }

        return $value;
    }
}
