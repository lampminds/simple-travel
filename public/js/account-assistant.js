(function () {
    const config = window.accountAssistantConfig;
    if (!config) {
        return;
    }

    const root = document.getElementById('account-assistant-root');
    const toggleBtn = document.getElementById('account-assistant-toggle');
    const closeBtn = document.getElementById('account-assistant-close');
    const panel = document.getElementById('account-assistant-panel');
    const form = document.getElementById('assistant-form');
    const input = document.getElementById('assistant-input');
    const messagesEl = document.getElementById('assistant-messages');
    const submitBtn = document.getElementById('assistant-submit');

    if (!root || !toggleBtn || !panel || !form || !input || !messagesEl || !submitBtn) {
        return;
    }

    const storageKey = [
        'accountAssistant',
        'v' + config.storageVersion,
        'user' + config.userId,
        'account' + (config.accountId ?? 0),
    ].join(':');

    /** @type {{ open: boolean, messages: Array<{role: string, content: string, sources?: Array<{title?: string, url?: string}>}>, history: Array<{role: string, content: string}> }} */
    let state = loadState();

    /** @type {boolean} */
    let historyLoaded = state.messages.length > 0;

    init();

    function init() {
        bindEvents();
        renderMessages();
        applyOpenState(state.open);

        if (!historyLoaded) {
            loadServerHistory();
        }
    }

    function bindEvents() {
        toggleBtn.addEventListener('click', function () {
            setOpen(!state.open);
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                setOpen(false);
            });
        }

        form.addEventListener('submit', onSubmit);

        messagesEl.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const button = target.closest('.assistant-suggestion');
            if (!button || !messagesEl.contains(button)) {
                return;
            }

            const question = button.getAttribute('data-question') || button.textContent || '';
            if (question.trim() === '') {
                return;
            }

            input.value = question.trim();
            setOpen(true);
            form.requestSubmit();
        });
    }

    function loadState() {
        try {
            const raw = sessionStorage.getItem(storageKey);
            if (!raw) {
                return defaultState();
            }

            const parsed = JSON.parse(raw);
            if (!parsed || typeof parsed !== 'object' || !Array.isArray(parsed.messages)) {
                return defaultState();
            }

            return {
                open: Boolean(parsed.open),
                messages: parsed.messages,
                history: Array.isArray(parsed.history) ? parsed.history : buildApiHistory(parsed.messages),
            };
        } catch (error) {
            return defaultState();
        }
    }

    function defaultState() {
        return {
            open: false,
            messages: [],
            history: [],
        };
    }

    function saveState() {
        sessionStorage.setItem(storageKey, JSON.stringify(state));
    }

    function setOpen(open) {
        state.open = open;
        applyOpenState(open);
        saveState();

        if (open) {
            window.requestAnimationFrame(function () {
                input.focus();
            });
        }
    }

    function applyOpenState(open) {
        panel.hidden = !open;
        toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    async function loadServerHistory() {
        try {
            const response = await fetch(config.historyUrl, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !Array.isArray(data.messages) || data.messages.length === 0) {
                historyLoaded = true;
                renderMessages();
                return;
            }

            if (state.messages.length === 0) {
                state.messages = data.messages;
                state.history = buildApiHistory(data.messages);
                saveState();
                renderMessages();
            }

            historyLoaded = true;
        } catch (error) {
            historyLoaded = true;
            renderMessages();
        }
    }

    async function onSubmit(event) {
        event.preventDefault();

        const text = input.value.trim();
        if (!text) {
            return;
        }

        removeSuggestions();
        appendMessage('user', text);
        input.value = '';
        setLoading(true);

        const thinkingNode = appendMessage('bot', config.strings.thinking, true);

        try {
            const response = await fetch(config.messageUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message: text,
                    history: state.history.slice(-6),
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (thinkingNode && thinkingNode.parentNode) {
                thinkingNode.parentNode.removeChild(thinkingNode);
            }

            if (!response.ok) {
                appendMessage('error', data.message || config.strings.errorGeneric);
                return;
            }

            const answer = typeof data.answer === 'string' ? data.answer : '';
            appendMessage('bot', answer, false, data.sources || []);

            state.history.push({ role: 'user', content: text });
            state.history.push({ role: 'assistant', content: answer });
            saveState();
        } catch (error) {
            if (thinkingNode && thinkingNode.parentNode) {
                thinkingNode.parentNode.removeChild(thinkingNode);
            }
            appendMessage('error', config.strings.errorGeneric);
        } finally {
            setLoading(false);
            if (state.open) {
                input.focus();
            }
        }
    }

    function renderMessages() {
        messagesEl.innerHTML = '';

        if (state.messages.length === 0) {
            appendMessage('bot', config.welcome, false, [], false);
            renderSuggestions();
            return;
        }

        state.messages.forEach(function (message) {
            const type = message.role === 'user' ? 'user' : 'bot';
            appendMessage(type, message.content, false, message.sources || [], false);
        });
    }

    /**
     * @param {'user'|'bot'|'error'} type
     * @param {Array<{title?: string, url?: string}>} sources
     */
    function appendMessage(type, text, isTemporary, sources, persist) {
        if (persist !== false && !isTemporary && type !== 'error') {
            state.messages.push({
                role: type === 'user' ? 'user' : 'assistant',
                content: text,
                sources: Array.isArray(sources) ? sources : [],
            });
            saveState();
        }

        const div = document.createElement('div');
        div.className = 'assistant-bubble assistant-bubble--' + type;
        if (isTemporary) {
            div.dataset.temporary = '1';
        }
        div.textContent = text;

        if (type === 'bot' && Array.isArray(sources) && sources.length > 0) {
            const sourcesWrap = document.createElement('div');
            sourcesWrap.className = 'assistant-sources text-muted';
            const heading = document.createElement('strong');
            heading.textContent = config.strings.sourcesHeading;
            sourcesWrap.appendChild(heading);

            const list = document.createElement('ul');
            sources.forEach(function (source) {
                const li = document.createElement('li');
                if (source.url) {
                    const link = document.createElement('a');
                    link.href = source.url;
                    link.textContent = source.title || source.url;
                    li.appendChild(link);
                } else {
                    li.textContent = source.title || '';
                }
                list.appendChild(li);
            });
            sourcesWrap.appendChild(list);
            div.appendChild(sourcesWrap);
        }

        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;

        return div;
    }

    /**
     * @param {Array<{role: string, content: string}>} messages
     * @returns {Array<{role: string, content: string}>}
     */
    function buildApiHistory(messages) {
        const history = [];

        messages.forEach(function (message) {
            const role = message.role === 'assistant' ? 'assistant' : 'user';
            const content = typeof message.content === 'string' ? message.content.trim() : '';
            if (content === '') {
                return;
            }
            history.push({ role: role, content: content });
        });

        return history;
    }

    function setLoading(loading) {
        submitBtn.disabled = loading;
        input.disabled = loading;
    }

    function removeSuggestions() {
        messagesEl.querySelectorAll('.assistant-suggestion').forEach(function (element) {
            element.remove();
        });
    }

    function renderSuggestions() {
        const examples = Array.isArray(config.exampleQuestions) ? config.exampleQuestions : [];
        examples.forEach(function (question) {
            if (typeof question !== 'string' || question.trim() === '') {
                return;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'assistant-suggestion btn btn-sm btn-outline-secondary text-start mb-2';
            button.dataset.question = question.trim();
            button.textContent = question.trim();
            messagesEl.appendChild(button);
        });
    }
})();
