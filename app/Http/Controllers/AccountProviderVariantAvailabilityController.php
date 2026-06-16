<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ServiceVariant;
use App\Models\ServiceVariantAvailabilityOverride;
use App\Models\ServiceVariantAvailabilityRule;
use App\Models\ServiceVariantAvailabilityTimeSlot;
use App\Support\AccountBusinessTypeGate;
use App\Support\WeekdayMask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountProviderVariantAvailabilityController extends Controller
{
    public function show(Request $request, ServiceVariant $variant): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $variant->load([
            'service.translations.language.locale',
            'translations.language.locale',
            'availabilityRules.timeSlots',
            'availabilityOverrides',
        ]);

        return view('account.availability.provider.show', [
            'account' => $account,
            'variant' => $variant,
        ]);
    }

    public function createRule(Request $request, ServiceVariant $variant): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        return view('account.availability.provider.rule-form', [
            'account' => $account,
            'variant' => $this->variantFormContext($variant),
            'rule' => null,
            'selectedWeekdayBits' => array_keys(WeekdayMask::DAY_BITS),
            'timeSlots' => old('time_slots', []),
            'submitRoute' => route('account.availability.rules.store', $variant),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.availability.variants.show', $variant),
        ]);
    }

    public function storeRule(Request $request, ServiceVariant $variant): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $payload = $this->validateRulePayload($request, $variant);

        DB::transaction(function () use ($variant, $payload): void {
            $rule = $variant->availabilityRules()->create([
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'weekday_mask' => $payload['weekday_mask'],
                'active' => $payload['active'],
            ]);

            if ($variant->usesTimeSlotInventory()) {
                $this->syncTimeSlots($rule, $payload['time_slots']);
            }
        });

        return redirect()
            ->route('account.availability.variants.show', $variant)
            ->with('status', __('account.availability.status_rule_created'));
    }

    public function editRule(Request $request, ServiceVariantAvailabilityRule $rule): View
    {
        $account = $this->resolveProviderAccount($request);
        $variant = $rule->serviceVariant;
        abort_unless($variant instanceof ServiceVariant, 404);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $rule->load('timeSlots');

        $timeSlots = old('time_slots');
        if (! is_array($timeSlots)) {
            $timeSlots = $rule->timeSlots->map(fn (ServiceVariantAvailabilityTimeSlot $slot): array => [
                'start_time' => $this->formatTimeInput($slot->start_time),
                'end_time' => $this->formatTimeInput($slot->end_time),
                'capacity' => $slot->capacity !== null ? (string) $slot->capacity : '',
                'cutoff_minutes' => $slot->cutoff_minutes !== null ? (string) $slot->cutoff_minutes : '',
                'active' => $slot->active,
            ])->all();
        }

        return view('account.availability.provider.rule-form', [
            'account' => $account,
            'variant' => $this->variantFormContext($variant),
            'rule' => $rule,
            'selectedWeekdayBits' => old('weekday_bits', WeekdayMask::toSelectedBits($rule->weekday_mask)),
            'timeSlots' => $timeSlots,
            'submitRoute' => route('account.availability.rules.update', $rule),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.availability.variants.show', $variant),
        ]);
    }

    public function updateRule(Request $request, ServiceVariantAvailabilityRule $rule): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $variant = $rule->serviceVariant;
        abort_unless($variant instanceof ServiceVariant, 404);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $payload = $this->validateRulePayload($request, $variant);

        DB::transaction(function () use ($rule, $variant, $payload): void {
            $rule->update([
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'weekday_mask' => $payload['weekday_mask'],
                'active' => $payload['active'],
            ]);

            if ($variant->usesTimeSlotInventory()) {
                $this->syncTimeSlots($rule, $payload['time_slots']);
            } else {
                $rule->timeSlots()->delete();
            }
        });

        return redirect()
            ->route('account.availability.variants.show', $variant)
            ->with('status', __('account.availability.status_rule_updated'));
    }

    public function destroyRule(Request $request, ServiceVariantAvailabilityRule $rule): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $variant = $rule->serviceVariant;
        abort_unless($variant instanceof ServiceVariant, 404);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $rule->delete();

        return redirect()
            ->route('account.availability.variants.show', $variant)
            ->with('status', __('account.availability.status_rule_deleted'));
    }

    public function createOverride(Request $request, ServiceVariant $variant): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        return view('account.availability.provider.override-form', [
            'account' => $account,
            'variant' => $this->variantFormContext($variant),
            'override' => null,
            'submitRoute' => route('account.availability.overrides.store', $variant),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.availability.variants.show', $variant),
        ]);
    }

    public function storeOverride(Request $request, ServiceVariant $variant): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $payload = $this->validateOverridePayload($request, $variant);

        $variant->availabilityOverrides()->create($payload);

        return redirect()
            ->route('account.availability.variants.show', $variant)
            ->with('status', __('account.availability.status_override_created'));
    }

    public function editOverride(Request $request, ServiceVariantAvailabilityOverride $override): View
    {
        $account = $this->resolveProviderAccount($request);
        $variant = $override->serviceVariant;
        abort_unless($variant instanceof ServiceVariant, 404);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        return view('account.availability.provider.override-form', [
            'account' => $account,
            'variant' => $this->variantFormContext($variant),
            'override' => $override,
            'submitRoute' => route('account.availability.overrides.update', $override),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.availability.variants.show', $variant),
        ]);
    }

    public function updateOverride(Request $request, ServiceVariantAvailabilityOverride $override): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $variant = $override->serviceVariant;
        abort_unless($variant instanceof ServiceVariant, 404);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $payload = $this->validateOverridePayload($request, $variant, (int) $override->id);

        $override->update($payload);

        return redirect()
            ->route('account.availability.variants.show', $variant)
            ->with('status', __('account.availability.status_override_updated'));
    }

    public function destroyOverride(Request $request, ServiceVariantAvailabilityOverride $override): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $variant = $override->serviceVariant;
        abort_unless($variant instanceof ServiceVariant, 404);
        $this->assertVariantBelongsToProvider($variant, (int) $account->id);

        $override->delete();

        return redirect()
            ->route('account.availability.variants.show', $variant)
            ->with('status', __('account.availability.status_override_deleted'));
    }

    /**
     * @return array{
     *     start_date: string|null,
     *     end_date: string|null,
     *     weekday_mask: int|null,
     *     active: bool,
     *     time_slots: list<array{start_time: string|null, end_time: string|null, capacity: int|null, cutoff_minutes: int|null, active: bool}>
     * }
     */
    private function validateRulePayload(Request $request, ServiceVariant $variant): array
    {
        normalize_request_locale_dates($request, ['start_date', 'end_date']);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'weekday_bits' => ['nullable', 'array'],
            'weekday_bits.*' => ['integer', Rule::in(array_keys(WeekdayMask::DAY_BITS))],
            'active' => ['nullable', 'boolean'],
            'time_slots' => ['nullable', 'array'],
            'time_slots.*.start_time' => ['nullable', 'date_format:H:i'],
            'time_slots.*.end_time' => ['nullable', 'date_format:H:i'],
            'time_slots.*.capacity' => ['nullable', 'integer', 'min:0'],
            'time_slots.*.cutoff_minutes' => ['nullable', 'integer', 'min:0'],
            'time_slots.*.active' => ['nullable', 'boolean'],
        ]);

        $weekdayBits = is_array($validated['weekday_bits'] ?? null) ? $validated['weekday_bits'] : [];
        $weekdayMask = WeekdayMask::fromSelectedBits($weekdayBits);

        $timeSlots = [];
        if ($variant->usesTimeSlotInventory()) {
            $rawSlots = is_array($validated['time_slots'] ?? null) ? $validated['time_slots'] : [];
            foreach ($rawSlots as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }
                $start = trim((string) ($row['start_time'] ?? ''));
                $end = trim((string) ($row['end_time'] ?? ''));
                if ($start === '' && $end === '') {
                    continue;
                }
                if ($start === '' || $end === '') {
                    throw ValidationException::withMessages([
                        "time_slots.{$index}.start_time" => __('account.availability.validation.slot_times_required'),
                    ]);
                }

                $timeSlots[] = [
                    'start_time' => $start,
                    'end_time' => $end,
                    'capacity' => isset($row['capacity']) && $row['capacity'] !== ''
                        ? (int) $row['capacity']
                        : null,
                    'cutoff_minutes' => isset($row['cutoff_minutes']) && $row['cutoff_minutes'] !== ''
                        ? (int) $row['cutoff_minutes']
                        : null,
                    'active' => filter_var($row['active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                ];
            }

            if ($timeSlots === []) {
                throw ValidationException::withMessages([
                    'time_slots' => __('account.availability.validation.slots_required'),
                ]);
            }
        }

        return [
            'start_date' => isset($validated['start_date']) ? (string) $validated['start_date'] : null,
            'end_date' => isset($validated['end_date']) ? (string) $validated['end_date'] : null,
            'weekday_mask' => $weekdayMask,
            'active' => (bool) ($validated['active'] ?? false),
            'time_slots' => $timeSlots,
        ];
    }

    /**
     * @return array{
     *     date: string,
     *     start_time: string|null,
     *     capacity: int|null,
     *     closed: bool,
     *     reason: string|null
     * }
     */
    private function validateOverridePayload(
        Request $request,
        ServiceVariant $variant,
        ?int $excludeOverrideId = null,
    ): array {
        normalize_request_locale_dates($request, ['date']);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'closed' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $closed = (bool) ($validated['closed'] ?? false);
        $startTime = trim((string) ($validated['start_time'] ?? ''));
        $startTime = $startTime !== '' ? $startTime.':00' : null;

        if ($variant->usesTimeSlotInventory() && $startTime === null && ! $closed) {
            throw ValidationException::withMessages([
                'start_time' => __('account.availability.validation.override_start_time_required'),
            ]);
        }

        if (! $variant->usesTimeSlotInventory()) {
            $startTime = null;
        }

        $date = (string) $validated['date'];

        $duplicate = ServiceVariantAvailabilityOverride::query()
            ->where('service_variant_id', $variant->id)
            ->whereDate('date', $date)
            ->when($startTime === null, fn ($q) => $q->whereNull('start_time'))
            ->when($startTime !== null, fn ($q) => $q->where('start_time', $startTime))
            ->when($excludeOverrideId !== null, fn ($q) => $q->where('id', '!=', $excludeOverrideId))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'date' => __('account.availability.validation.override_duplicate'),
            ]);
        }

        return [
            'date' => $date,
            'start_time' => $startTime,
            'capacity' => $closed ? null : (
                isset($validated['capacity']) && $validated['capacity'] !== ''
                    ? (int) $validated['capacity']
                    : null
            ),
            'closed' => $closed,
            'reason' => isset($validated['reason']) ? trim((string) $validated['reason']) : null,
        ];
    }

    /**
     * @param  list<array{start_time: string|null, end_time: string|null, capacity: int|null, cutoff_minutes: int|null, active: bool}>  $rows
     */
    private function syncTimeSlots(ServiceVariantAvailabilityRule $rule, array $rows): void
    {
        $rule->timeSlots()->delete();

        $sort = 1;
        foreach ($rows as $row) {
            $rule->timeSlots()->create([
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'capacity' => $row['capacity'],
                'cutoff_minutes' => $row['cutoff_minutes'],
                'active' => $row['active'],
                'sort_order' => $sort,
            ]);
            $sort++;
        }
    }

    private function variantFormContext(ServiceVariant $variant): ServiceVariant
    {
        return $variant->loadMissing([
            'service.translations.language.locale',
            'translations.language.locale',
        ]);
    }

    private function formatTimeInput(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i');
        }

        $string = trim((string) $value);
        if (strlen($string) >= 5) {
            return substr($string, 0, 5);
        }

        return $string;
    }

    private function resolveProviderAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertProviderAccount($request);
    }

    private function assertVariantBelongsToProvider(ServiceVariant $variant, int $providerId): void
    {
        abort_unless(
            ServiceVariant::query()
                ->whereKey($variant->id)
                ->whereHas('service', fn ($q) => $q->where('account_id', $providerId))
                ->exists(),
            404
        );
    }
}
