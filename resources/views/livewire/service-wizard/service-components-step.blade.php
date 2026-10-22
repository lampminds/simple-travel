<div>
    @if ($flashMessage)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $flashMessage }}
            <button type="button" class="btn-close" wire:click="$set('flashMessage', null)" aria-label="{{ __('filament.common.close') }}"></button>
        </div>
    @endif

    <h6 class="mb-1">{{ __('wizard.components_heading') }}</h6>
    <p class="text-muted small mb-3">{{ __('wizard.components_intro') }}</p>

    @if ($parentVariants->isEmpty())
        <div class="alert alert-light border" role="status">
            {{ __('wizard.components_needs_variant') }}
        </div>
    @else
        <div class="row g-3 align-items-end mb-3">
            <div class="col-12 col-lg-5">
                <label for="component-parent-variant" class="form-label">{{ __('wizard.components_parent_variant') }}</label>
                <select id="component-parent-variant" class="form-select" wire:model.live="parentVariantId">
                    @foreach ($parentVariants as $variant)
                        <option value="{{ $variant->id }}">{{ $variant->dropdownLabel(false) }}</option>
                    @endforeach
                </select>
                @error('parentVariantId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        @if ($selectedComponents->isEmpty())
            <div class="alert alert-light border" role="status">
                {{ __('wizard.components_none') }}
            </div>
        @else
            <div class="table-responsive mb-3">
                <table class="table table-hover align-middle bg-white border rounded mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('wizard.components_col_child') }}</th>
                            <th scope="col">{{ __('wizard.components_col_quantity') }}</th>
                            <th scope="col">{{ __('wizard.components_col_day') }}</th>
                            <th scope="col">{{ __('wizard.components_col_inclusion') }}</th>
                            <th scope="col" class="text-end">{{ __('wizard.provider_services_col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($selectedComponents as $component)
                            <tr>
                                <td>{{ $component->childVariant?->dropdownLabel() ?? ('#'.$component->child_variant_id) }}</td>
                                <td>{{ $component->quantity }}</td>
                                <td>{{ $component->day_number ?? '—' }}</td>
                                <td>{{ __('wizard.components_inclusion.'.$component->inclusion_mode) }}</td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="moveComponent({{ $component->id }}, 'up')" title="{{ __('wizard.components_move_up') }}">↑</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="moveComponent({{ $component->id }}, 'down')" title="{{ __('wizard.components_move_down') }}">↓</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeComponent({{ $component->id }})">{{ __('wizard.components_remove') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <form wire:submit="addComponent">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-5">
                    <label for="component-child-variant" class="form-label">{{ __('wizard.components_child_variant') }}</label>
                    <select id="component-child-variant" class="form-select" wire:model="form.child_variant_id">
                        <option value="">{{ __('wizard.components_select_child') }}</option>
                        @foreach ($eligibleChildVariants as $variant)
                            <option value="{{ $variant->id }}">{{ $variant->dropdownLabel() }}</option>
                        @endforeach
                    </select>
                    @error('form.child_variant_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    @if ($eligibleChildVariants->isEmpty())
                        <div class="text-muted small mt-1">{{ __('wizard.components_no_children_available') }}</div>
                    @endif
                </div>
                <div class="col-6 col-lg-1">
                    <label for="component-quantity" class="form-label">{{ __('wizard.components_quantity') }}</label>
                    <input id="component-quantity" type="number" min="1" class="form-control" wire:model="form.quantity">
                    @error('form.quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 col-lg-1">
                    <label for="component-day" class="form-label">{{ __('wizard.components_day_number') }}</label>
                    <input id="component-day" type="number" min="1" class="form-control" wire:model="form.day_number">
                    @error('form.day_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-lg-2">
                    <label for="component-inclusion" class="form-label">{{ __('wizard.components_inclusion_mode') }}</label>
                    <select id="component-inclusion" class="form-select" wire:model="form.inclusion_mode">
                        @foreach (['included', 'optional', 'upgrade'] as $mode)
                            <option value="{{ $mode }}">{{ __('wizard.components_inclusion.'.$mode) }}</option>
                        @endforeach
                    </select>
                    @error('form.inclusion_mode') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-lg-3">
                    <label for="component-notes" class="form-label">{{ __('wizard.components_notes') }}</label>
                    <input id="component-notes" type="text" class="form-control" wire:model="form.notes">
                    @error('form.notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <span class="text-muted small">{{ __('wizard.components_save_hint') }}</span>
                <button type="submit" class="btn btn-primary">{{ __('wizard.components_add') }}</button>
            </div>
        </form>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <a href="{{ route('catalog') }}" class="btn btn-outline-secondary">{{ __('wizard.components_done') }}</a>
        </div>
    @endif
</div>
