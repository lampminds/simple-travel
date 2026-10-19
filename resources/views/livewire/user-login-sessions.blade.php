<div class="overflow-x-auto">
    <table class="w-full text-start text-sm">
        <thead>
            <tr class="border-b border-gray-200 text-gray-500 dark:border-white/10 dark:text-gray-400">
                <th class="px-3 py-2 font-medium whitespace-nowrap">{{ __('filament.resources.user_sessions.logged_in_at') }}</th>
                <th class="px-3 py-2 font-medium whitespace-nowrap">{{ __('filament.resources.user_sessions.ip') }}</th>
                <th class="px-3 py-2 font-medium">{{ __('filament.resources.user_sessions.origin') }}</th>
                <th class="px-3 py-2 font-medium">{{ __('filament.resources.user_sessions.user_agent') }}</th>
                <th class="px-3 py-2 font-medium whitespace-nowrap">{{ __('filament.resources.user_sessions.active') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
            @forelse ($sessions as $session)
                <tr wire:key="lmp-session-{{ $session->id }}">
                    <td class="px-3 py-2 whitespace-nowrap">{{ locale_datetime($session->logged_in_at) }}</td>
                    <td class="px-3 py-2 whitespace-nowrap font-mono text-xs">{{ $session->ip?->ip_address ?: '—' }}</td>
                    <td class="px-3 py-2">{{ $session->ip?->originLabel() ?: __('filament.resources.user_sessions.unavailable') }}</td>
                    <td class="px-3 py-2 max-w-xs truncate" title="{{ $session->user_agent }}">
                        {{ \Illuminate\Support\Str::limit((string) $session->user_agent, 80) ?: '—' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if ($session->session_id && isset($activeSessionIds[(string) $session->session_id]))
                            <span class="text-xs font-medium text-success-600 dark:text-success-400">{{ __('filament.resources.user_sessions.yes') }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-6 text-gray-500 dark:text-gray-400">
                        {{ __('filament.resources.user_sessions.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($sessions->hasPages())
        <div class="px-3 py-3">
            {{ $sessions->links() }}
        </div>
    @endif
</div>
