{{-- When cluster (or any) page sub-navigation is visible on desktop, collapse the main panel sidebar
     so the secondary column does not reduce usable table width. Uses Filament's Alpine sidebar store. --}}
<script>
    (function () {
        function isLargeScreen() {
            return window.matchMedia('(min-width: 1024px)').matches;
        }

        function syncMainSidebarWithPageSubNavigation() {
            if (!isLargeScreen()) {
                return;
            }

            if (!window.Alpine || typeof Alpine.store !== 'function') {
                return;
            }

            const sidebarStore = Alpine.store('sidebar');
            if (
                !sidebarStore ||
                typeof sidebarStore.close !== 'function' ||
                typeof sidebarStore.open !== 'function'
            ) {
                return;
            }

            const hasPageSubNavigation = document.querySelector('.fi-page-has-sub-navigation');

            if (hasPageSubNavigation) {
                sidebarStore.close();
            } else {
                sidebarStore.open();
            }
        }

        document.addEventListener('livewire:navigated', function () {
            window.requestAnimationFrame(function () {
                window.setTimeout(syncMainSidebarWithPageSubNavigation, 0);
            });
        });

        document.addEventListener('alpine:init', function () {
            window.setTimeout(syncMainSidebarWithPageSubNavigation, 0);
        });

        document.addEventListener('livewire:init', function () {
            window.setTimeout(syncMainSidebarWithPageSubNavigation, 10);
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                window.setTimeout(syncMainSidebarWithPageSubNavigation, 50);
            });
        } else {
            window.setTimeout(syncMainSidebarWithPageSubNavigation, 50);
        }
    })();
</script>
