import { initPricingPage } from './pricing-calculator';

document.addEventListener('DOMContentLoaded', () => {
    const configElement = document.getElementById('pricing-config');
    if (!configElement) {
        return;
    }

    try {
        const config = JSON.parse(configElement.textContent || '{}');
        initPricingPage(config);
    } catch (error) {
        console.error('Pricing page config is invalid.', error);
    }
});
