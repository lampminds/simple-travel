/**
 * Public pricing page: account type filter, user count, module selection, estimate breakdown.
 */
export function initPricingPage(config) {
    const root = document.querySelector('[data-pricing-page]');
    if (!root || !config) {
        return;
    }

    const labels = config.labels || {};
    const state = {
        accountTypeCode: config.defaultAccountTypeCode,
        userCount: Math.max(1, parseInt(config.defaultUserCount, 10) || 1),
        currencyId: parseInt(config.defaultCurrencyId, 10) || null,
        selectedAddonIds: new Set(),
    };

    const els = {
        accountTypeButtons: root.querySelectorAll('[data-account-type]'),
        userCountInput: root.querySelector('[data-user-count-input]'),
        userPresetButtons: root.querySelectorAll('[data-user-preset]'),
        currencySelect: root.querySelector('[data-currency-select]'),
        exchangeRateNote: root.querySelector('[data-exchange-rate-note]'),
        configSummaryRole: root.querySelector('[data-config-summary-role]'),
        configSummaryUsers: root.querySelector('[data-config-summary-users]'),
        configSummaryCurrency: root.querySelector('[data-config-summary-currency]'),
        coreSection: root.querySelector('[data-core-section]'),
        coreCard: root.querySelector('[data-core-card]'),
        addonsGrid: root.querySelector('[data-addons-grid]'),
        addonsEmpty: root.querySelector('[data-addons-empty]'),
        mobileBreakdownTotal: root.querySelector('[data-mobile-breakdown-total]'),
        mobileBreakdownContext: root.querySelector('[data-mobile-breakdown-context]'),
    };

    const breakdownPanels = ['', '-mobile'].map((suffix) => ({
        suffix,
        empty: root.querySelector(`[data-breakdown-empty${suffix}]`),
        content: root.querySelector(`[data-breakdown-content${suffix}]`),
        lines: root.querySelector(`[data-breakdown-lines${suffix}]`),
        total: root.querySelector(`[data-breakdown-total${suffix}]`),
        context: root.querySelector(`[data-breakdown-context${suffix}]`),
    }));

    function moduleAppliesToAccountType(module) {
        const codes = module.accountTypeCodes || [];
        if (codes.length === 0) {
            return true;
        }
        return codes.includes(state.accountTypeCode);
    }

    function visibleModules() {
        return (config.modules || []).filter(moduleAppliesToAccountType);
    }

    function findTierForUserCount(price, userCount) {
        for (const tier of price.tiers || []) {
            const from = tier.fromUsers ?? 1;
            const to = tier.toUsers;
            if (userCount >= from && (to === null || to === undefined || userCount <= to)) {
                return tier;
            }
        }
        return null;
    }

    function resolvePerUserRate(price, userCount) {
        const tier = findTierForUserCount(price, userCount);
        const perUser = tier?.pricePerUser ?? price.pricePerUser;
        return perUser !== null && perUser !== undefined ? Number(perUser) : null;
    }

    function moduleBasePrice(module) {
        if (module.basePrice != null) {
            return Number(module.basePrice);
        }
        if (module.base_price != null) {
            return Number(module.base_price);
        }

        return 0;
    }

    function moduleBasePriceUsd(module) {
        if (module.basePrice == null && module.base_price == null) {
            return null;
        }

        const base = moduleBasePrice(module);

        return base > 0 ? base : null;
    }

    function monthlyAmountFromServer(module, userCount) {
        const amounts = module.amountsByUsers;
        if (!amounts) {
            return null;
        }

        const key = String(userCount);
        if (amounts[key] != null) {
            return Number(amounts[key]);
        }
        if (amounts[userCount] != null) {
            return Number(amounts[userCount]);
        }

        return null;
    }

    function monthlyAmount(module, userCount) {
        const users = Math.max(1, userCount);
        const serverAmount = monthlyAmountFromServer(module, users);
        if (serverAmount !== null) {
            return serverAmount;
        }

        const price = {
            billingType: module.billingType,
            basePrice: moduleBasePrice(module),
            includedUsers: module.includedUsers,
            pricePerUser: module.pricePerUser,
            tiers: module.tiers || [],
        };

        if (!price.billingType) {
            return null;
        }

        if (price.billingType === 'fixed' || price.billingType === 'usage') {
            return module.basePrice != null || module.base_price != null ? price.basePrice : null;
        }

        if (price.billingType === 'per_user') {
            const rate = resolvePerUserRate(price, users);
            const base = moduleBasePrice(module);

            if (rate === null && module.basePrice == null && module.base_price == null) {
                return null;
            }

            if (rate === null) {
                return module.basePrice != null || module.base_price != null ? base : null;
            }

            return base + rate * users;
        }

        if (price.billingType === 'hybrid') {
            const base = Number(price.basePrice ?? 0);
            const includedUsers = Number(price.includedUsers ?? 0);
            const extraUsers = Math.max(0, users - includedUsers);

            if (extraUsers === 0) {
                return price.basePrice !== null ? base : null;
            }

            const rate = resolvePerUserRate(price, users);
            if (rate === null) {
                return price.basePrice !== null ? base : null;
            }

            return base + extraUsers * rate;
        }

        return null;
    }

    function requiresCustomQuote(module, userCount) {
        return monthlyAmount(module, userCount) === null;
    }

    function getSelectedCurrency() {
        const currencies = config.currencies || [];
        return currencies.find((currency) => currency.id === state.currencyId) || currencies[0] || null;
    }

    function convertFromUsd(usdAmount) {
        if (usdAmount === null || usdAmount === undefined) {
            return null;
        }

        const currency = getSelectedCurrency();
        if (!currency || !currency.unitsPerUsd) {
            return null;
        }

        return usdAmount * Number(currency.unitsPerUsd);
    }

    function formatAmount(amount) {
        if (amount === null || amount === undefined) {
            return labels.customQuote || 'Custom quote';
        }

        const locale = document.documentElement.lang?.replace('_', '-') || 'en';
        return new Intl.NumberFormat(locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);
    }

    function formatPriceDisplay(usdAmount) {
        if (usdAmount === null || usdAmount === undefined) {
            return labels.customQuote || 'Custom quote';
        }

        const currency = getSelectedCurrency();
        const converted = convertFromUsd(usdAmount);
        if (converted === null) {
            return labels.customQuote || 'Custom quote';
        }

        const symbol = currency?.symbol || labels.currency || '$';
        return `${symbol}${formatAmount(converted)}`;
    }

    function updateExchangeRateNote() {
        if (!els.exchangeRateNote) {
            return;
        }

        const currency = getSelectedCurrency();
        if (!currency || currency.isUsd) {
            els.exchangeRateNote.textContent = labels.pricesUsdNote || '';
            return;
        }

        const template = labels.exchangeRateNote || '';
        els.exchangeRateNote.textContent = template
            .replace(':rate', formatAmount(Number(currency.unitsPerUsd)))
            .replace(':code', currency.code || '')
            .replace(':date', currency.rateDateLabel || currency.rateDate || '');
    }

    function billingBadge(module, userCount) {
        switch (module.billingType) {
            case 'fixed':
                return labels.billingFixed;
            case 'per_user': {
                const rate = resolvePerUserRate(
                    {
                        pricePerUser: module.pricePerUser,
                        tiers: module.tiers || [],
                    },
                    userCount,
                );
                if (rate === null) {
                    return labels.billingPerUser || '';
                }
                const rateLabel = formatPriceDisplay(rate);
                const baseUsd = moduleBasePriceUsd(module);
                if (baseUsd !== null) {
                    const template = labels.billingPerUserBaseAndAmount || ':base + :amount';
                    return template
                        .replace(':base', formatPriceDisplay(baseUsd))
                        .replace(':amount', rateLabel);
                }
                const template = labels.billingPerUserAmount || ':amount';
                return template.replace(':amount', rateLabel);
            }
            case 'hybrid':
                return labels.billingHybrid;
            case 'usage':
                return labels.billingUsage;
            default:
                return '';
        }
    }

    function getCoreModule() {
        const visible = visibleModules();
        return visible.find((module) => module.isCore) || null;
    }

    function getAddonModules() {
        return visibleModules().filter((module) => !module.isCore);
    }

    function renderCore() {
        const core = getCoreModule();
        if (!core || !els.coreSection || !els.coreCard) {
            if (els.coreSection) {
                els.coreSection.classList.add('d-none');
            }
            return;
        }

        els.coreSection.classList.remove('d-none');
        const amount = monthlyAmount(core, state.userCount);
        const featuresHtml = (core.features || [])
            .map((text) => `<li class="py-2 d-flex align-items-center"><i class="align-middle icon-dual-success me-2 icon-xs flex-shrink-0" data-feather="check"></i><span>${escapeHtml(text)}</span></li>`)
            .join('');

        els.coreCard.innerHTML = `
            <div class="card border-primary border-2 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                        <div>
                            <span class="badge bg-soft-primary text-primary mb-2">${escapeHtml(labels.coreRequired || '')}</span>
                            ${core.name ? `<h3 class="text-primary mb-1">${escapeHtml(core.name)}</h3>` : ''}
                            ${core.description ? `<p class="text-muted mb-0">${escapeHtml(core.description)}</p>` : ''}
                        </div>
                        <div class="text-end">
                            <div class="fs-4 fw-bold text-primary" data-core-price>${formatPriceDisplay(amount)}</div>
                            <div class="small text-muted">${escapeHtml(labels.perMonth || '')}</div>
                        </div>
                    </div>
                    ${billingBadge(core, state.userCount) ? `<span class="badge bg-light text-muted">${escapeHtml(billingBadge(core, state.userCount))}</span>` : ''}
                    ${featuresHtml ? `<ul class="list-unstyled border-top pt-3 mt-3 mb-0">${featuresHtml}</ul>` : ''}
                </div>
            </div>
        `;

        if (window.feather) {
            window.feather.replace();
        }
    }

    function renderAddons() {
        if (!els.addonsGrid) {
            return;
        }

        const addons = getAddonModules();
        els.addonsGrid.innerHTML = '';

        if (els.addonsEmpty) {
            els.addonsEmpty.classList.toggle('d-none', addons.length > 0);
        }

        addons.forEach((module) => {
            const amount = monthlyAmount(module, state.userCount);
            const checked = state.selectedAddonIds.has(module.id);
            const col = document.createElement('div');
            col.className = 'col-lg-4 col-xl-4 mb-4';
            col.innerHTML = `
                <div class="card border hoverable h-100 ${checked ? 'border-primary' : ''}" data-addon-card data-module-id="${module.id}">
                    <div class="card-body d-flex flex-column">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="addon-${module.id}" data-addon-checkbox data-module-id="${module.id}" ${checked ? 'checked' : ''}>
                            <label class="form-check-label fw-semibold" for="addon-${module.id}">${escapeHtml(labels.selectModule || 'Include')}</label>
                        </div>
                        ${module.name ? `<h4 class="text-primary my-0">${escapeHtml(module.name)}</h4>` : ''}
                        <div class="mt-2 mb-1">
                            <span class="fw-bolder display-6" data-addon-price>${formatPriceDisplay(amount)}</span>
                            <span class="fw-normal text-muted fs-13">${escapeHtml(labels.perMonth || '')}</span>
                        </div>
                        ${billingBadge(module, state.userCount) ? `<span class="badge bg-soft-primary text-primary">${escapeHtml(billingBadge(module, state.userCount))}</span>` : ''}
                        ${module.description ? `<p class="text-muted small mt-2 mb-0">${escapeHtml(module.description)}</p>` : ''}
                        <ul class="list-unstyled border-top py-3 mt-3 mb-0 text-start flex-grow-1">
                            ${(module.features || []).map((text) => `<li class="py-1">${escapeHtml(text)}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
            els.addonsGrid.appendChild(col);
        });

        els.addonsGrid.querySelectorAll('[data-addon-checkbox]').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const moduleId = parseInt(checkbox.getAttribute('data-module-id'), 10);
                if (checkbox.checked) {
                    state.selectedAddonIds.add(moduleId);
                } else {
                    state.selectedAddonIds.delete(moduleId);
                }
                renderAddons();
                renderBreakdown();
            });
        });
    }

    function renderBreakdown() {
        const core = getCoreModule();
        const lines = [];
        let total = 0;
        let hasCustomQuote = false;

        if (core) {
            const amount = monthlyAmount(core, state.userCount);
            lines.push({
                name: core.name || core.code,
                amount,
                required: true,
            });
            if (amount !== null) {
                total += amount;
            } else {
                hasCustomQuote = true;
            }
        }

        getAddonModules()
            .filter((module) => state.selectedAddonIds.has(module.id))
            .forEach((module) => {
                const amount = monthlyAmount(module, state.userCount);
                lines.push({
                    name: module.name || module.code,
                    amount,
                    required: false,
                });
                if (amount !== null) {
                    total += amount;
                } else {
                    hasCustomQuote = true;
                }
            });

        const hasLines = lines.length > 0;
        const totalLabel = hasCustomQuote
            ? `${formatPriceDisplay(total)} + ${labels.customQuote || ''}`
            : formatPriceDisplay(total);
        const contextLabel = buildEstimateContextLabel();
        const linesHtml = lines
            .map((line) => `
                <div class="d-flex justify-content-between align-items-start gap-3 py-2 border-bottom">
                    <div>
                        <span class="fw-semibold">${escapeHtml(line.name)}</span>
                        ${line.required ? `<span class="badge bg-soft-primary text-primary ms-1">${escapeHtml(labels.coreRequired || '')}</span>` : ''}
                    </div>
                    <span class="text-nowrap fw-semibold">${formatPriceDisplay(line.amount)}</span>
                </div>
            `)
            .join('');

        breakdownPanels.forEach((panel) => {
            if (panel.empty) {
                panel.empty.classList.toggle('d-none', hasLines);
            }
            if (panel.content) {
                panel.content.classList.toggle('d-none', !hasLines);
                panel.content.classList.toggle('d-flex', hasLines);
            }
            if (panel.lines) {
                panel.lines.innerHTML = linesHtml;
            }
            if (panel.total) {
                panel.total.textContent = totalLabel;
            }
            if (panel.context) {
                panel.context.textContent = contextLabel;
            }
        });

        if (els.mobileBreakdownTotal) {
            els.mobileBreakdownTotal.textContent = `${totalLabel} ${labels.perMonth || ''}`.trim();
        }
        if (els.mobileBreakdownContext) {
            els.mobileBreakdownContext.textContent = contextLabel;
        }

        updateExchangeRateNote();
    }

    function buildEstimateContextLabel() {
        const currency = getSelectedCurrency();
        const template = labels.estimateContext || ':count · :code';
        const countTemplate = labels.usersSummary || ':count';

        return template
            .replace(':count', countTemplate.replace(':count', String(state.userCount)))
            .replace(':code', currency?.code || 'USD');
    }

    function updateConfigSummary() {
        const activeButton = [...els.accountTypeButtons].find(
            (button) => button.getAttribute('data-account-type') === state.accountTypeCode,
        );
        const currency = getSelectedCurrency();
        const usersTemplate = labels.usersSummary || ':count';

        if (els.configSummaryRole) {
            els.configSummaryRole.textContent = activeButton?.getAttribute('data-account-type-name') || state.accountTypeCode;
        }
        if (els.configSummaryUsers) {
            els.configSummaryUsers.textContent = usersTemplate.replace(':count', String(state.userCount));
        }
        if (els.configSummaryCurrency) {
            els.configSummaryCurrency.textContent = currency?.code || 'USD';
        }
    }

    function syncAccountTypeButtons() {
        els.accountTypeButtons.forEach((button) => {
            const isActive = button.getAttribute('data-account-type') === state.accountTypeCode;
            button.classList.toggle('btn-primary', isActive);
            button.classList.toggle('btn-outline-primary', !isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function syncUserPresets() {
        els.userPresetButtons.forEach((button) => {
            const value = parseInt(button.getAttribute('data-user-preset'), 10);
            const isActive = value === state.userCount;
            button.classList.toggle('btn-primary', isActive);
            button.classList.toggle('btn-outline-primary', !isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        if (els.userCountInput) {
            els.userCountInput.value = String(state.userCount);
        }
    }

    function setCurrency(currencyId) {
        state.currencyId = parseInt(currencyId, 10) || null;
        updateConfigSummary();
        renderCore();
        renderAddons();
        renderBreakdown();
        updateExchangeRateNote();
    }

    function setAccountType(code) {
        state.accountTypeCode = code;
        state.selectedAddonIds = new Set(
            [...state.selectedAddonIds].filter((id) => getAddonModules().some((module) => module.id === id)),
        );
        syncAccountTypeButtons();
        updateConfigSummary();
        renderCore();
        renderAddons();
        renderBreakdown();
    }

    function setUserCount(count) {
        const parsed = Math.max(1, parseInt(count, 10) || 1);
        state.userCount = parsed;
        syncUserPresets();
        updateConfigSummary();
        renderCore();
        renderAddons();
        renderBreakdown();
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    els.accountTypeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setAccountType(button.getAttribute('data-account-type'));
        });
    });

    els.userPresetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setUserCount(button.getAttribute('data-user-preset'));
        });
    });

    if (els.userCountInput) {
        els.userCountInput.addEventListener('change', () => {
            setUserCount(els.userCountInput.value);
        });
        els.userCountInput.addEventListener('input', () => {
            const parsed = parseInt(els.userCountInput.value, 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                state.userCount = parsed;
                syncUserPresets();
                updateConfigSummary();
            }
        });
        els.userCountInput.addEventListener('blur', () => {
            setUserCount(els.userCountInput.value);
        });
    }

    if (els.currencySelect) {
        els.currencySelect.addEventListener('change', () => {
            setCurrency(els.currencySelect.value);
        });
    }

    setAccountType(state.accountTypeCode);
    setUserCount(state.userCount);
    if (state.currencyId) {
        setCurrency(state.currencyId);
    } else {
        updateConfigSummary();
        updateExchangeRateNote();
    }

    root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        if (window.bootstrap?.Tooltip) {
            new window.bootstrap.Tooltip(element);
        }
    });

    if (window.feather) {
        window.feather.replace();
    }
}
