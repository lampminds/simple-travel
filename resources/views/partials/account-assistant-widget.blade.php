@php
    $assistantUser = auth()->user();
    $assistantAccount = $assistantUser?->currentAccount();
@endphp

<div id="account-assistant-root" class="account-assistant-root" aria-live="polite">
    <button
        type="button"
        id="account-assistant-toggle"
        class="account-assistant-fab btn btn-primary shadow"
        aria-controls="account-assistant-panel"
        aria-expanded="false"
        aria-label="{{ __('account.assistant.open_button_label') }}"
        title="{{ __('account.assistant.widget_title') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </button>

    <div
        id="account-assistant-panel"
        class="account-assistant-panel card shadow-lg"
        role="dialog"
        aria-labelledby="account-assistant-panel-title"
        hidden
    >
        <div class="account-assistant-panel__header card-header d-flex align-items-center justify-content-between py-2 px-3">
            <strong id="account-assistant-panel-title" class="mb-0">{{ __('account.assistant.widget_title') }}</strong>
            <button
                type="button"
                id="account-assistant-close"
                class="btn btn-sm btn-link text-muted p-0"
                aria-label="{{ __('account.assistant.close_button_label') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div
            id="assistant-messages"
            class="account-assistant-panel__messages flex-grow-1 overflow-auto p-3"
            aria-relevant="additions"
        ></div>

        <div class="card-footer border-top p-3">
            <form id="assistant-form" class="d-flex gap-2 align-items-end" autocomplete="off">
                @csrf
                <div class="flex-grow-1">
                    <label for="assistant-input" class="form-label visually-hidden">{{ __('account.assistant.input_label') }}</label>
                    <textarea
                        id="assistant-input"
                        class="form-control form-control-sm"
                        rows="2"
                        maxlength="{{ (int) config('assistant.max_question_length', 2000) }}"
                        placeholder="{{ __('account.assistant.input_placeholder') }}"
                        required
                    ></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" id="assistant-submit">
                    {{ __('account.assistant.send_button') }}
                </button>
            </form>
            <p class="small text-muted mb-0 mt-2">{{ __('account.assistant.disclaimer') }}</p>
        </div>
    </div>
</div>

<style>
    .account-assistant-root {
        position: fixed;
        right: 1.25rem;
        bottom: 1.25rem;
        z-index: 1045;
    }

    .account-assistant-fab {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .account-assistant-panel {
        position: fixed;
        right: 1.25rem;
        bottom: 5rem;
        width: min(24rem, calc(100vw - 2.5rem));
        height: min(32rem, calc(100vh - 7rem));
        display: flex;
        flex-direction: column;
        border: 1px solid var(--bs-border-color);
    }

    .account-assistant-panel[hidden] {
        display: none !important;
    }

    .account-assistant-panel__messages {
        min-height: 0;
    }

    .assistant-bubble {
        max-width: 92%;
        padding: 0.65rem 0.85rem;
        border-radius: 0.65rem;
        margin-bottom: 0.75rem;
        white-space: pre-wrap;
        font-size: 0.925rem;
    }

    .assistant-bubble--user {
        margin-left: auto;
        background: var(--bs-primary);
        color: #fff;
    }

    .assistant-bubble--bot {
        background: var(--bs-light);
        border: 1px solid var(--bs-border-color);
    }

    .assistant-bubble--error {
        background: #fff5f5;
        border: 1px solid #f5c2c7;
        color: #842029;
    }

    .assistant-sources {
        font-size: 0.85rem;
        margin-top: 0.35rem;
    }

    .assistant-sources ul {
        margin-bottom: 0;
        padding-left: 1.1rem;
    }

    .assistant-suggestion {
        display: block;
        width: 100%;
        white-space: normal;
        line-height: 1.35;
    }

    @media (max-width: 575.98px) {
        .account-assistant-panel {
            right: 0.75rem;
            left: 0.75rem;
            width: auto;
            bottom: 4.75rem;
            height: min(70vh, calc(100vh - 6rem));
        }

        .account-assistant-root {
            right: 0.75rem;
            bottom: 0.75rem;
        }
    }
</style>

<script>
    window.accountAssistantConfig = {
        messageUrl: @json(route('account.assistant.message')),
        historyUrl: @json(route('account.assistant.history')),
        csrfToken: @json(csrf_token()),
        userId: @json((int) $assistantUser->id),
        accountId: @json($assistantAccount?->id),
        storageVersion: @json((int) config('assistant.history.storage_version', 1)),
        welcome: @json(__('account.assistant.welcome')),
        exampleQuestions: @json(config('assistant.example_questions', [])),
        strings: {
            thinking: @json(__('account.assistant.thinking')),
            errorGeneric: @json(__('account.assistant.error_generic')),
            sourcesHeading: @json(__('account.assistant.sources_heading')),
        },
    };
</script>
<script src="{{ asset('js/account-assistant.js') }}" defer></script>
