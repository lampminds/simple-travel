@if (is_test_site())
    <style>
        .test-site-badge {
            display: inline-flex;
            align-items: center;
            margin-inline-start: 0.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: #c2410c;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1.2;
            text-transform: uppercase;
            white-space: nowrap;
        }

        body.is-test-site .navbar.topnav-menu {
            background-color: #c5ced6 !important;
            box-shadow: inset 0 3px 0 #c2410c;
        }

        .fi-topbar-ctn,
        .fi-topbar {
            background-color: #c5ced6 !important;
            box-shadow: inset 0 3px 0 #c2410c;
        }

        .fi-topbar-start .test-site-badge {
            align-self: center;
        }
    </style>
    <script>
        (function () {
            var prefix = @json(__('nav.test_site_badge').' · ');
            var apply = function () {
                if (document.title && document.title.indexOf(prefix) !== 0) {
                    document.title = prefix + document.title;
                }
            };
            apply();
            var title = document.querySelector('title');
            if (!title) {
                return;
            }
            new MutationObserver(apply).observe(title, { childList: true, characterData: true, subtree: true });
        })();
    </script>
@endif
