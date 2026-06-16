<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\OperatorPackageAvailabilityOverride;
use App\Models\OperatorPackageAvailabilityRule;
use App\Models\OperatorPackageAvailabilityTimeSlot;
use App\Models\OperatorServiceCatalog;
use App\Support\AccountBusinessTypeGate;
use App\Support\WeekdayMask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountOperatorPackageAvailabilityController extends Controller
{
    public function index(Request $request): View
    {
        $account = $this->resolveOperatorAccount($request);

        $catalogs = OperatorServiceCatalog::query()
            ->where('operator_id', $account->id)
            ->with(['translations.language.locale'])
            ->withCount(['availabilityRules', 'availabilityOverrides'])
            ->orderByRaw('COALESCE(id, 9999)')
            ->orderBy('id')
            ->get();

        return view('account.package-availability.operator.index', [
            'account' => $account,
            'catalogs' => $catalogs,
        ]);
    }

    public function show(Request $request, OperatorServiceCatalog $catalog): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $catalog->load([
            'translations.language.locale',
            'availabilityRules.timeSlots',
            'availabilityOverrides',
        ]);

        return view('account.package-availability.operator.show', [
            'account' => $account,
            'catalog' => $catalog,
        ]);
    }

    public function updateInventory(Request $request, OperatorServiceCatalog $catalog): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $validated = $request->validate([
            'inventory_type' => ['required', Rule::in(['unlimited', 'per_day', 'per_timeslot', 'per_departure'])],
            'inventory_total' => ['nullable', 'integer', 'min:0'],
        ]);

        $inventoryType = (string) $validated['inventory_type'];
        $inventoryTotal = $inventoryType === 'unlimited'
            ? null
            : (isset($validated['inventory_total']) ? (int) $validated['inventory_total'] : null);

        $catalog->update([
            'inventory_type' => $inventoryType,
            'inventory_total' => $inventoryTotal,
        ]);

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_inventory_updated'));
    }

    public function createRule(Request $request, OperatorServiceCatalog $catalog): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        return view('account.package-availability.operator.rule-form', [
            'account' => $account,
            'catalog' => $this->catalogFormContext($catalog),
            'rule' => null,
            'selectedWeekdayBits' => array_keys(WeekdayMask::DAY_BITS),
            'timeSlots' => old('time_slots', []),
            'submitRoute' => route('account.package-availability.rules.store', $catalog),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.package-availability.catalogs.show', $catalog),
        ]);
    }

    public function storeRule(Request $request, OperatorServiceCatalog $catalog): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $payload = $this->validateRulePayload($request, $catalog);

        DB::transaction(function () use ($catalog, $payload): void {
            $rule = $catalog->availabilityRules()->create([
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'weekday_mask' => $payload['weekday_mask'],
                'active' => $payload['active'],
            ]);

            if ($catalog->usesTimeSlotInventory()) {
                $this->syncTimeSlots($rule, $payload['time_slots']);
            }
        });

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_rule_created'));
    }

    public function editRule(Request $request, OperatorPackageAvailabilityRule $rule): View
    {
        $account = $this->resolveOperatorAccount($request);
        $catalog = $rule->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $rule->load('timeSlots');

        $timeSlots = old('time_slots');
        if (! is_array($timeSlots)) {
            $timeSlots = $rule->timeSlots->map(fn (OperatorPackageAvailabilityTimeSlot $slot): array => [
                'start_time' => $this->formatTimeInput($slot->start_time),
                'end_time' => $this->formatTimeInput($slot->end_time),
                'capacity' => $slot->capacity !== null ? (string) $slot->capacity : '',
                'cutoff_minutes' => $slot->cutoff_minutes !== null ? (string) $slot->cutoff_minutes : '',
                'active' => $slot->active,
            ])->all();
        }

        return view('account.package-availability.operator.rule-form', [
            'account' => $account,
            'catalog' => $this->catalogFormContext($catalog),
            'rule' => $rule,
            'selectedWeekdayBits' => old('weekday_bits', WeekdayMask::toSelectedBits($rule->weekday_mask)),
            'timeSlots' => $timeSlots,
            'submitRoute' => route('account.package-availability.rules.update', $rule),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.package-availability.catalogs.show', $catalog),
        ]);
    }

    public function updateRule(Request $request, OperatorPackageAvailabilityRule $rule): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $catalog = $rule->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $payload = $this->validateRulePayload($request, $catalog);

        DB::transaction(function () use ($rule, $catalog, $payload): void {
            $rule->update([
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'weekday_mask' => $payload['weekday_mask'],
                'active' => $payload['active'],
            ]);

            if ($catalog->usesTimeSlotInventory()) {
                $this->syncTimeSlots($rule, $payload['time_slots']);
            } else {
                $rule->timeSlots()->delete();
            }
        });

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_rule_updated'));
    }

    public function destroyRule(Request $request, OperatorPackageAvailabilityRule $rule): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $catalog = $rule->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $rule->delete();

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_rule_deleted'));
    }

    public function createOverride(Request $request, OperatorServiceCatalog $catalog): View
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        return view('account.package-availability.operator.override-form', [
            'account' => $account,
            'catalog' => $this->catalogFormContext($catalog),
            'override' => null,
            'submitRoute' => route('account.package-availability.overrides.store', $catalog),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.package-availability.catalogs.show', $catalog),
        ]);
    }

    public function storeOverride(Request $request, OperatorServiceCatalog $catalog): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $payload = $this->validateOverridePayload($request, $catalog);

        $catalog->availabilityOverrides()->create($payload);

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_override_created'));
    }

    public function editOverride(Request $request, OperatorPackageAvailabilityOverride $override): View
    {
        $account = $this->resolveOperatorAccount($request);
        $catalog = $override->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        return view('account.package-availability.operator.override-form', [
            'account' => $account,
            'catalog' => $this->catalogFormContext($catalog),
            'override' => $override,
            'submitRoute' => route('account.package-availability.overrides.update', $override),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.package-availability.catalogs.show', $catalog),
        ]);
    }

    public function updateOverride(Request $request, OperatorPackageAvailabilityOverride $override): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $catalog = $override->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $payload = $this->validateOverridePayload($request, $catalog, (int) $override->id);

        $override->update($payload);

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_override_updated'));
    }

    public function destroyOverride(Request $request, OperatorPackageAvailabilityOverride $override): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $catalog = $override->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);
        $this->assertCatalogBelongsToOperator($catalog, (int) $account->id);

        $override->delete();

        return redirect()
            ->route('account.package-availability.catalogs.show', $catalog)
            ->with('status', __('account.package_availability.status_override_deleted'));
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
    private function validateRulePayload(Request $request, OperatorServiceCatalog $catalog): array
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
        if ($catalog->usesTimeSlotInventory()) {
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
        OperatorServiceCatalog $catalog,
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

        if ($catalog->usesTimeSlotInventory() && $startTime === null && ! $closed) {
            throw ValidationException::withMessages([
                'start_time' => __('account.availability.validation.override_start_time_required'),
            ]);
        }

        if (! $catalog->usesTimeSlotInventory()) {
            $startTime = null;
        }

        $date = (string) $validated['date'];

        $duplicate = OperatorPackageAvailabilityOverride::query()
            ->where('operator_service_catalog_id', $catalog->id)
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
    private function syncTimeSlots(OperatorPackageAvailabilityRule $rule, array $rows): void
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

    private function catalogFormContext(OperatorServiceCatalog $catalog): OperatorServiceCatalog
    {
        return $catalog->loadMissing([
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

    private function resolveOperatorAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertOperatorAccount($request);
    }

    private function assertCatalogBelongsToOperator(OperatorServiceCatalog $catalog, int $operatorId): void
    {
        abort_unless((int) $catalog->operator_id === $operatorId, 404);
    }
}
