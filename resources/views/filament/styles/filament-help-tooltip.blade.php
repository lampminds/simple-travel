{{-- Styles for resource list help tooltips (see ListServiceTypes, lang/*/filament_help.php) --}}
<style>
    .lmp-filament-help-tooltip {
        max-width: 22rem;
        background-color: rgb(31 41 55);
        color: rgb(229 231 235);
        border-radius: 0.5rem;
        padding: 1.125rem 1.375rem;
        line-height: 1.5;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.25), 0 4px 6px -4px rgb(0 0 0 / 0.2);
    }

    .lmp-filament-help-tooltip__title {
        font-size: 0.9375rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        color: rgb(249 250 251);
        margin: 0 0 0.875rem 0;
        padding-bottom: 0.625rem;
        border-bottom: 1px solid rgb(75 85 99);
        line-height: 1.35;
    }

    .lmp-filament-help-tooltip__body {
        font-size: 0.8125rem;
        color: rgb(229 231 235);
        line-height: 1.55;
    }

    .lmp-filament-help-tooltip__body > :first-child {
        margin-top: 0;
    }

    .lmp-filament-help-tooltip__body > :last-child {
        margin-bottom: 0;
    }

    .lmp-filament-help-tooltip__body p {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .lmp-filament-help-tooltip__body code {
        font-size: 0.75rem;
        color: rgb(251 191 36);
        word-break: break-word;
    }

    /* Outer tooltip shell (Tippy) when present */
    .tippy-box:has(.lmp-filament-help-tooltip) {
        background-color: rgb(31 41 55);
        color: rgb(229 231 235);
        border: 1px solid rgb(55 65 81);
    }

    .tippy-box:has(.lmp-filament-help-tooltip) .tippy-content {
        padding: 0;
    }

    .tippy-box:has(.lmp-filament-help-tooltip) .tippy-arrow {
        color: rgb(31 41 55);
    }
</style>
