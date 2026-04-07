<style>
    :root {
        --err-orange: #f97316;
        --err-navy: #1e3a5f;
        --err-muted: #6b7280;
        --err-bg: #f3f4f6;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        min-height: 100vh;
        font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: var(--err-bg);
        color: var(--err-navy);
    }
    .err-robot {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1.5rem 1rem 2rem;
    }
    .err-robot__stage {
        width: 100%;
        max-width: 42rem;
        text-align: center;
    }
    .err-robot__code {
        margin: 0 0 0.25rem;
        font-size: clamp(3.5rem, 12vw, 5rem);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.02em;
        color: var(--err-orange);
    }
    .err-robot__title {
        margin: 0 0 0.75rem;
        font-size: clamp(1.5rem, 4vw, 2rem);
        font-weight: 700;
        color: var(--err-navy);
    }
    .err-robot__desc {
        margin: 0 auto 1.25rem;
        max-width: 26rem;
        font-size: 1rem;
        line-height: 1.5;
        color: var(--err-muted);
    }
    .err-robot__visual {
        position: relative;
        width: 100%;
        max-width: 36rem;
        margin: 0 auto;
    }
    .err-robot__visual img {
        display: block;
        width: 100%;
        height: auto;
        vertical-align: middle;
    }
    .err-robot__cta-wrap {
        position: absolute;
        left: 50%;
        bottom: 6%;
        transform: translateX(-50%);
        z-index: 1;
    }
    .err-robot__cta {
        display: inline-block;
        padding: 0.75rem 1.75rem;
        font-size: 1rem;
        font-weight: 700;
        color: #fff !important;
        text-decoration: none;
        background: var(--err-orange);
        border-radius: 9999px;
        box-shadow: 0 4px 14px rgba(249, 115, 22, 0.35);
        transition: background 0.15s ease, transform 0.15s ease;
        white-space: nowrap;
    }
    .err-robot__cta:hover {
        background: #ea580c;
        transform: translateY(-1px);
    }
    @media (max-width: 480px) {
        .err-robot__cta-wrap { bottom: 5%; }
        .err-robot__cta { padding: 0.65rem 1.35rem; font-size: 0.9375rem; }
    }
</style>
