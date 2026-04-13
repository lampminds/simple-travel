<?php

namespace App\Http\Controllers;

use App\Models\ParameterDefinition;
use App\Models\ParameterValue;

/**
 * Resolves stored parameter values by `code` on {@see ParameterDefinition} and rows in {@see ParameterValue}.
 *
 * **Scope `system`:** Values exist only with `account_id` null. The `$accountId` argument is ignored.
 *
 * **Scope `tenant`:** Rows may use `account_id` null (default for every account) or a specific account
 * (override). Resolution: specific account row if `$accountId` is given, else row with null `account_id`,
 * else the definition's `default_value`.
 */
class ParameterReaderController extends Controller
{
    public const CODE_INVITATION_EXPIRATION_DAYS = 'invitation_expiration_days';

    /** @var array<string, ?string> */
    private array $rawCache = [];

    /**
     * Raw stored value for a parameter code, or null if unknown / empty.
     */
    public function getRawValue(string $code, ?int $accountId = null): ?string
    {
        $cacheKey = $code.'|'.($accountId ?? '');

        if (array_key_exists($cacheKey, $this->rawCache)) {
            return $this->rawCache[$cacheKey];
        }

        $definition = ParameterDefinition::query()->where('code', $code)->first();

        if (! $definition) {
            return $this->rawCache[$cacheKey] = null;
        }

        $raw = $this->resolveRawForDefinition($definition, $accountId);
        if ($raw !== null) {
            $raw = trim((string) $raw);
        }

        return $this->rawCache[$cacheKey] = ($raw === '' || $raw === null) ? null : $raw;
    }

    public function getInt(string $code, ?int $accountId = null, int $default = 0): int
    {
        $raw = $this->getRawValue($code, $accountId);
        if ($raw === null) {
            return $default;
        }

        $value = filter_var($raw, FILTER_VALIDATE_INT);

        return $value === false ? $default : $value;
    }

    /**
     * Invitation link lifetime in days (respects definition scope and tenant overrides).
     */
    public function invitationExpirationDays(?int $accountId = null): int
    {
        $days = $this->getInt(self::CODE_INVITATION_EXPIRATION_DAYS, $accountId, 7);

        return max(1, min(365, $days));
    }

    private function resolveRawForDefinition(ParameterDefinition $definition, ?int $accountId): ?string
    {
        if ($definition->scope === 'system') {
            // Only system-level rows (account_id must be null by design).
            $row = ParameterValue::query()
                ->where('parameter_definition_id', $definition->id)
                ->whereNull('account_id')
                ->first();

            $value = $row?->value;

            return ($value === null || $value === '') ? $definition->default_value : $value;
        }

        // Tenant: prefer account-specific row, then global default row (null account_id).
        $value = null;
        if ($accountId !== null) {
            $value = ParameterValue::query()
                ->where('parameter_definition_id', $definition->id)
                ->where('account_id', $accountId)
                ->value('value');
        }

        if ($value === null || $value === '') {
            $value = ParameterValue::query()
                ->where('parameter_definition_id', $definition->id)
                ->whereNull('account_id')
                ->value('value');
        }

        if ($value === null || $value === '') {
            $value = $definition->default_value;
        }

        return $value;
    }
}
