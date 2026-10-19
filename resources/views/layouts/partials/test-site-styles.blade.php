@php
    $isTestEnvironment = is_test_site();
    $environmentAccent = $isTestEnvironment ? '#c2410c' : '#1d4ed8';
    $environmentHeader = $isTestEnvironment ? '#c5ced6' : '#d5deea';
@endphp
@if (site_environment_label())
    <style>
        .site-environment-badge {
            display: inline-flex;
            align-items: center;
            margin-inline-start: 0.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: {{ $environmentAccent }};
            color: #fff;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1.2;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .navbar.topnav-menu,
        .fi-topbar-ctn,
        .fi-topbar {
            background-color: {{ $environmentHeader }} !important;
            box-shadow: inset 0 3px 0 {{ $environmentAccent }};
        }

        .fi-topbar-start .site-environment-badge {
            align-self: center;
        }
    </style>
    <script>
        (function () {
            var prefix = @json(site_environment_label().' · ');
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
