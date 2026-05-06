{{-- Registers copy helper for Filament (HTTP dev: execCommand fallback; HTTPS: Clipboard API). Loaded once per panel via render hook. --}}
<script>
    (function () {
        if (window.simpleTravelCopyTextToClipboard) {
            return;
        }

        /**
         * @param {string} text
         * @returns {Promise<void>}
         */
        window.simpleTravelCopyTextToClipboard = async function (text) {
            if (text == null || text === '') {
                throw new Error('empty');
            }

            if (window.isSecureContext && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                await navigator.clipboard.writeText(text);

                return;
            }

            const ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            ta.style.top = '0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();

            const ok = document.execCommand('copy');

            document.body.removeChild(ta);

            if (!ok) {
                throw new Error('copy');
            }
        };
    })();
</script>
