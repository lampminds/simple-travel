<script>
    (function () {
        const itemConditionsUrl = @json($itemConditionsUrl);
        const languages = @json($languagesForConditions);
        const actionLabels = @json($actionLabels);
        const editItemIds = @json($editItemIds ?? []);
        const oldItems = @json(old('items', []));
        const labels = {
            itemHeading: @json(__('account.operator_packages.conditions.item_row_label')),
            inherited: @json(__('account.operator_packages.conditions.inherited_label')),
            fixed: @json(__('account.operator_packages.conditions.fixed_topic')),
            topic: @json(__('account.operator_packages.conditions.topic')),
            action: @json(__('account.operator_packages.conditions.action')),
            customText: @json(__('account.operator_packages.conditions.custom_text')),
            loading: @json(__('account.operator_packages.conditions.loading')),
            noOffer: @json(__('account.operator_packages.conditions.no_offer_selected')),
            noRows: @json(__('account.operator_packages.conditions.no_customizable_rows')),
        };

        let nextPackageConditionIndex = document.querySelectorAll('#package-conditions-list .package-condition-row').length;
        const itemConditionsCache = new Map();

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;');
        }

        function actionOptions(allowedActions, selected) {
            let html = `<option value="">${escapeHtml(actionLabels[''])}</option>`;
            for (const action of allowedActions) {
                const selectedAttr = String(selected) === String(action) ? ' selected' : '';
                html += `<option value="${escapeHtml(action)}"${selectedAttr}>${escapeHtml(actionLabels[action] ?? action)}</option>`;
            }
            return html;
        }

        function toggleTextBlock(selectEl) {
            const row = selectEl.closest('.package-condition-row, .item-condition-topic-row');
            if (!row) return;
            const block = row.querySelector('.package-condition-text-block, .item-condition-text-block');
            if (!block) return;
            const action = selectEl.value;
            const needsText = action === 'append_top' || action === 'append_bottom' || action === 'replace';
            block.classList.toggle('d-none', !needsText);
        }

        function bindPackageConditionRow(row) {
            const topicSelect = row.querySelector('.package-condition-topic');
            const actionSelect = row.querySelector('.package-condition-action');
            if (!topicSelect || !actionSelect) return;

            function syncAllowedActions() {
                const option = topicSelect.selectedOptions[0];
                const allowed = option?.dataset.allowedActions ? JSON.parse(option.dataset.allowedActions) : [];
                const current = actionSelect.value;
                actionSelect.innerHTML = '';
                for (const action of allowed) {
                    const selected = current === action ? ' selected' : '';
                    actionSelect.innerHTML += `<option value="${escapeHtml(action)}"${selected}>${escapeHtml(actionLabels[action] ?? action)}</option>`;
                }
                toggleTextBlock(actionSelect);
            }

            topicSelect.addEventListener('change', syncAllowedActions);
            actionSelect.addEventListener('change', () => toggleTextBlock(actionSelect));
            syncAllowedActions();
        }

        function reindexPackageConditionRows() {
            const rows = document.querySelectorAll('#package-conditions-list .package-condition-row');
            rows.forEach((row, index) => {
                row.querySelectorAll('[name^="package_conditions["]').forEach((input) => {
                    input.name = input.name.replace(/package_conditions\[\d+\]/, `package_conditions[${index}]`);
                });
            });
            nextPackageConditionIndex = rows.length;
        }

        document.querySelectorAll('#package-conditions-list .package-condition-row').forEach(bindPackageConditionRow);

        document.getElementById('package-add-condition')?.addEventListener('click', () => {
            const template = document.getElementById('package-condition-row-template');
            if (!template) return;
            const html = template.innerHTML.replaceAll('__PINDEX__', String(nextPackageConditionIndex));
            const list = document.getElementById('package-conditions-list');
            list.insertAdjacentHTML('beforeend', html);
            const row = list.lastElementChild;
            if (row) bindPackageConditionRow(row);
            reindexPackageConditionRows();
        });

        document.getElementById('package-conditions-list')?.addEventListener('click', (event) => {
            const btn = event.target.closest('.package-condition-remove');
            if (!btn) return;
            btn.closest('.package-condition-row')?.remove();
            reindexPackageConditionRows();
        });

        function inheritedPreview(row, langId) {
            const inherited = row.inherited_by_language || {};
            return inherited[langId] ?? inherited[String(langId)] ?? '';
        }

        function savedOverride(oldItem, topicId) {
            const overrides = oldItem?.condition_overrides || {};
            return overrides[topicId] ?? overrides[String(topicId)] ?? null;
        }

        function renderItemConditionTopic(itemIndex, row, oldItem) {
            const topicId = row.topic_id;
            const saved = savedOverride(oldItem, topicId) || {
                action: row.saved_action || '',
                translations: row.saved_translations || {},
            };
            const allowedActions = row.allowed_actions || [];
            const canCustomize = !!row.can_customize;

            let inheritedHtml = '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-2 mb-3">';
            for (const lang of languages) {
                const text = inheritedPreview(row, lang.id);
                inheritedHtml += `
                    <div class="col">
                        <div class="small text-muted">${escapeHtml(lang.display_name)}</div>
                        <div class="border rounded p-2 bg-light small">${text ? escapeHtml(text) : '—'}</div>
                    </div>`;
            }
            inheritedHtml += '</div>';

            let customHtml = '';
            if (canCustomize) {
                customHtml = `
                    <div class="col-md-4">
                        <label class="form-label">${labels.action}</label>
                        <select
                            name="items[${itemIndex}][condition_overrides][${topicId}][action]"
                            class="form-select item-condition-action"
                        >${actionOptions(allowedActions, saved.action || '')}</select>
                    </div>
                    <div class="col-12 item-condition-text-block">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                `;
                for (const lang of languages) {
                    const value = saved.translations?.[lang.id] ?? saved.translations?.[String(lang.id)] ?? '';
                    customHtml += `
                        <div class="col">
                            <label class="form-label">${escapeHtml(lang.display_name)}</label>
                            <textarea
                                name="items[${itemIndex}][condition_overrides][${topicId}][translations][${lang.id}]"
                                class="form-control item-condition-custom-text"
                                rows="3"
                            >${escapeHtml(value)}</textarea>
                        </div>`;
                }
                customHtml += '</div></div>';
            } else {
                customHtml = `<p class="col-12 text-muted small mb-0">${labels.fixed}</p>`;
            }

            return `
                <div class="border rounded p-3 item-condition-topic-row" data-topic-id="${topicId}">
                    <div class="mb-2">
                        <strong>${escapeHtml(row.category_name)} · ${escapeHtml(row.topic_name)}</strong>
                    </div>
                    <p class="small text-muted mb-2">${labels.inherited}</p>
                    ${inheritedHtml}
                    <div class="row g-3">${customHtml}</div>
                </div>`;
        }

        async function fetchItemRows(itemIndex, offerId, packageItemId) {
            const cacheKey = `${itemIndex}:${offerId}:${packageItemId || ''}`;
            if (itemConditionsCache.has(cacheKey)) {
                return itemConditionsCache.get(cacheKey);
            }

            const params = new URLSearchParams({ service_offer_id: String(offerId) });
            if (packageItemId) {
                params.set('package_item_id', String(packageItemId));
            }

            const response = await fetch(`${itemConditionsUrl}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) {
                throw new Error('fetch failed');
            }
            const payload = await response.json();
            itemConditionsCache.set(cacheKey, payload.rows || []);
            return payload.rows || [];
        }

        async function rebuildItemConditions() {
            const container = document.getElementById('package-item-conditions');
            const emptyState = document.getElementById('package-item-conditions-empty');
            if (!container) return;

            const itemRows = document.querySelectorAll('#package-items-body tr.package-item-row');
            container.querySelectorAll('.package-item-condition-card').forEach((node) => node.remove());

            if (itemRows.length === 0) {
                if (emptyState) emptyState.classList.remove('d-none');
                return;
            }

            let rendered = 0;

            for (let index = 0; index < itemRows.length; index++) {
                const row = itemRows[index];
                const offerSelect = row.querySelector('.package-item-offer');
                const offerId = offerSelect?.value || '';
                const offerLabel = offerSelect?.selectedOptions[0]?.textContent?.trim() || labels.itemHeading;
                const oldItem = oldItems[index] || null;
                const packageItemId = row.querySelector('.package-item-id')?.value || editItemIds[index] || null;

                const card = document.createElement('div');
                card.className = 'border rounded p-3 package-item-condition-card';
                card.dataset.itemIndex = String(index);

                if (!offerId) {
                    card.innerHTML = `
                        <h6 class="h6 mb-2">${escapeHtml(labels.itemHeading)} #${index + 1}</h6>
                        <p class="text-muted small mb-0">${escapeHtml(labels.noOffer)}</p>`;
                    container.appendChild(card);
                    rendered++;
                    continue;
                }

                card.innerHTML = `
                    <h6 class="h6 mb-3">${escapeHtml(labels.itemHeading)} #${index + 1} — ${escapeHtml(offerLabel)}</h6>
                    <p class="text-muted small">${escapeHtml(labels.loading)}</p>`;
                container.appendChild(card);

                try {
                    const rows = await fetchItemRows(index, offerId, packageItemId);
                    if (rows.length === 0) {
                        card.innerHTML = `
                            <h6 class="h6 mb-2">${escapeHtml(labels.itemHeading)} #${index + 1} — ${escapeHtml(offerLabel)}</h6>
                            <p class="text-muted small mb-0">${escapeHtml(labels.noRows)}</p>`;
                    } else {
                        card.innerHTML = `<h6 class="h6 mb-3">${escapeHtml(labels.itemHeading)} #${index + 1} — ${escapeHtml(offerLabel)}</h6>`;
                        card.innerHTML += rows.map((topicRow) => renderItemConditionTopic(index, topicRow, oldItem)).join('');
                        card.querySelectorAll('.item-condition-action').forEach((selectEl) => {
                            selectEl.addEventListener('change', () => toggleTextBlock(selectEl));
                            toggleTextBlock(selectEl);
                        });
                    }
                } catch (error) {
                    card.innerHTML = `
                        <h6 class="h6 mb-2">${escapeHtml(labels.itemHeading)} #${index + 1}</h6>
                        <p class="text-danger small mb-0">${escapeHtml(labels.loading)}</p>`;
                }

                rendered++;
            }

            if (emptyState) {
                emptyState.classList.toggle('d-none', rendered > 0);
            }
        }

        function invalidateItemConditionsCache() {
            itemConditionsCache.clear();
        }

        document.querySelector('a[href="#package-tab-conditions"]')?.addEventListener('shown.bs.tab', rebuildItemConditions);

        if (document.querySelector('#package-tab-conditions.show')) {
            rebuildItemConditions();
        }

        document.getElementById('package-items-body')?.addEventListener('change', (event) => {
            if (event.target.classList.contains('package-item-offer') || event.target.classList.contains('package-item-provider')) {
                invalidateItemConditionsCache();
                const conditionsTab = document.querySelector('#package-tab-conditions.show');
                if (conditionsTab) {
                    rebuildItemConditions();
                }
            }
        });

        window.packageConditionsReindexItems = function () {
            invalidateItemConditionsCache();
            const conditionsTab = document.querySelector('#package-tab-conditions.show');
            if (conditionsTab) {
                rebuildItemConditions();
            }
        };
    })();
</script>
