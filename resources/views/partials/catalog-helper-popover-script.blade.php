<style>
    .popover.catalog-helper-popover .popover-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .popover.catalog-helper-popover .popover-header .btn-close {
        flex-shrink: 0;
        margin-left: auto;
    }
</style>
<script>
    (function () {
        const POPOVER_CLASS = 'catalog-helper-popover';
        const TRIGGER_SELECTOR = '[data-catalog-helper-trigger]';
        let documentListenersRegistered = false;

        function hideAllCatalogHelperPopovers(exceptBtn) {
            document.querySelectorAll(TRIGGER_SELECTOR).forEach(function (btn) {
                if (exceptBtn && btn === exceptBtn) {
                    return;
                }

                const instance = window.bootstrap?.Popover?.getInstance(btn);
                if (instance) {
                    instance.hide();
                }
            });
        }

        function disposeAllCatalogHelperPopovers() {
            document.querySelectorAll(TRIGGER_SELECTOR).forEach(function (btn) {
                const instance = window.bootstrap?.Popover?.getInstance(btn);
                if (instance) {
                    instance.dispose();
                }
            });
        }

        function buildPopoverTitle() {
            const label = @json(__('wizard.catalog_helper.popover_title'));
            const closeLabel = @json(__('filament.common.close'));

            return label
                + '<button type="button" class="btn-close btn-close-sm" data-catalog-helper-close aria-label="'
                + closeLabel
                + '"></button>';
        }

        function registerDocumentListeners() {
            if (documentListenersRegistered) {
                return;
            }

            documentListenersRegistered = true;

            document.addEventListener('click', function (e) {
                if (e.target.closest('.popover.' + POPOVER_CLASS + ' [data-catalog-helper-close]')) {
                    hideAllCatalogHelperPopovers();

                    return;
                }

                const clickedTrigger = e.target.closest(TRIGGER_SELECTOR);
                const clickedPopover = e.target.closest('.popover.' + POPOVER_CLASS);

                if (!clickedTrigger && !clickedPopover) {
                    hideAllCatalogHelperPopovers();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    hideAllCatalogHelperPopovers();
                }
            });
        }

        function initCatalogHelperPopovers() {
            if (typeof window.bootstrap === 'undefined' || !window.bootstrap.Popover) {
                return;
            }

            registerDocumentListeners();

            document.querySelectorAll(TRIGGER_SELECTOR).forEach(function (btn) {
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

                const popover = new window.bootstrap.Popover(btn, {
                    html: true,
                    sanitize: false,
                    trigger: 'hover',
                    placement: 'auto',
                    title: buildPopoverTitle(),
                    content: content,
                    customClass: POPOVER_CLASS,
                    container: 'body',
                });

                btn.addEventListener('shown.bs.popover', function () {
                    hideAllCatalogHelperPopovers(btn);
                });

                btn.addEventListener('click', function (e) {
                    if (window.matchMedia('(hover: hover)').matches) {
                        e.preventDefault();

                        return;
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    if (popover._isShown) {
                        popover.hide();
                    } else {
                        hideAllCatalogHelperPopovers(btn);
                        popover.show();
                    }
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
                hideAllCatalogHelperPopovers();
                disposeAllCatalogHelperPopovers();
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
