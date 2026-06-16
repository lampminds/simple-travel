@props([
    'columns',
    'rows',
    'emptyMessage',
    'showImpersonation' => false,
])

@if ($rows->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ $emptyMessage }}
    </p>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="table-layout: fixed; width: 100%;">
            <colgroup>
                @if ($showImpersonation)
                    <col style="width: 24%">
                @endif
                <col style="width: {{ $showImpersonation ? '26%' : '38%' }}">
                <col style="width: {{ $showImpersonation ? '26%' : '38%' }}">
                <col style="width: {{ $showImpersonation ? '24%' : '24%' }}">
            </colgroup>
            <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400">
                    @if ($showImpersonation)
                        <th class="pb-3 font-medium" style="padding-right: 1.25rem; white-space: nowrap;">
                            {{ __('filament.resources.user_actions.impersonate') }}
                        </th>
                    @endif
                    @foreach ($columns as $column)
                        <th
                            @class([
                                'pb-3 font-medium',
                                'text-end' => $loop->last,
                            ])
                            style="padding-right: {{ $loop->last ? '0' : '1.25rem' }}; white-space: nowrap;"
                        >
                            {{ $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach ($rows as $row)
                    <tr>
                        @if ($showImpersonation)
                            <td class="py-2.5" style="padding-right: 1.25rem; white-space: nowrap; vertical-align: middle;">
                                @if ($row['can_impersonate'] ?? false)
                                    <button
                                        type="button"
                                        wire:click="mountAction('openWebsiteImpersonation', { record: {{ $row['id'] }} })"
                                        title="{{ __('filament.resources.user_actions.open_website_impersonation_tooltip') }}"
                                        aria-label="{{ __('filament.resources.user_actions.impersonate') }}"
                                        style="padding: 0; border: 0; background: transparent; cursor: pointer; white-space: nowrap; font-size: inherit; color: rgb(180 83 9); text-decoration: underline; text-underline-offset: 2px;"
                                    >
                                        {{ __('filament.resources.user_actions.impersonate') }}
                                    </button>
                                @else
                                    <span style="color: rgb(156 163 175);">&mdash;</span>
                                @endif
                            </td>
                        @endif
                        @foreach ($row['cells'] as $index => $cell)
                            <td
                                class="py-2.5 {{ $loop->last ? 'text-end' : '' }}"
                                style="padding-right: {{ $loop->last ? '0' : '1.25rem' }}; white-space: nowrap;"
                            >
                                <span
                                    @class([
                                        'font-medium text-gray-950 dark:text-white' => $index === 0,
                                        'text-gray-600 dark:text-gray-300' => $index === 1,
                                        'text-gray-500 dark:text-gray-400' => $loop->last,
                                    ])
                                    @if (filled($row['titles'][$index] ?? null))
                                        title="{{ $row['titles'][$index] }}"
                                    @endif
                                >
                                    {{ $cell }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
