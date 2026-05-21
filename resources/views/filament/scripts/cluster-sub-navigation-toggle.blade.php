<script>
    (function () {
        const mountId = 'st-cluster-subnav-toggle';
        const hiddenClass = 'st-cluster-subnav-hidden';

        function isLargeScreen() {
            return window.matchMedia('(min-width: 1024px)').matches;
        }

        function hasClusterSubNavigation() {
            return document.querySelector('.fi-page-sub-navigation-sidebar-ctn') !== null;
        }

        function readHidden(storageKey) {
            try {
                return window.localStorage.getItem(storageKey) === '1';
            } catch (e) {
                return false;
            }
        }

        function writeHidden(storageKey, hidden) {
            try {
                window.localStorage.setItem(storageKey, hidden ? '1' : '0');
            } catch (e) {
                /* ignore */
            }
        }

        function applyHidden(hidden) {
            document.documentElement.classList.toggle(hiddenClass, hidden);
        }

        function ensureTopbarButton(mount) {
            if (!mount) {
                return;
            }

            const topbarEnd = document.querySelector('.fi-topbar-end');
            if (!topbarEnd) {
                return;
            }

            let btn = document.querySelector('.st-cluster-subnav-toggle-btn');
            if (btn) {
                return;
            }

            const storageKey = mount.dataset.storageKey || 'st_filament_cluster_subnav_hidden';
            const labelHide = mount.dataset.labelHide || 'Hide module menu';
            const labelShow = mount.dataset.labelShow || 'Show module menu';
            const hidden = readHidden(storageKey);

            btn = document.createElement('button');
            btn.type = 'button';
            btn.className =
                'fi-btn fi-size-md fi-ac-btn-action fi-btn-color-gray fi-color-gray fi-outlined st-cluster-subnav-toggle-btn';
            btn.setAttribute('title', hidden ? labelShow : labelHide);

            const icon = document.createElement('span');
            icon.className = 'fi-icon fi-size-md';
            icon.innerHTML =
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>';

            const label = document.createElement('span');
            label.className = 'fi-btn-label';
            label.textContent = hidden ? labelShow : labelHide;

            btn.appendChild(icon);
            btn.appendChild(label);

            btn.addEventListener('click', function () {
                const nextHidden = !document.documentElement.classList.contains(hiddenClass);
                applyHidden(nextHidden);
                writeHidden(storageKey, nextHidden);
                label.textContent = nextHidden ? labelShow : labelHide;
                btn.setAttribute('title', nextHidden ? labelShow : labelHide);
            });

            topbarEnd.insertBefore(btn, topbarEnd.firstChild);
        }

        function syncToggleVisibility() {
            const mount = document.getElementById(mountId);
            if (!mount) {
                return;
            }

            const show = isLargeScreen() && hasClusterSubNavigation();
            mount.classList.toggle('hidden', !show);

            const btn = document.querySelector('.st-cluster-subnav-toggle-btn');
            if (btn) {
                btn.classList.toggle('fi-hidden', !show);
                btn.classList.toggle('hidden', !show);
            }

            if (show) {
                ensureTopbarButton(mount);
                applyHidden(readHidden(mount.dataset.storageKey));
            } else {
                document.documentElement.classList.remove(hiddenClass);
            }
        }

        document.addEventListener('livewire:navigated', function () {
            window.requestAnimationFrame(function () {
                window.setTimeout(syncToggleVisibility, 0);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            window.setTimeout(syncToggleVisibility, 50);
        });

        if (document.readyState !== 'loading') {
            window.setTimeout(syncToggleVisibility, 50);
        }
    })();
</script>
