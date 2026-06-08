<script>
    (function () {
        function refreshFeatherIcons() {
            if (typeof window.feather === 'undefined' || typeof window.feather.replace !== 'function') {
                return;
            }

            window.feather.replace();
        }

        function registerLivewireHook() {
            if (!window.Livewire || typeof window.Livewire.hook !== 'function') {
                return;
            }

            window.Livewire.hook('morph.updated', function () {
                queueMicrotask(refreshFeatherIcons);
            });
        }

        if (window.Livewire && typeof window.Livewire.hook === 'function') {
            registerLivewireHook();
        } else {
            document.addEventListener('livewire:init', registerLivewireHook);
        }
    })();
</script>
