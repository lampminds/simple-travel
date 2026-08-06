@php
    $url = (string) ($url ?? '');
@endphp

<div
    x-data="{
        url: @js($url),
        copied: false,
        copyFailed: false,
        copyLink() {
            this.copied = false;
            this.copyFailed = false;

            const input = this.$refs.linkInput;
            if (! input || ! this.url) {
                this.copyFailed = true;
                return;
            }

            input.focus();
            input.select();
            input.setSelectionRange(0, this.url.length);

            let ok = false;

            try {
                ok = document.execCommand('copy');
            } catch (error) {
                ok = false;
            }

            if (ok) {
                this.copied = true;
                window.setTimeout(() => { this.copied = false; }, 2000);

                return;
            }

            if (window.isSecureContext && navigator.clipboard?.writeText) {
                navigator.clipboard.writeText(this.url)
                    .then(() => {
                        this.copied = true;
                        window.setTimeout(() => { this.copied = false; }, 2000);
                    })
                    .catch(() => {
                        this.copyFailed = true;
                    });

                return;
            }

            this.copyFailed = true;
        },
    }"
    class="space-y-2"
>
    <div class="flex items-stretch gap-2">
        <input
            x-ref="linkInput"
            type="text"
            readonly
            :value="url"
            aria-label="{{ __('filament.resources.user_actions.impersonation_link_aria') }}"
            class="fi-input min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 font-mono text-sm dark:border-gray-600 dark:bg-gray-900"
            @focus="$event.target.select()"
            @click="$event.target.select()"
        />

        <button
            type="button"
            class="inline-flex shrink-0 items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
            title="{{ __('filament.resources.user_actions.impersonation_copy_button') }}"
            aria-label="{{ __('filament.resources.user_actions.impersonation_copy_button') }}"
            x-on:click.prevent.stop="copyLink()"
        >
            <x-filament::icon
                icon="heroicon-o-clipboard-document-list"
                x-show="! copied"
                class="h-5 w-5"
            />
            <x-filament::icon
                icon="heroicon-o-check"
                x-show="copied"
                x-cloak
                class="h-5 w-5 text-success-600"
            />
        </button>
    </div>

    <p
        x-show="copied"
        x-cloak
        class="text-xs text-success-600 dark:text-success-400"
    >
        {{ __('filament.resources.user_actions.impersonation_copied') }}
    </p>

    <p
        x-show="copyFailed"
        x-cloak
        class="text-xs text-danger-600 dark:text-danger-400"
    >
        {{ __('filament.resources.user_actions.impersonation_copy_failed') }}
    </p>
</div>
