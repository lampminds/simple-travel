<div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold">{{ __('wizard.step6_validation_heading') }}</div>
            <ul class="mb-0 mt-2 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($categories->isEmpty() || $topicsByCategory->flatten()->isEmpty())
        <div class="alert alert-warning mb-0" role="alert">
            {{ __('wizard.step6_no_catalog') }}
        </div>
    @else
        <p class="text-muted small mb-3">{{ __('wizard.step6_list_intro') }}</p>

        <ul class="nav nav-tabs flex-wrap gap-1 gap-md-0 mb-3" role="tablist">
            @foreach ($visibilityTabs as $tab)
                <li class="nav-item" role="presentation">
                    <button
                        type="button"
                        class="nav-link @if ($activeVisibilityTab === $tab) active @endif"
                        wire:click="setVisibilityTab('{{ $tab }}')"
                    >
                        {{ __('filament.resources.service_detail_topic_visibility.'.$tab) }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content border border-top-0 rounded-bottom p-3 p-md-4 bg-white mb-0">
            @if (! $this->hasCatalogForVisibility($activeVisibilityTab))
                <div class="alert alert-warning mb-0" role="alert">
                    {{ __('wizard.step6_no_catalog_for_visibility', ['visibility' => __('filament.resources.service_detail_topic_visibility.'.$activeVisibilityTab)]) }}
                </div>
            @else
                <p class="text-muted small mb-3">{{ __('wizard.step6_tab_intro_'.$activeVisibilityTab) }}</p>

                <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                    <button type="button" class="btn btn-primary" wire:click="openAddModal">
                        {{ __('wizard.step6_add_detail') }}
                    </button>
                </div>

                @php
                    $visibleLineIndexes = [];
                    foreach ($lines as $index => $line) {
                        if ($this->lineMatchesActiveTab($line)) {
                            $visibleLineIndexes[] = $index;
                        }
                    }
                @endphp

                @if (count($visibleLineIndexes) === 0)
                    <div class="alert alert-light border mb-0" role="status">
                        {{ __('wizard.step6_empty_list') }}
                    </div>
                @else
                    <table class="table table-hover align-middle bg-white rounded border mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center text-nowrap" style="width: 1%;">{{ __('wizard.step6_col_order') }}</th>
                                <th scope="col">{{ __('wizard.step6_col_category') }}</th>
                                <th scope="col">{{ __('wizard.step6_col_topic') }}</th>
                                <th scope="col">{{ __('wizard.step6_col_condition_key') }}</th>
                                <th scope="col" class="text-center">{{ __('wizard.step6_col_mandatory') }}</th>
                                <th scope="col">{{ __('wizard.step6_col_excerpt') }}</th>
                                <th scope="col" class="text-center">{{ __('wizard.step6_col_active') }}</th>
                                <th scope="col" class="text-end">{{ __('wizard.step6_col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visibleLineIndexes as $position => $index)
                                @php
                                    $line = $lines[$index];
                                    $cat = $categories->firstWhere('id', (int) ($line['category_id'] ?? 0));
                                    $topic = $topicsById->get((int) ($line['topic_id'] ?? 0));
                                    $conditionKey = $conditionKeysById->get((int) ($line['condition_key_id'] ?? 0));
                                @endphp
                                <tr wire:key="service-detail-line-{{ $activeVisibilityTab }}-{{ $index }}">
                                    <td class="text-center text-nowrap">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('wizard.step6_col_order') }}">
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                title="{{ __('wizard.step6_move_up') }}"
                                                @if (! $this->canMoveLineUp($index)) disabled @endif
                                                wire:click="moveLineUp({{ $index }})"
                                            >↑</button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                title="{{ __('wizard.step6_move_down') }}"
                                                @if (! $this->canMoveLineDown($index)) disabled @endif
                                                wire:click="moveLineDown({{ $index }})"
                                            >↓</button>
                                        </div>
                                    </td>
                                    <td>{{ $cat ? ($cat->name ?: $cat->code) : '—' }}</td>
                                    <td>{{ $topic ? ($topic->name ?: $topic->code) : '—' }}</td>
                                    <td class="small font-monospace">{{ $conditionKey?->code ?? '—' }}</td>
                                    <td class="text-center">
                                        @if ($line['is_mandatory'] ?? false)
                                            <span class="badge text-bg-warning">{{ __('wizard.step6_mandatory_yes') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $this->excerptForLine($line) }}</td>
                                    <td class="text-center">
                                        @if ($line['active'] ?? true)
                                            <span class="badge text-bg-success">{{ __('wizard.step6_active') }}</span>
                                        @else
                                            <span class="badge text-bg-secondary">{{ __('wizard.step6_inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-outline-secondary"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="{{ __('wizard.provider_services_actions_menu') }}"
                                            >
                                                <i class="icon icon-xs text-primary" data-feather="more-horizontal" aria-hidden="true"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item"
                                                        wire:click="openEditModal({{ $index }})"
                                                    >
                                                        <i class="icon-xxs icon me-2 text-primary" data-feather="edit-3" aria-hidden="true"></i>
                                                        {{ __('wizard.step6_edit') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item"
                                                        wire:click="toggleLineActive({{ $index }})"
                                                    >
                                                        <i class="icon-xxs icon me-2 text-secondary" data-feather="eye" aria-hidden="true"></i>
                                                        {{ __('wizard.step6_toggle_active') }}
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item text-danger"
                                                        wire:click="deleteLine({{ $index }})"
                                                        wire:confirm="{{ __('wizard.step6_delete_confirm') }}"
                                                    >
                                                        <i class="icon-xxs icon me-2" data-feather="trash-2" aria-hidden="true"></i>
                                                        {{ __('wizard.step6_delete') }}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>

        @if ($showModal)
            @php
                $modalCategoryId = (int) ($modalLine['category_id'] ?? 0);
                $modalTopicOptions = $this->topicsForCategoryAndVisibility(
                    $topicsByCategory->get($modalCategoryId, collect()),
                    $modalVisibility
                );
            @endphp
            <div
                class="modal fade show d-block"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                style="background-color: rgba(0, 0, 0, 0.45);"
                wire:keydown.escape.window="closeModal"
            >
                <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content" wire:click.stop>
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $modalLineIndex === null ? __('wizard.step6_modal_title_add') : __('wizard.step6_modal_title_edit') }}
                                <span class="text-muted fw-normal small ms-1">
                                    — {{ __('filament.resources.service_detail_topic_visibility.'.$modalVisibility) }}
                                </span>
                            </h5>
                            <button type="button" class="btn-close" aria-label="{{ __('wizard.step6_modal_cancel') }}" wire:click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-detail-category">{{ __('wizard.step6_category') }}</label>
                                    <select
                                        id="modal-detail-category"
                                        class="form-select @error('modalLine.category_id') is-invalid @enderror"
                                        wire:model.live="modalLine.category_id"
                                        wire:change="clearModalTopic"
                                    >
                                        <option value="">{{ __('wizard.step6_select_category') }}</option>
                                        @foreach ($modalCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name ?: $cat->code }}</option>
                                        @endforeach
                                    </select>
                                    @error('modalLine.category_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-detail-topic">{{ __('wizard.step6_topic') }}</label>
                                    <select
                                        id="modal-detail-topic"
                                        class="form-select @error('modalLine.topic_id') is-invalid @enderror"
                                        wire:model.live="modalLine.topic_id"
                                        @if ($modalCategoryId < 1) disabled @endif
                                    >
                                        <option value="">{{ __('wizard.step6_select_topic') }}</option>
                                        @foreach ($modalTopicOptions as $topic)
                                            <option value="{{ $topic->id }}">{{ $topic->name ?: $topic->code }}</option>
                                        @endforeach
                                    </select>
                                    @error('modalLine.topic_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="modal-detail-condition-key">{{ __('wizard.step6_condition_key') }}</label>
                                    <select
                                        id="modal-detail-condition-key"
                                        class="form-select @error('modalLine.condition_key_id') is-invalid @enderror"
                                        wire:model="modalLine.condition_key_id"
                                    >
                                        <option value="">{{ __('wizard.step6_select_condition_key') }}</option>
                                        @foreach ($conditionKeysByCategory as $category => $keys)
                                            <optgroup label="{{ $this->conditionKeyCategoryLabel($category) }}">
                                                @foreach ($keys as $key)
                                                    <option value="{{ $key->id }}">{{ $key->code }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('modalLine.condition_key_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="modal-detail-mandatory"
                                            wire:model="modalLine.is_mandatory"
                                        >
                                        <label class="form-check-label" for="modal-detail-mandatory">{{ __('wizard.step6_is_mandatory') }}</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="modal-detail-active"
                                            wire:model="modalLine.active"
                                        >
                                        <label class="form-check-label" for="modal-detail-active">{{ __('wizard.step6_active_label') }}</label>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted small mb-2">{{ __('wizard.step6_modal_translations_help') }}</p>

                            <div class="row">
                                @foreach ($languages as $language)
                                    @php $langId = (int) $language->id; @endphp
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="border rounded p-3 mb-3 bg-body-secondary bg-opacity-25">
                                            <div class="d-flex align-items-center justify-content-between mb-2 gap-2 flex-wrap">
                                                <span class="fw-medium">{{ $language->display_name }}</span>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="{{ __('wizard.step6_translate') }}"
                                                    wire:click="translateModal({{ $langId }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="translateModal({{ $langId }})"
                                                >
                                                    <span wire:loading.remove wire:target="translateModal({{ $langId }})">🌐</span>
                                                    <span wire:loading wire:target="translateModal({{ $langId }})" class="spinner-border spinner-border-sm" role="status"></span>
                                                    <span class="visually-hidden">{{ __('wizard.step6_translate') }}</span>
                                                </button>
                                            </div>
                                            <label class="form-label small text-muted" for="modal-detail-desc-{{ $langId }}">{{ __('wizard.step6_description') }}</label>
                                            <textarea
                                                id="modal-detail-desc-{{ $langId }}"
                                                class="form-control @error('modalLine.translations.'.$langId.'.description') is-invalid @enderror"
                                                rows="4"
                                                wire:model="modalLine.translations.{{ $langId }}.description"
                                            ></textarea>
                                            @error('modalLine.translations.'.$langId.'.description')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('modalLine.translations')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">
                                {{ __('wizard.step6_modal_cancel') }}
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveModal" wire:loading.attr="disabled" wire:target="saveModal">
                                <span wire:loading.remove wire:target="saveModal">{{ __('wizard.step6_modal_save') }}</span>
                                <span wire:loading wire:target="saveModal" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
