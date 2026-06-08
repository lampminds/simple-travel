@php
    $pIndex = $pIndex ?? 0;
    $packageCondition = $packageCondition ?? [];
    $selectedTopicId = (string) ($packageCondition['service_detail_topic_id'] ?? '');
    $selectedAction = (string) ($packageCondition['action'] ?? '');
    $translations = is_array($packageCondition['translations'] ?? null) ? $packageCondition['translations'] : [];
@endphp
<div class="border rounded p-3 package-condition-row" data-package-condition-row>
    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
        <strong class="small text-uppercase text-muted">{{ __('account.operator_packages.conditions.package_row_label') }}</strong>
        <button type="button" class="btn btn-sm btn-outline-danger package-condition-remove" title="{{ __('account.operator_packages.conditions.remove') }}">×</button>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">{{ __('account.operator_packages.conditions.topic') }}</label>
            <select
                name="package_conditions[{{ $pIndex }}][service_detail_topic_id]"
                class="form-select package-condition-topic"
                required
            >
                <option value="">{{ __('account.operator_packages.conditions.topic_placeholder') }}</option>
                @foreach ($packageTopicOptions as $option)
                    <option
                        value="{{ $option['topic_id'] }}"
                        data-allowed-actions="{{ json_encode($option['allowed_actions']) }}"
                        data-override-mode="{{ $option['operator_override_mode'] }}"
                        @selected($selectedTopicId === (string) $option['topic_id'])
                    >{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('account.operator_packages.conditions.action') }}</label>
            <select
                name="package_conditions[{{ $pIndex }}][action]"
                class="form-select package-condition-action"
                required
            >
                @foreach ($packageActionLabels ?? $actionLabels as $value => $label)
                    @if ($value === '')
                        @continue
                    @endif
                    <option value="{{ $value }}" @selected($selectedAction === (string) $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 package-condition-text-block">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                @foreach ($languages as $language)
                    @php $langId = (int) $language->id; @endphp
                    <div class="col">
                        <label class="form-label">{{ $language->display_name }}</label>
                        <textarea
                            name="package_conditions[{{ $pIndex }}][translations][{{ $langId }}]"
                            class="form-control package-condition-custom-text"
                            rows="3"
                        >{{ old("package_conditions.{$pIndex}.translations.{$langId}", $translations[$langId] ?? $translations[(string) $langId] ?? '') }}</textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
