<x-filament-widgets::widget class="fi-wi-latest-signups">
    <x-filament::section
        :heading="$heading"
        compact
    >
        @include('filament.widgets.partials.compact-recent-users-table', [
            'columns' => ['Usuario', 'Cuenta', 'Registro'],
            'rows' => $users->map(fn (array $user): array => $this->toCompactTableRow($user)),
            'emptyMessage' => 'Sin registraciones recientes.',
            'showImpersonation' => $showImpersonation,
        ])
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
