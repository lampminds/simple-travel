<script>
    (function () {
        function initCatalogHelperPopovers() {
            if (typeof window.bootstrap === 'undefined' || !window.bootstrap.Popover) {
                return;
            }

            document.querySelectorAll('[data-catalog-helper-trigger]').forEach(function (btn) {
                if (window.bootstrap.Popover.getInstance(btn)) {
                    return;
                }

                const contentId = btn.getAttribute('data-catalog-helper-content');
                if (!contentId) {
                    return;
                }

                const source = document.getElementById(contentId);
                if (!source) {
                    return;
                }

                const content = source.innerHTML;
                if (!content || !content.trim()) {
                    return;
                }

                new window.bootstrap.Popover(btn, {
                    html: true,
                    sanitize: false,
                    trigger: 'hover focus click',
                    placement: 'auto',
                    title: @json(__('wizard.catalog_helper.popover_title')),
                    content: content,
                    customClass: 'catalog-helper-popover',
                    container: 'body',
                });
            });

            if (window.feather) {
                window.feather.replace();
            }
        }

        function registerLivewireHook() {
            if (!window.Livewire || typeof window.Livewire.hook !== 'function') {
                return;
            }
            window.Livewire.hook('morph.updated', function () {
                queueMicrotask(initCatalogHelperPopovers);
            });
        }

        if (window.Livewire && typeof window.Livewire.hook === 'function') {
            registerLivewireHook();
        } else {
            document.addEventListener('livewire:init', registerLivewireHook);
        }

        window.addEventListener('load', function () {
            queueMicrotask(initCatalogHelperPopovers);
        });
    })();
</script>
