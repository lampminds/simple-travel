{{--
    Auto-translate name/description fields from one language to the others.
    Expects inputs: translations[{id}][name], translations[{id}][description] with data-language-id.
--}}
<script>
    (function () {
        const formRoot = document.querySelector(@json($formSelector ?? '#operator-package-form'));
        if (!formRoot) {
            return;
        }

        const translateRoute = @json($translateRoute);
        const emptySourceMessage = @json($emptySourceMessage ?? __('account.operator_packages.translate_empty_source'));
        const translateErrorTemplate = @json($translateErrorTemplate ?? __('account.operator_packages.translate_error', ['message' => '__MESSAGE__']));
        const defaultButtonMarkup = '<span aria-hidden="true">🌐</span>';

        const translateButtons = Array.from(formRoot.querySelectorAll('.translate-from-language-btn'));
        const descriptionFields = Array.from(formRoot.querySelectorAll('textarea[name^="translations"][name$="[description]"]'));
        const nameFields = Array.from(formRoot.querySelectorAll('input[name^="translations"][name$="[name]"]'));

        if (translateButtons.length === 0) {
            return;
        }

        function collectTranslationsPayload() {
            const payload = {};

            nameFields.forEach((field) => {
                const languageId = field.dataset.languageId;
                if (!payload[languageId]) {
                    payload[languageId] = {};
                }
                payload[languageId].name = field.value || '';
            });

            descriptionFields.forEach((field) => {
                const languageId = field.dataset.languageId;
                if (!payload[languageId]) {
                    payload[languageId] = {};
                }
                payload[languageId].description = field.value || '';
            });

            return payload;
        }

        async function translateFromLanguage(sourceLanguageId, triggerButton) {
            if (!sourceLanguageId) {
                return;
            }

            const translationsPayload = collectTranslationsPayload();
            const sourceData = translationsPayload[sourceLanguageId] || {};
            const sourceName = (sourceData.name || '').trim();
            const sourceDescription = (sourceData.description || '').trim();

            if (!sourceName && !sourceDescription) {
                alert(emptySourceMessage);
                return;
            }

            translateButtons.forEach((button) => button.disabled = true);
            if (triggerButton) {
                triggerButton.classList.add('disabled');
                triggerButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }

            try {
                const response = await fetch(translateRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        source_language_id: Number(sourceLanguageId),
                        translations: translationsPayload,
                    }),
                });

                if (!response.ok) {
                    let message = `HTTP ${response.status}`;
                    try {
                        const errorPayload = await response.json();
                        if (errorPayload.message) {
                            message = errorPayload.message;
                        } else if (errorPayload.errors) {
                            const firstError = Object.values(errorPayload.errors)[0];
                            if (Array.isArray(firstError) && firstError.length) {
                                message = firstError[0];
                            }
                        }
                    } catch (parseError) {
                        // Keep HTTP status message for non-JSON responses.
                    }
                    throw new Error(message);
                }

                const payload = await response.json();
                const translated = payload.translations || {};

                nameFields.forEach((field) => {
                    const langId = field.dataset.languageId;
                    if (translated[langId] && typeof translated[langId].name === 'string') {
                        field.value = translated[langId].name;
                    }
                });

                descriptionFields.forEach((field) => {
                    const langId = field.dataset.languageId;
                    if (translated[langId] && typeof translated[langId].description === 'string') {
                        field.value = translated[langId].description;
                    }
                });
            } catch (error) {
                const message = error instanceof Error ? error.message : String(error);
                alert(translateErrorTemplate.replace('__MESSAGE__', message));
            } finally {
                translateButtons.forEach((button) => button.disabled = false);
                if (triggerButton) {
                    triggerButton.classList.remove('disabled');
                    triggerButton.innerHTML = defaultButtonMarkup;
                }
            }
        }

        translateButtons.forEach((button) => {
            button.addEventListener('click', () => {
                translateFromLanguage(button.dataset.sourceLanguageId, button);
            });
        });
    })();
</script>
