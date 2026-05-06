<?php

namespace App\Http\Controllers;

use App\Models\ParameterDefinition;
use App\Models\ParameterValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account !== null, 404);

        $definitions = ParameterDefinition::query()
            ->where('scope', 'tenant')
            ->with([
                'translations.language.locale',
                'parameterOptions.translations.language.locale',
            ])
            ->orderBy('category')
            ->orderBy('subcategory')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $valuesByDefinitionId = ParameterValue::query()
            ->where('account_id', $account->id)
            ->whereIn('parameter_definition_id', $definitions->pluck('id')->all())
            ->get()
            ->keyBy('parameter_definition_id');

        $definitionsByCategory = $definitions->groupBy(
            fn (ParameterDefinition $definition): string => (string) ($definition->category ?: 'general')
        );

        return view('account.settings', [
            'account' => $account,
            'definitionsByCategory' => $definitionsByCategory,
            'valuesByDefinitionId' => $valuesByDefinitionId,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account !== null, 404);

        $definitions = ParameterDefinition::query()
            ->where('scope', 'tenant')
            ->with([
                'parameterOptions.translations.language.locale',
            ])
            ->orderBy('id')
            ->get();

        $existingByDefinitionId = ParameterValue::query()
            ->where('account_id', $account->id)
            ->whereIn('parameter_definition_id', $definitions->pluck('id')->all())
            ->get()
            ->keyBy('parameter_definition_id');

        foreach ($definitions as $definition) {
            $rawInput = $request->input('values.'.$definition->id);
            $normalized = $this->normalizeValue($definition, $rawInput);
            $existing = $existingByDefinitionId->get($definition->id);

            if ($normalized === null) {
                if ($existing instanceof ParameterValue) {
                    $existing->delete();
                }

                continue;
            }

            if ($existing instanceof ParameterValue) {
                $existing->update(['value' => $normalized]);

                continue;
            }

            ParameterValue::assertUniqueCombination((int) $definition->id, (int) $account->id);
            ParameterValue::query()->create([
                'parameter_definition_id' => $definition->id,
                'account_id' => $account->id,
                'value' => $normalized,
            ]);
        }

        return redirect()
            ->route('account.settings')
            ->with('status', 'La configuración se actualizó correctamente.');
    }

    private function normalizeValue(ParameterDefinition $definition, mixed $rawInput): ?string
    {
        $usesOptions = ParameterDefinition::uiComponentRequiresOptions($definition->ui_component)
            && $definition->parameterOptions->count() >= 2;
        $label = $definition->name !== '' ? $definition->name : $definition->code;

        if ($usesOptions) {
            if ($rawInput === null || $rawInput === '') {
                return null;
            }

            $value = (string) $rawInput;
            $allowedValues = $definition->parameterOptions
                ->map(fn ($option): string => (string) $option->value)
                ->all();
            if (! in_array($value, $allowedValues, true)) {
                throw ValidationException::withMessages([
                    'values.'.$definition->id => "Valor inválido para {$label}.",
                ]);
            }

            return $value;
        }

        if (in_array($definition->ui_component, ['checkbox', 'switch'], true) || $definition->type === 'boolean') {
            if (in_array($rawInput, [1, '1', true, 'true', 'on'], true)) {
                return '1';
            }

            return '0';
        }

        $value = is_string($rawInput) ? trim($rawInput) : (string) $rawInput;
        if ($value === '') {
            return null;
        }

        if ($definition->type === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw ValidationException::withMessages([
                'values.'.$definition->id => "El valor de {$label} debe ser un número entero.",
            ]);
        }

        if ($definition->type === 'decimal' && ! is_numeric($value)) {
            throw ValidationException::withMessages([
                'values.'.$definition->id => "El valor de {$label} debe ser numérico.",
            ]);
        }

        if ($definition->type === 'date' && ! $this->isValidDate($value, 'Y-m-d')) {
            throw ValidationException::withMessages([
                'values.'.$definition->id => "El valor de {$label} debe ser una fecha válida.",
            ]);
        }

        if ($definition->type === 'time' && ! $this->isValidDate($value, 'H:i')) {
            throw ValidationException::withMessages([
                'values.'.$definition->id => "El valor de {$label} debe ser una hora válida.",
            ]);
        }

        if ($definition->type === 'datetime' && strtotime($value) === false) {
            throw ValidationException::withMessages([
                'values.'.$definition->id => "El valor de {$label} debe ser una fecha y hora válida.",
            ]);
        }

        return $value;
    }

    private function isValidDate(string $value, string $format): bool
    {
        $parsed = \DateTime::createFromFormat($format, $value);

        return $parsed !== false && $parsed->format($format) === $value;
    }
}

