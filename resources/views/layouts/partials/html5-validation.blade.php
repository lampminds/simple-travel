@php
    /** @var array<string, string> $__html5 */
    $__html5 = __('forms.html5');
    if (! is_array($__html5)) {
        $__html5 = [];
    }
@endphp
<script>
(function () {
    window.__html5ValidationMessages = @json($__html5);

    function clearCustomValidity(el) {
        if (el && typeof el.setCustomValidity === 'function') {
            el.setCustomValidity('');
        }
    }

    function resolveMessage(t, msgs) {
        if (!t || !t.validity || !msgs) {
            return '';
        }
        const v = t.validity;
        if (v.valueMissing) {
            return msgs.value_missing || '';
        }
        if (v.typeMismatch) {
            if (t.type === 'email') {
                return msgs.type_mismatch_email || msgs.generic || '';
            }
            if (t.type === 'url') {
                return msgs.type_mismatch_url || msgs.generic || '';
            }
            return msgs.generic || '';
        }
        if (v.patternMismatch) {
            return msgs.pattern_mismatch || msgs.generic || '';
        }
        if (v.tooShort) {
            return msgs.too_short || msgs.generic || '';
        }
        if (v.tooLong) {
            return msgs.too_long || msgs.generic || '';
        }
        if (v.rangeUnderflow) {
            return msgs.range_underflow || msgs.generic || '';
        }
        if (v.rangeOverflow) {
            return msgs.range_overflow || msgs.generic || '';
        }
        if (v.stepMismatch) {
            return msgs.step_mismatch || msgs.generic || '';
        }
        if (v.badInput) {
            return msgs.bad_input || msgs.generic || '';
        }
        if (!v.valid) {
            return msgs.generic || '';
        }
        return '';
    }

    document.addEventListener('invalid', function (e) {
        const t = e.target;
        if (!(t instanceof HTMLInputElement || t instanceof HTMLSelectElement || t instanceof HTMLTextAreaElement)) {
            return;
        }
        if (!t.willValidate) {
            return;
        }
        const msgs = window.__html5ValidationMessages;
        if (!msgs) {
            return;
        }
        const msg = resolveMessage(t, msgs);
        if (msg !== '') {
            t.setCustomValidity(msg);
        }
    }, true);

    document.addEventListener('input', function (e) {
        const t = e.target;
        if (t instanceof HTMLInputElement || t instanceof HTMLTextAreaElement) {
            clearCustomValidity(t);
        }
    }, true);

    document.addEventListener('change', function (e) {
        const t = e.target;
        if (t instanceof HTMLInputElement || t instanceof HTMLSelectElement || t instanceof HTMLTextAreaElement) {
            clearCustomValidity(t);
        }
    }, true);
})();
</script>
