<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageItem;
use App\Models\PackageOffer;
use App\Models\ProviderBookingField;
use App\Support\ResolvedBookingField;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Union of active booking fields required by the included services of a package.
 *
 * The same field is asked once. It is required when any included service marks it required.
 */
final class BookingRequiredFieldsResolver
{
    /**
     * @return array{passenger: list<ResolvedBookingField>, booking: list<ResolvedBookingField>}
     */
    public function forPackageOffer(PackageOffer $offer): array
    {
        $offer->loadMissing([
            'catalog.items.serviceVariant.service.bookingFieldAssignments.field.translations.language.locale',
        ]);

        $grouped = [];
        $items = $offer->catalog?->items;

        if ($items !== null) {
            foreach ($items as $item) {
                if (! $item instanceof OperatorPackageItem || $item->inclusion_mode !== 'included') {
                    continue;
                }

                $service = $item->serviceVariant?->service;
                if ($service === null) {
                    continue;
                }

                foreach ($service->bookingFieldAssignments as $assignment) {
                    $field = $assignment->field;
                    if ($field === null || ! $field->is_active) {
                        continue;
                    }

                    $id = (int) $field->id;
                    if (! isset($grouped[$id])) {
                        $grouped[$id] = [
                            'field' => $field,
                            'required' => (bool) $assignment->is_required,
                            'sort' => (int) $assignment->sort_order,
                        ];

                        continue;
                    }

                    $grouped[$id]['required'] = $grouped[$id]['required'] || (bool) $assignment->is_required;
                    $grouped[$id]['sort'] = min($grouped[$id]['sort'], (int) $assignment->sort_order);
                }
            }
        }

        $passenger = [];
        $booking = [];

        foreach ($grouped as $row) {
            /** @var ProviderBookingField $field */
            $field = $row['field'];
            $label = $field->labelForDisplay();
            if ($label === '') {
                $label = (string) __('account.booking_fields.untitled');
            }

            $resolved = new ResolvedBookingField(
                id: (int) $field->id,
                scope: (string) $field->scope,
                inputType: (string) $field->input_type,
                required: (bool) $row['required'],
                sortOrder: (int) $row['sort'],
                label: $label,
                help: $field->helpForDisplay(),
            );

            if ($resolved->scope === ProviderBookingField::SCOPE_PASSENGER) {
                $passenger[] = $resolved;
            } else {
                $booking[] = $resolved;
            }
        }

        $sort = static function (ResolvedBookingField $left, ResolvedBookingField $right): int {
            return [$left->sortOrder, $left->id] <=> [$right->sortOrder, $right->id];
        };
        usort($passenger, $sort);
        usort($booking, $sort);

        return [
            'passenger' => $passenger,
            'booking' => $booking,
        ];
    }

    /**
     * @param  array{passenger: list<ResolvedBookingField>, booking: list<ResolvedBookingField>}  $resolved
     * @return array{rules: array<string, mixed>, messages: array<string, string>, attributes: array<string, string>}
     */
    public function validationSpec(array $resolved, int $passengerCount): array
    {
        $rules = [];
        $messages = [];
        $attributes = [];

        foreach ($resolved['passenger'] as $field) {
            $key = 'passenger_fields.'.$field->id;
            $rules[$key] = [$field->required ? 'required' : 'nullable', 'array', 'size:'.$passengerCount];
            $rules[$key.'.*'] = $this->valueRules($field);
            $attributes[$key] = $field->label;
            $messages[$key.'.size'] = (string) __('account.reservations.validation.passenger_field_count', [
                'field' => $field->label,
            ]);

            for ($index = 0; $index < $passengerCount; $index++) {
                $attributes[$key.'.'.$index] = $field->label;
            }
        }

        foreach ($resolved['booking'] as $field) {
            $key = 'booking_fields.'.$field->id;
            $rules[$key] = $this->valueRules($field);
            $attributes[$key] = $field->label;
        }

        return [
            'rules' => $rules,
            'messages' => $messages,
            'attributes' => $attributes,
        ];
    }

    /**
     * Rows ready to insert, using the field definition at booking time.
     *
     * @param  array{passenger: list<ResolvedBookingField>, booking: list<ResolvedBookingField>}  $resolved
     * @param  array<string, mixed>  $input
     * @param  int  $passengerCount
     * @return list<array{
     *     provider_booking_field_id: int,
     *     passenger_index: int|null,
     *     label_snapshot: string,
     *     input_type_snapshot: string,
     *     scope_snapshot: string,
     *     value: string
     * }>
     */
    public function rowsForStorage(array $resolved, array $input, int $passengerCount): array
    {
        $passengerInput = is_array($input['passenger_fields'] ?? null) ? $input['passenger_fields'] : [];
        $bookingInput = is_array($input['booking_fields'] ?? null) ? $input['booking_fields'] : [];
        $rows = [];

        foreach ($resolved['passenger'] as $field) {
            $values = $passengerInput[$field->id] ?? $passengerInput[(string) $field->id] ?? [];
            if (! is_array($values)) {
                $values = [];
            }
            $values = array_values($values);

            for ($index = 0; $index < $passengerCount; $index++) {
                $normalized = $this->normalizeValue($field, $values[$index] ?? null);
                if ($normalized === null) {
                    if ($field->required) {
                        throw ValidationException::withMessages([
                            'passenger_fields.'.$field->id.'.'.$index => (string) __('validation.required', [
                                'attribute' => $field->label,
                            ]),
                        ]);
                    }

                    continue;
                }

                $rows[] = $this->snapshotRow($field, $index, $normalized);
            }
        }

        foreach ($resolved['booking'] as $field) {
            $value = $bookingInput[$field->id] ?? $bookingInput[(string) $field->id] ?? null;
            $normalized = $this->normalizeValue($field, $value);
            if ($normalized === null) {
                if ($field->required) {
                    throw ValidationException::withMessages([
                        'booking_fields.'.$field->id => (string) __('validation.required', [
                            'attribute' => $field->label,
                        ]),
                    ]);
                }

                continue;
            }

            $rows[] = $this->snapshotRow($field, null, $normalized);
        }

        return $rows;
    }

    /**
     * @return list<mixed>
     */
    private function valueRules(ResolvedBookingField $field): array
    {
        $presence = $field->required ? 'required' : 'nullable';

        return match ($field->inputType) {
            ProviderBookingField::TYPE_TEXTAREA => [$presence, 'string', 'max:2000'],
            ProviderBookingField::TYPE_NUMBER => [$presence, 'regex:/^-?\d+(\.\d+)?$/'],
            ProviderBookingField::TYPE_DATE => [$presence, 'date'],
            ProviderBookingField::TYPE_TIME => [$presence, 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/'],
            ProviderBookingField::TYPE_PHONE => [$presence, 'string', 'max:40'],
            ProviderBookingField::TYPE_COUNTRY => [$presence, 'integer', Rule::exists(\App\Models\LmpCountry::class, 'id')],
            default => [$presence, 'string', 'max:255'],
        };
    }

    private function normalizeValue(ResolvedBookingField $field, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        if ($field->inputType === ProviderBookingField::TYPE_TIME && preg_match('/^\d{2}:\d{2}:\d{2}$/', $text) === 1) {
            return substr($text, 0, 5);
        }

        if ($field->inputType === ProviderBookingField::TYPE_COUNTRY) {
            return (string) (int) $text;
        }

        return $text;
    }

    /**
     * @return array{
     *     provider_booking_field_id: int,
     *     passenger_index: int|null,
     *     label_snapshot: string,
     *     input_type_snapshot: string,
     *     scope_snapshot: string,
     *     value: string
     * }
     */
    private function snapshotRow(ResolvedBookingField $field, ?int $passengerIndex, string $value): array
    {
        return [
            'provider_booking_field_id' => $field->id,
            'passenger_index' => $passengerIndex,
            'label_snapshot' => $field->label,
            'input_type_snapshot' => $field->inputType,
            'scope_snapshot' => $field->scope,
            'value' => $value,
        ];
    }
}
