<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\ProviderBookingField;
use App\Models\ProviderBookingFieldTranslation;
use App\Models\ProviderBookingTemplate;
use App\Models\ProviderBookingTemplateItem;
use App\Models\ProviderBookingTemplateItemTranslation;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Creates provider booking fields and replaces the set assigned to one service.
 */
final class ProviderBookingFieldWriter
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function create(Account $account, array $input): ProviderBookingField
    {
        $validated = $this->validateDefinition($input);

        return DB::transaction(function () use ($account, $validated): ProviderBookingField {
            $next = (int) ProviderBookingField::query()
                ->where('account_id', $account->id)
                ->max('sort_order');

            $field = ProviderBookingField::query()->create([
                'account_id' => (int) $account->id,
                'scope' => $validated['scope'],
                'input_type' => $validated['input_type'],
                'sort_order' => $next > 0 ? $next + 10 : 10,
                'is_active' => true,
            ]);

            $this->replaceTranslations($field, $validated['translations']);

            return $field;
        });
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function update(ProviderBookingField $field, array $input): void
    {
        $validated = $this->validateDefinition($input);

        DB::transaction(function () use ($field, $validated): void {
            $field->update([
                'scope' => $validated['scope'],
                'input_type' => $validated['input_type'],
            ]);

            $this->replaceTranslations($field, $validated['translations']);
        });
    }

    /**
     * @param  list<int>  $allowedFieldIds
     * @param  array<string, mixed>  $input
     */
    public function syncServiceAssignments(Service $service, array $allowedFieldIds, array $input): void
    {
        $allowed = array_fill_keys($allowedFieldIds, true);
        $posted = is_array($input['assignments'] ?? null) ? $input['assignments'] : [];
        $rows = [];

        foreach ($posted as $fieldId => $row) {
            if (! is_array($row) || empty($row['assigned'])) {
                continue;
            }

            $id = (int) $fieldId;
            if (! isset($allowed[$id])) {
                throw ValidationException::withMessages([
                    'assignments' => (string) __('account.booking_fields.validation.field_not_allowed'),
                ]);
            }

            $sort = $row['sort_order'] ?? 9999;
            if ($sort === '' || $sort === null) {
                $sort = 9999;
            }

            $rows[] = [
                'provider_booking_field_id' => $id,
                'is_required' => ! empty($row['is_required']),
                'sort_order' => max(0, min(9999, (int) $sort)),
            ];
        }

        DB::transaction(function () use ($service, $rows): void {
            $service->bookingFieldAssignments()->delete();

            foreach ($rows as $row) {
                $service->bookingFieldAssignments()->create($row);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{
     *     scope: string,
     *     input_type: string,
     *     translations: list<array{language_id: int, label: string, help_text: string|null}>
     * }
     */
    private function validateDefinition(array $input): array
    {
        $validator = validator($input, [
            'scope' => ['required', Rule::in(ProviderBookingField::scopes())],
            'input_type' => ['required', Rule::in(ProviderBookingField::inputTypes())],
            'translations' => ['required', 'array'],
            'translations.*.language_id' => ['required', 'integer', Rule::exists(\App\Models\Language::class, 'id')],
            'translations.*.label' => ['nullable', 'string', 'max:120'],
            'translations.*.help_text' => ['nullable', 'string', 'max:255'],
        ]);

        $validated = $validator->validate();
        $translations = [];

        foreach ($validated['translations'] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $help = trim((string) ($row['help_text'] ?? ''));
            $translations[] = [
                'language_id' => (int) $row['language_id'],
                'label' => $label,
                'help_text' => $help !== '' ? $help : null,
            ];
        }

        if ($translations === []) {
            throw ValidationException::withMessages([
                'translations' => (string) __('account.booking_fields.validation.label_required'),
            ]);
        }

        return [
            'scope' => (string) $validated['scope'],
            'input_type' => (string) $validated['input_type'],
            'translations' => $translations,
        ];
    }

    /**
     * @param  list<array{language_id: int, label: string, help_text: string|null}>  $translations
     */
    private function replaceTranslations(ProviderBookingField $field, array $translations): void
    {
        $field->translations()->delete();

        foreach ($translations as $row) {
            ProviderBookingFieldTranslation::query()->create([
                'provider_booking_field_id' => (int) $field->id,
                'language_id' => $row['language_id'],
                'label' => $row['label'],
                'help_text' => $row['help_text'],
            ]);
        }
    }

    /**
     * Saves the template's own items. Items keep their id so a later replicate
     * still finds the catalog snapshot created from that item.
     *
     * @param  array<string, mixed>  $input
     */
    public function saveTemplate(?ProviderBookingTemplate $template, Account $account, array $input): ProviderBookingTemplate
    {
        $validated = validator($input, [
            'name' => ['required', 'string', 'max:120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.scope' => ['required', Rule::in(ProviderBookingField::scopes())],
            'items.*.input_type' => ['required', Rule::in(ProviderBookingField::inputTypes())],
            'items.*.is_required' => ['nullable', 'boolean'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'items.*.translations' => ['required', 'array'],
            'items.*.translations.*.language_id' => ['required', 'integer', Rule::exists(\App\Models\Language::class, 'id')],
            'items.*.translations.*.label' => ['nullable', 'string', 'max:120'],
            'items.*.translations.*.help_text' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $name = trim((string) $validated['name']);
        $ownedIds = $template === null
            ? []
            : $template->items()->pluck('id')->map(static fn (mixed $id): int => (int) $id)->all();
        $owned = array_fill_keys($ownedIds, true);
        $rows = [];

        foreach ($validated['items'] as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $translations = $this->translationsFromInput($item['translations'] ?? []);
            if ($translations === []) {
                throw ValidationException::withMessages([
                    'items.'.$index.'.translations' => (string) __('account.booking_fields.templates.validation.item_label_required'),
                ]);
            }

            $postedId = $item['id'] ?? null;
            $id = is_numeric($postedId) ? (int) $postedId : null;
            if ($id !== null && ! isset($owned[$id])) {
                $id = null;
            }

            $sort = $item['sort_order'] ?? 9999;
            if ($sort === '' || $sort === null) {
                $sort = 9999;
            }

            $rows[] = [
                'id' => $id,
                'scope' => (string) $item['scope'],
                'input_type' => (string) $item['input_type'],
                'is_required' => ! empty($item['is_required']),
                'sort_order' => max(0, min(9999, (int) $sort)),
                'translations' => $translations,
            ];
        }

        if ($rows === []) {
            throw ValidationException::withMessages([
                'items' => (string) __('account.booking_fields.templates.validation.items_required'),
            ]);
        }

        $duplicate = ProviderBookingTemplate::query()
            ->where('account_id', $account->id)
            ->where('name', $name)
            ->when($template !== null, fn ($query) => $query->whereKeyNot($template->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'name' => (string) __('account.booking_fields.templates.validation.name_taken'),
            ]);
        }

        return DB::transaction(function () use ($template, $account, $name, $rows): ProviderBookingTemplate {
            if ($template === null) {
                $template = ProviderBookingTemplate::query()->create([
                    'account_id' => (int) $account->id,
                    'name' => $name,
                    'sort_order' => 9999,
                ]);
            } else {
                $template->update(['name' => $name]);
            }

            $kept = [];

            foreach ($rows as $row) {
                if ($row['id'] !== null) {
                    $item = $template->items()->whereKey($row['id'])->first();
                } else {
                    $item = null;
                }

                $values = [
                    'scope' => $row['scope'],
                    'input_type' => $row['input_type'],
                    'is_required' => $row['is_required'],
                    'sort_order' => $row['sort_order'],
                ];

                if ($item instanceof ProviderBookingTemplateItem) {
                    $item->update($values);
                } else {
                    $item = $template->items()->create($values);
                }

                $this->replaceItemTranslations($item, $row['translations']);
                $kept[] = (int) $item->id;
            }

            $template->items()->whereNotIn('id', $kept)->delete();

            return $template;
        });
    }

    /**
     * Replicates the template onto one service.
     *
     * The first replicate of an item creates a catalog field. Later replicates
     * reuse that field, so included services ask the question once. Assignment
     * order and the required flag follow the template at the moment of this
     * replicate. The catalog wording stays as it was on the first replicate.
     */
    public function copyTemplateToService(Service $service, ProviderBookingTemplate $template): void
    {
        $template->load('items.translations');

        DB::transaction(function () use ($service, $template): void {
            foreach ($template->items as $item) {
                $field = ProviderBookingField::query()
                    ->where('source_template_item_id', $item->id)
                    ->first();

                if ($field instanceof ProviderBookingField && (int) $field->account_id !== (int) $service->account_id) {
                    continue;
                }

                if (! $field instanceof ProviderBookingField) {
                    $next = (int) ProviderBookingField::query()
                        ->where('account_id', $service->account_id)
                        ->max('sort_order');

                    $field = ProviderBookingField::query()->create([
                        'account_id' => (int) $service->account_id,
                        'source_template_item_id' => (int) $item->id,
                        'scope' => $item->scope,
                        'input_type' => $item->input_type,
                        'sort_order' => $next > 0 ? $next + 10 : 10,
                        'is_active' => true,
                    ]);

                    $this->replaceTranslations($field, $item->translations->map(static fn (ProviderBookingTemplateItemTranslation $translation): array => [
                        'language_id' => (int) $translation->language_id,
                        'label' => (string) $translation->label,
                        'help_text' => $translation->help_text,
                    ])->all());
                }

                $values = [
                    'is_required' => (bool) $item->is_required,
                    'sort_order' => (int) $item->sort_order,
                ];

                $assignment = $service->bookingFieldAssignments()
                    ->where('provider_booking_field_id', $field->id)
                    ->first();

                if ($assignment === null) {
                    $service->bookingFieldAssignments()->create($values + [
                        'provider_booking_field_id' => (int) $field->id,
                    ]);

                    continue;
                }

                $assignment->update($values);
            }
        });
    }

    /**
     * @param  array<int|string, mixed>  $input
     * @return list<array{language_id: int, label: string, help_text: string|null}>
     */
    private function translationsFromInput(array $input): array
    {
        $translations = [];

        foreach ($input as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $help = trim((string) ($row['help_text'] ?? ''));
            $translations[] = [
                'language_id' => (int) $row['language_id'],
                'label' => $label,
                'help_text' => $help !== '' ? $help : null,
            ];
        }

        return $translations;
    }

    /**
     * @param  list<array{language_id: int, label: string, help_text: string|null}>  $translations
     */
    private function replaceItemTranslations(ProviderBookingTemplateItem $item, array $translations): void
    {
        $item->translations()->delete();

        foreach ($translations as $row) {
            ProviderBookingTemplateItemTranslation::query()->create([
                'provider_booking_template_item_id' => (int) $item->id,
                'language_id' => $row['language_id'],
                'label' => $row['label'],
                'help_text' => $row['help_text'],
            ]);
        }
    }
}
