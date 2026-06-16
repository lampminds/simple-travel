<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Service;
use App\Models\ServiceAvailabilityOverride;
use App\Models\ServiceAvailabilityRule;
use App\Support\AccountBusinessTypeGate;
use App\Support\WeekdayMask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountProviderServiceAvailabilityController extends Controller
{
    public function index(Request $request): View
    {
        $account = $this->resolveProviderAccount($request);

        $services = Service::query()
            ->where('account_id', $account->id)
            ->with([
                'translations.language.locale',
                'serviceVariants.translations.language.locale',
            ])
            ->withCount(['availabilityRules', 'availabilityOverrides'])
            ->orderByDesc('id')
            ->get();

        foreach ($services as $service) {
            $service->serviceVariants->loadCount(['availabilityRules', 'availabilityOverrides']);
        }

        return view('account.availability.provider.index', [
            'account' => $account,
            'services' => $services,
        ]);
    }

    public function show(Request $request, Service $service): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $service->load([
            'translations.language.locale',
            'availabilityRules',
            'availabilityOverrides',
            'serviceVariants' => fn ($query) => $query
                ->orderByRaw('COALESCE(sort_order, 9999)')
                ->orderBy('id'),
        ]);

        return view('account.availability.provider.service.show', [
            'account' => $account,
            'service' => $service,
        ]);
    }

    public function createRule(Request $request, Service $service): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        return view('account.availability.provider.service.rule-form', [
            'account' => $account,
            'service' => $this->serviceFormContext($service),
            'rule' => null,
            'selectedWeekdayBits' => array_keys(WeekdayMask::DAY_BITS),
            'submitRoute' => route('account.availability.service-rules.store', $service),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.availability.services.show', $service),
        ]);
    }

    public function storeRule(Request $request, Service $service): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $payload = $this->validateRulePayload($request);

        $service->availabilityRules()->create($payload);

        return redirect()
            ->route('account.availability.services.show', $service)
            ->with('status', __('account.availability.service_status_rule_created'));
    }

    public function editRule(Request $request, ServiceAvailabilityRule $rule): View
    {
        $account = $this->resolveProviderAccount($request);
        $service = $rule->service;
        abort_unless($service instanceof Service, 404);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        return view('account.availability.provider.service.rule-form', [
            'account' => $account,
            'service' => $this->serviceFormContext($service),
            'rule' => $rule,
            'selectedWeekdayBits' => old('weekday_bits', WeekdayMask::toSelectedBits($rule->weekday_mask)),
            'submitRoute' => route('account.availability.service-rules.update', $rule),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.availability.services.show', $service),
        ]);
    }

    public function updateRule(Request $request, ServiceAvailabilityRule $rule): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $service = $rule->service;
        abort_unless($service instanceof Service, 404);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $payload = $this->validateRulePayload($request);
        $rule->update($payload);

        return redirect()
            ->route('account.availability.services.show', $service)
            ->with('status', __('account.availability.service_status_rule_updated'));
    }

    public function destroyRule(Request $request, ServiceAvailabilityRule $rule): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $service = $rule->service;
        abort_unless($service instanceof Service, 404);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $rule->delete();

        return redirect()
            ->route('account.availability.services.show', $service)
            ->with('status', __('account.availability.service_status_rule_deleted'));
    }

    public function createOverride(Request $request, Service $service): View
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        return view('account.availability.provider.service.override-form', [
            'account' => $account,
            'service' => $this->serviceFormContext($service),
            'override' => null,
            'submitRoute' => route('account.availability.service-overrides.store', $service),
            'submitMethod' => 'POST',
            'cancelRoute' => route('account.availability.services.show', $service),
        ]);
    }

    public function storeOverride(Request $request, Service $service): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $payload = $this->validateOverridePayload($request, $service);

        $service->availabilityOverrides()->create($payload);

        return redirect()
            ->route('account.availability.services.show', $service)
            ->with('status', __('account.availability.service_status_override_created'));
    }

    public function editOverride(Request $request, ServiceAvailabilityOverride $override): View
    {
        $account = $this->resolveProviderAccount($request);
        $service = $override->service;
        abort_unless($service instanceof Service, 404);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        return view('account.availability.provider.service.override-form', [
            'account' => $account,
            'service' => $this->serviceFormContext($service),
            'override' => $override,
            'submitRoute' => route('account.availability.service-overrides.update', $override),
            'submitMethod' => 'PUT',
            'cancelRoute' => route('account.availability.services.show', $service),
        ]);
    }

    public function updateOverride(Request $request, ServiceAvailabilityOverride $override): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $service = $override->service;
        abort_unless($service instanceof Service, 404);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $payload = $this->validateOverridePayload($request, $service, (int) $override->id);

        $override->update($payload);

        return redirect()
            ->route('account.availability.services.show', $service)
            ->with('status', __('account.availability.service_status_override_updated'));
    }

    public function destroyOverride(Request $request, ServiceAvailabilityOverride $override): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        $service = $override->service;
        abort_unless($service instanceof Service, 404);
        $this->assertServiceBelongsToProvider($service, (int) $account->id);

        $override->delete();

        return redirect()
            ->route('account.availability.services.show', $service)
            ->with('status', __('account.availability.service_status_override_deleted'));
    }

    /**
     * @return array{
     *     start_date: string|null,
     *     end_date: string|null,
     *     weekday_mask: int|null,
     *     active: bool
     * }
     */
    private function validateRulePayload(Request $request): array
    {
        normalize_request_locale_dates($request, ['start_date', 'end_date']);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'weekday_bits' => ['nullable', 'array'],
            'weekday_bits.*' => ['integer', Rule::in(array_keys(WeekdayMask::DAY_BITS))],
            'active' => ['nullable', 'boolean'],
        ]);

        $weekdayBits = is_array($validated['weekday_bits'] ?? null) ? $validated['weekday_bits'] : [];

        return [
            'start_date' => isset($validated['start_date']) ? (string) $validated['start_date'] : null,
            'end_date' => isset($validated['end_date']) ? (string) $validated['end_date'] : null,
            'weekday_mask' => WeekdayMask::fromSelectedBits($weekdayBits),
            'active' => (bool) ($validated['active'] ?? false),
        ];
    }

    /**
     * @return array{
     *     date: string,
     *     end_date: string|null,
     *     closed: bool,
     *     reason: string|null
     * }
     */
    private function validateOverridePayload(
        Request $request,
        Service $service,
        ?int $excludeOverrideId = null,
    ): array {
        normalize_request_locale_dates($request, ['date', 'end_date']);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:date'],
            'closed' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $closed = (bool) ($validated['closed'] ?? true);
        if (! $closed) {
            throw ValidationException::withMessages([
                'closed' => __('account.availability.validation.service_override_closure_only'),
            ]);
        }

        $startDate = (string) $validated['date'];
        $endDate = isset($validated['end_date']) && $validated['end_date'] !== ''
            ? (string) $validated['end_date']
            : null;

        if ($this->overrideRangeOverlaps($service, $startDate, $endDate ?? $startDate, $excludeOverrideId)) {
            throw ValidationException::withMessages([
                'date' => __('account.availability.validation.service_override_overlap'),
            ]);
        }

        return [
            'date' => $startDate,
            'end_date' => $endDate,
            'closed' => true,
            'reason' => isset($validated['reason']) ? trim((string) $validated['reason']) : null,
        ];
    }

    private function overrideRangeOverlaps(
        Service $service,
        string $startDate,
        string $endDate,
        ?int $excludeOverrideId = null,
    ): bool {
        return ServiceAvailabilityOverride::query()
            ->where('service_id', $service->id)
            ->when($excludeOverrideId !== null, fn ($query) => $query->where('id', '!=', $excludeOverrideId))
            ->where(function ($query) use ($startDate, $endDate): void {
                $query->whereRaw(
                    'date <= ? AND COALESCE(end_date, date) >= ?',
                    [$endDate, $startDate],
                );
            })
            ->exists();
    }

    private function serviceFormContext(Service $service): Service
    {
        return $service->loadMissing([
            'translations.language.locale',
        ]);
    }

    private function resolveProviderAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertProviderAccount($request);
    }

    private function assertServiceBelongsToProvider(Service $service, int $providerId): void
    {
        abort_unless((int) $service->account_id === $providerId, 404);
    }
}
