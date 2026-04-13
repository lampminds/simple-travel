{{-- Inline SVG from feather-icons (toSvg) so toggle icons keep theme classes after dynamic updates. --}}
<script>
    window.renderPasswordToggleFeatherIcon = function (hostEl, passwordsVisible) {
        if (!hostEl || !window.feather || !window.feather.icons) {
            return;
        }
        var name = passwordsVisible ? 'eye-off' : 'eye';
        var def = window.feather.icons[name];
        if (!def) {
            return;
        }
        hostEl.innerHTML = def.toSvg({ class: 'feather icon icon-xs', width: 16, height: 16 });
    };
</script>
