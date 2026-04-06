<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ParameterValue extends Model
{
    use AuditTrait;

    protected $table = 'parameter_values';

    /** @var list<string> */
    protected $fillable = [
        'parameter_definition_id',
        'account_id',
        'value',
    ];

    /**
     * Ensure at most one row per (definition, account): account null = system-wide value.
     *
     * @throws ValidationException
     */
    public static function assertUniqueCombination(int $parameterDefinitionId, ?int $accountId, ?int $ignoreId = null): void
    {
        $query = static::query()->where('parameter_definition_id', $parameterDefinitionId);

        if ($accountId === null) {
            $query->whereNull('account_id');
        } else {
            $query->where('account_id', $accountId);
        }

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            $def = ParameterDefinition::find($parameterDefinitionId);
            $field = $def && $def->scope === 'tenant' ? 'account_id' : 'parameter_definition_id';

            throw ValidationException::withMessages([
                $field => __('filament.resources.parameter_value_duplicate'),
            ]);
        }
    }

    public function parameterDefinition(): BelongsTo
    {
        return $this->belongsTo(ParameterDefinition::class, 'parameter_definition_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
