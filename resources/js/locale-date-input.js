import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Spanish } from 'flatpickr/dist/l10n/es.js';
import { Portuguese } from 'flatpickr/dist/l10n/pt.js';

/**
 * Locale-aware date inputs with Flatpickr (d/m/Y for es/pt, m/d/Y for en).
 * Visible field + calendar picker; hidden field submits ISO Y-m-d.
 */
(function () {
    function resolveFlatpickrLocale() {
        const lang = (document.documentElement.lang || 'es').split('-')[0].toLowerCase();

        if (lang === 'es') {
            return Spanish;
        }

        if (lang === 'pt') {
            return Portuguese;
        }

        return flatpickr.l10ns.default;
    }

    function dateFormatForPattern(pattern) {
        return pattern === 'mdy' ? 'm/d/Y' : 'd/m/Y';
    }

    function parseDisplayValue(raw, pattern) {
        const value = (raw || '').trim();
        if (value === '') {
            return '';
        }

        const format = dateFormatForPattern(pattern);
        const parsed = flatpickr.parseDate(value, format);

        if (!parsed) {
            return null;
        }

        return flatpickr.formatDate(parsed, 'Y-m-d');
    }

    function parseIsoDate(iso) {
        const value = (iso || '').trim();
        if (value === '') {
            return undefined;
        }

        const parsed = flatpickr.parseDate(value, 'Y-m-d');

        return parsed || undefined;
    }

    function formatIsoToDisplay(iso, pattern) {
        const value = (iso || '').trim();
        if (value === '') {
            return '';
        }

        const parsed = flatpickr.parseDate(value, 'Y-m-d');
        if (!parsed) {
            return '';
        }

        return flatpickr.formatDate(parsed, dateFormatForPattern(pattern));
    }

    function syncWrap(wrap, reportInvalid) {
        const display = wrap.querySelector('.js-locale-date-display');
        const iso = wrap.querySelector('.js-locale-date-iso');
        if (!(display instanceof HTMLInputElement) || !(iso instanceof HTMLInputElement)) {
            return true;
        }

        const pattern = wrap.getAttribute('data-date-pattern') || 'dmy';
        const picker = wrap._flatpickr;

        if (display.value.trim() === '') {
            iso.value = '';
            display.setCustomValidity('');

            return true;
        }

        let isoValue = null;

        if (picker && picker.selectedDates.length > 0) {
            isoValue = flatpickr.formatDate(picker.selectedDates[0], 'Y-m-d');
        } else {
            isoValue = parseDisplayValue(display.value, pattern);
        }

        if (isoValue === null) {
            if (reportInvalid) {
                display.setCustomValidity(
                    (window.__html5ValidationMessages && window.__html5ValidationMessages.date_invalid)
                    || 'Invalid date. Use the placeholder format.'
                );
                display.reportValidity();
            }

            return false;
        }

        iso.value = isoValue;
        display.setCustomValidity('');

        return true;
    }

    function initWrap(wrap) {
        if (!(wrap instanceof HTMLElement) || wrap.getAttribute('data-locale-date-bound') === '1') {
            return;
        }

        wrap.setAttribute('data-locale-date-bound', '1');

        const display = wrap.querySelector('.js-locale-date-display');
        const iso = wrap.querySelector('.js-locale-date-iso');
        if (!(display instanceof HTMLInputElement) || !(iso instanceof HTMLInputElement)) {
            return;
        }

        const pattern = wrap.getAttribute('data-date-pattern') || 'dmy';
        const dateFormat = dateFormatForPattern(pattern);
        const defaultDate = parseIsoDate(iso.value);

        const picker = flatpickr(display, {
            locale: resolveFlatpickrLocale(),
            dateFormat,
            allowInput: true,
            disableMobile: true,
            defaultDate,
            onChange(selectedDates) {
                if (selectedDates.length > 0) {
                    iso.value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                    display.value = flatpickr.formatDate(selectedDates[0], dateFormat);
                } else {
                    iso.value = '';
                }
                display.setCustomValidity('');
            },
            onClose() {
                syncWrap(wrap, false);
            },
        });

        wrap._flatpickr = picker;

        if (defaultDate) {
            iso.value = flatpickr.formatDate(defaultDate, 'Y-m-d');
            display.value = flatpickr.formatDate(defaultDate, dateFormat);
        }

        display.addEventListener('input', function () {
            display.setCustomValidity('');
        });
    }

    function initAll(root) {
        const scope = root instanceof HTMLElement ? root : document;
        scope.querySelectorAll('[data-locale-date-wrap]').forEach(initWrap);
    }

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        let valid = true;
        form.querySelectorAll('[data-locale-date-wrap]').forEach(function (wrap) {
            if (!syncWrap(wrap, valid)) {
                valid = false;
            }
        });

        if (!valid) {
            event.preventDefault();
            event.stopPropagation();
        }
    }, true);

    window.LocaleDateInput = {
        init: initWrap,
        initAll: initAll,
        parseDisplayValue: parseDisplayValue,
        formatIsoToDisplay: formatIsoToDisplay,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initAll(document);
        });
    } else {
        initAll(document);
    }
})();
