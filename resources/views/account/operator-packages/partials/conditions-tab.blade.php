@php
    $packageConditions = $packageConditions ?? [];
    $packageTopicOptions = $packageTopicOptions ?? [];
    $languages = $languages ?? collect();
    $itemConditionsUrl = route('account.operator-packages.item-conditions');
    $actionLabels = [
        '' => __('account.operator_packages.conditions.action_inherit'),
        'append_top' => __('account.operator_packages.conditions.action_append_top'),
        'append_bottom' => __('account.operator_packages.conditions.action_append_bottom'),
        'replace' => __('account.operator_packages.conditions.action_replace'),
        'suppress' => __('account.operator_packages.conditions.action_suppress'),
    ];
    $packageActionLabels = $actionLabels;
@endphp

<div class="tab-pane fade @if (($activeTab ?? '') === 'conditions') show active @endif" id="package-tab-conditions" role="tabpanel">
    <p class="text-muted small">{{ __('account.operator_packages.conditions.help') }}</p>

    <h5 class="h6 mb-3">{{ __('account.operator_packages.conditions.package_heading') }}</h5>
    <p class="text-muted small">{{ __('account.operator_packages.conditions.package_help') }}</p>

    <div id="package-conditions-list" class="d-flex flex-column gap-3 mb-4">
        @foreach ($packageConditions as $pIndex => $packageCondition)
            @include('account.operator-packages.partials.package-condition-row', [
                'pIndex' => $pIndex,
                'packageCondition' => $packageCondition,
                'packageTopicOptions' => $packageTopicOptions,
                'languages' => $languages,
                'actionLabels' => $actionLabels,
                'packageActionLabels' => $packageActionLabels,
            ])
        @endforeach
    </div>

    <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="package-add-condition">
        {{ __('account.operator_packages.conditions.add_package_condition') }}
    </button>

    <h5 class="h6 mb-3">{{ __('account.operator_packages.conditions.items_heading') }}</h5>
    <p class="text-muted small">{{ __('account.operator_packages.conditions.items_help') }}</p>
    <p class="text-muted small">{{ __('account.operator_packages.conditions.items_consolidated_note') }}</p>

    <div id="package-item-conditions" class="d-flex flex-column gap-3">
        <p class="text-muted small mb-0" id="package-item-conditions-empty">
            {{ __('account.operator_packages.conditions.items_empty') }}
        </p>
    </div>
</div>

<template id="package-condition-row-template">
    @include('account.operator-packages.partials.package-condition-row', [
        'pIndex' => '__PINDEX__',
        'packageCondition' => [
            'service_detail_topic_id' => '',
            'action' => '',
            'translations' => [],
        ],
        'packageTopicOptions' => $packageTopicOptions,
        'languages' => $languages,
        'actionLabels' => $actionLabels,
        'packageActionLabels' => $packageActionLabels,
    ])
</template>
