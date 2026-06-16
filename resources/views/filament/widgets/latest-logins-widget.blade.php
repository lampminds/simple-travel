<x-filament-widgets::widget class="fi-wi-latest-logins">
    <x-filament::section
        :heading="$heading"
        compact
    >
        @include('filament.widgets.partials.compact-recent-users-table', [
            'columns' => ['Usuario', 'Cuenta', 'Acceso'],
            'rows' => $users->map(fn (array $user): array => $this->toCompactTableRow($user)),
            'emptyMessage' => 'Sin accesos registrados.',
            'showImpersonation' => $showImpersonation,
        ])
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
