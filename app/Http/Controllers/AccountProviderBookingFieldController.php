<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Language;
use App\Models\ProviderBookingField;
use App\Models\ProviderBookingTemplate;
use App\Models\ProviderBookingTemplateItem;
use App\Models\Service;
use App\Services\ProviderBookingFieldWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class AccountProviderBookingFieldController extends Controller
{
    public function __construct(
        private readonly ProviderBookingFieldWriter $writer,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->account($request);

        $fields = $account->providerBookingFields()
            ->with('translations.language.locale')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('account.booking-fields.index', [
            'account' => $account,
            'fields' => $fields,
        ]);
    }

    public function create(Request $request): View
    {
        $account = $this->account($request);

        return view('account.booking-fields.form', [
            'account' => $account,
            'field' => null,
            'languages' => $this->languages(),
            'translations' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->account($request);
        $this->writer->create($account, $request->all());

        return redirect()
            ->route('account.booking-fields.index')
            ->with('status', __('account.booking_fields.status_saved'));
    }

    public function edit(Request $request, ProviderBookingField $field): View
    {
        $account = $this->account($request);
        $this->assertField($field, (int) $account->id);
        $field->load('translations');

        $translations = [];
        foreach ($field->translations as $translation) {
            $translations[(int) $translation->language_id] = [
                'label' => $translation->label,
                'help_text' => $translation->help_text,
            ];
        }

        return view('account.booking-fields.form', [
            'account' => $account,
            'field' => $field,
            'languages' => $this->languages(),
            'translations' => $translations,
        ]);
    }

    public function update(Request $request, ProviderBookingField $field): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertField($field, (int) $account->id);
        $this->writer->update($field, $request->all());

        return redirect()
            ->route('account.booking-fields.index')
            ->with('status', __('account.booking_fields.status_saved'));
    }

    public function toggleActive(Request $request, ProviderBookingField $field): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertField($field, (int) $account->id);

        $field->is_active = ! $field->is_active;
        $field->save();

        return redirect()
            ->route('account.booking-fields.index')
            ->with('status', __($field->is_active
                ? 'account.booking_fields.status_activated'
                : 'account.booking_fields.status_deactivated'));
    }

    public function move(Request $request, ProviderBookingField $field, string $direction): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertField($field, (int) $account->id);
        abort_unless(in_array($direction, ['up', 'down'], true), 404);

        $ordered = ProviderBookingField::query()
            ->where('account_id', $account->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $ordered->search(
            fn (ProviderBookingField $row): bool => (int) $row->id === (int) $field->id
        );

        if ($index === false) {
            return redirect()->route('account.booking-fields.index');
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapIndex < 0 || $swapIndex >= $ordered->count()) {
            return redirect()->route('account.booking-fields.index');
        }

        DB::transaction(function () use ($ordered, $index, $swapIndex): void {
            $rows = $ordered->all();
            [$rows[$index], $rows[$swapIndex]] = [$rows[$swapIndex], $rows[$index]];

            foreach ($rows as $position => $row) {
                $row->update(['sort_order' => ($position + 1) * 10]);
            }
        });

        return redirect()
            ->route('account.booking-fields.index')
            ->with('status', __('account.booking_fields.status_reordered'));
    }

    public function editService(Request $request, Service $service): View
    {
        $account = $this->account($request);
        $this->assertService($service, (int) $account->id);

        $service->load([
            'translations.language.locale',
            'bookingFieldAssignments',
        ]);

        $assignedIds = $service->bookingFieldAssignments
            ->pluck('provider_booking_field_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $fields = ProviderBookingField::query()
            ->where('account_id', $account->id)
            ->where(function ($query) use ($assignedIds): void {
                $query->where('is_active', true);
                if ($assignedIds !== []) {
                    $query->orWhereIn('id', $assignedIds);
                }
            })
            ->with('translations.language.locale')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $assignments = $service->bookingFieldAssignments->keyBy('provider_booking_field_id');
        $fields = $fields->sort(function (ProviderBookingField $left, ProviderBookingField $right) use ($assignments): int {
            $leftStored = $assignments->get($left->id);
            $rightStored = $assignments->get($right->id);
            $leftSort = $leftStored !== null ? (int) $leftStored->sort_order : 100000 + (int) $left->sort_order;
            $rightSort = $rightStored !== null ? (int) $rightStored->sort_order : 100000 + (int) $right->sort_order;

            return [$leftSort, (int) $left->id] <=> [$rightSort, (int) $right->id];
        })->values();

        return view('account.booking-fields.service', [
            'account' => $account,
            'service' => $service,
            'fields' => $fields,
            'assignments' => $assignments,
            'templates' => $account->providerBookingTemplates()->orderBy('name')->get(),
        ]);
    }

    public function copyTemplate(Request $request, Service $service): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertService($service, (int) $account->id);

        $validated = $request->validate([
            'template_id' => ['required', 'integer'],
        ]);

        $template = ProviderBookingTemplate::query()
            ->where('account_id', $account->id)
            ->whereKey((int) $validated['template_id'])
            ->first();

        abort_unless($template instanceof ProviderBookingTemplate, 404);

        $this->writer->copyTemplateToService($service, $template);

        return redirect()
            ->route('account.services.booking-fields.edit', $service)
            ->with('status', __('account.booking_fields.templates.status_copied', ['name' => $template->name]));
    }

    public function templatesIndex(Request $request): View
    {
        $account = $this->account($request);

        return view('account.booking-fields.templates.index', [
            'account' => $account,
            'templates' => $account->providerBookingTemplates()->withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function templatesCreate(Request $request): View
    {
        $account = $this->account($request);

        return view('account.booking-fields.templates.form', [
            'account' => $account,
            'template' => null,
            'languages' => $this->languages(),
            'rows' => $this->templateRows(null),
        ]);
    }

    public function templatesStore(Request $request): RedirectResponse
    {
        $account = $this->account($request);
        $this->writer->saveTemplate(null, $account, $request->all());

        return redirect()
            ->route('account.booking-templates.index')
            ->with('status', __('account.booking_fields.templates.status_saved'));
    }

    public function templatesEdit(Request $request, ProviderBookingTemplate $template): View
    {
        $account = $this->account($request);
        $this->assertTemplate($template, (int) $account->id);
        $template->load('items.translations');

        return view('account.booking-fields.templates.form', [
            'account' => $account,
            'template' => $template,
            'languages' => $this->languages(),
            'rows' => $this->templateRows($template),
        ]);
    }

    public function templatesUpdate(Request $request, ProviderBookingTemplate $template): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertTemplate($template, (int) $account->id);

        $this->writer->saveTemplate($template, $account, $request->all());

        return redirect()
            ->route('account.booking-templates.index')
            ->with('status', __('account.booking_fields.templates.status_saved'));
    }

    public function templatesDestroy(Request $request, ProviderBookingTemplate $template): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertTemplate($template, (int) $account->id);
        $template->delete();

        return redirect()
            ->route('account.booking-templates.index')
            ->with('status', __('account.booking_fields.templates.status_removed'));
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertService($service, (int) $account->id);

        $request->validate([
            'assignments' => ['nullable', 'array'],
            'assignments.*.assigned' => ['nullable', 'boolean'],
            'assignments.*.is_required' => ['nullable', 'boolean'],
            'assignments.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $assignedIds = $service->bookingFieldAssignments()
            ->pluck('provider_booking_field_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $allowedIds = ProviderBookingField::query()
            ->where('account_id', $account->id)
            ->where(function ($query) use ($assignedIds): void {
                $query->where('is_active', true);
                if ($assignedIds !== []) {
                    $query->orWhereIn('id', $assignedIds);
                }
            })
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $this->writer->syncServiceAssignments($service, $allowedIds, $request->all());

        return redirect()
            ->route('account.services.booking-fields.edit', $service)
            ->with('status', __('account.booking_fields.service_saved'));
    }

    private function account(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    private function assertField(ProviderBookingField $field, int $accountId): void
    {
        abort_unless((int) $field->account_id === $accountId, 404);
    }

    private function assertService(Service $service, int $accountId): void
    {
        abort_unless((int) $service->account_id === $accountId, 404);
    }

    private function assertTemplate(ProviderBookingTemplate $template, int $accountId): void
    {
        abort_unless((int) $template->account_id === $accountId, 404);
    }

    /**
     * @return list<array{
     *     id: int|string|null,
     *     scope: string,
     *     input_type: string,
     *     is_required: bool,
     *     sort_order: int|string,
     *     translations: array<int|string, mixed>
     * }>
     */
    private function templateRows(?ProviderBookingTemplate $template): array
    {
        $posted = old('items');
        if (is_array($posted)) {
            $rows = [];

            foreach (array_values($posted) as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $rows[] = [
                    'id' => $row['id'] ?? null,
                    'scope' => (string) ($row['scope'] ?? ProviderBookingField::SCOPE_PASSENGER),
                    'input_type' => (string) ($row['input_type'] ?? ProviderBookingField::TYPE_TEXT),
                    'is_required' => ! empty($row['is_required']),
                    'sort_order' => $row['sort_order'] ?? 10,
                    'translations' => is_array($row['translations'] ?? null) ? $row['translations'] : [],
                ];
            }

            return $rows;
        }

        if ($template === null) {
            return [[
                'id' => null,
                'scope' => ProviderBookingField::SCOPE_PASSENGER,
                'input_type' => ProviderBookingField::TYPE_TEXT,
                'is_required' => true,
                'sort_order' => 10,
                'translations' => [],
            ]];
        }

        return $template->items->map(function (ProviderBookingTemplateItem $item): array {
            $translations = [];

            foreach ($item->translations as $translation) {
                $translations[(int) $translation->language_id] = [
                    'label' => $translation->label,
                    'help_text' => $translation->help_text,
                ];
            }

            return [
                'id' => (int) $item->id,
                'scope' => (string) $item->scope,
                'input_type' => (string) $item->input_type,
                'is_required' => (bool) $item->is_required,
                'sort_order' => (int) $item->sort_order,
                'translations' => $translations,
            ];
        })->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Language>
     */
    private function languages()
    {
        return Language::query()->with('locale')->get();
    }
}
