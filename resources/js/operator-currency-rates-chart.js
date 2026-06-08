import Chart from 'chart.js/auto';

const BUY_COLOR = '#0d6efd';
const SELL_COLOR = '#fd7e14';

function formatRate(value) {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '—';
    }

    const formatted = Number(value).toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 4,
    });

    return formatted;
}

function readPayload(root) {
    const raw = root.dataset.chart;

    if (!raw) {
        return null;
    }

    try {
        return JSON.parse(raw);
    } catch {
        return null;
    }
}

function findCurrency(payload, currencyId) {
    const id = Number(currencyId);

    return (payload.currencies ?? []).find((currency) => Number(currency.id) === id) ?? null;
}

function updateCurrentValues(root, currency) {
    const buyEl = root.querySelector('[data-chart-current-buy]');
    const sellEl = root.querySelector('[data-chart-current-sell]');
    const updatedEl = root.querySelector('[data-chart-updated-value]');

    if (buyEl) {
        buyEl.textContent = formatRate(currency?.current?.buy);
    }

    if (sellEl) {
        sellEl.textContent = formatRate(currency?.current?.sell);
    }

    if (updatedEl) {
        updatedEl.textContent = currency?.current?.updated_at_human ?? '—';
    }
}

function buildChartConfig(payload, currency) {
    const strings = payload.strings ?? {};

    return {
        type: 'line',
        data: {
            labels: currency.series.labels,
            datasets: [
                {
                    label: strings.buy ?? 'Buy',
                    data: currency.series.buy,
                    borderColor: BUY_COLOR,
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    pointBackgroundColor: BUY_COLOR,
                    pointRadius: 3,
                    tension: 0.25,
                    spanGaps: true,
                },
                {
                    label: strings.sell ?? 'Sell',
                    data: currency.series.sell,
                    borderColor: SELL_COLOR,
                    backgroundColor: 'rgba(253, 126, 20, 0.08)',
                    pointBackgroundColor: SELL_COLOR,
                    pointRadius: 3,
                    tension: 0.25,
                    spanGaps: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const label = context.dataset.label ?? '';
                            const value = formatRate(context.parsed.y);

                            return `${label}: ${value}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    ticks: {
                        callback(value) {
                            return formatRate(value);
                        },
                    },
                },
            },
        },
    };
}

function initOperatorCurrencyRatesChart() {
    const root = document.getElementById('operator-currency-rates-chart-root');

    if (!root) {
        return;
    }

    const payload = readPayload(root);

    if (!payload || !Array.isArray(payload.currencies) || payload.currencies.length === 0) {
        return;
    }

    const canvas = root.querySelector('[data-chart-canvas]');
    const select = root.querySelector('[data-chart-currency-select]');

    if (!canvas || !select) {
        return;
    }

    let chart = null;

    const render = (currencyId) => {
        const currency = findCurrency(payload, currencyId) ?? payload.currencies[0];

        if (!currency) {
            return;
        }

        updateCurrentValues(root, currency);

        const config = buildChartConfig(payload, currency);

        if (chart) {
            chart.data = config.data;
            chart.options = config.options;
            chart.update();

            return;
        }

        chart = new Chart(canvas, config);
    };

    render(select.value);

    select.addEventListener('change', () => {
        render(select.value);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOperatorCurrencyRatesChart);
} else {
    initOperatorCurrencyRatesChart();
}
