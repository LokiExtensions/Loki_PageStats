import {expect, Page} from '@playwright/test';

export const THEMES = ['Magento/luma', 'Hyva/default-csp'];

export const STATISTICS = [
    {label: 'Body size', name: 'body-size', pattern: /^\d+ Kb$/},
    {label: 'DOM complete', name: 'dom-complete', pattern: /^\d+ ms$/},
    {label: 'Elements found', name: 'element-count', pattern: /^\d+$/},
    {label: 'Event counts', name: 'events', pattern: /^\d+$/},
    {label: 'Script count', name: 'script-count', pattern: /^\d+$/},
    {label: 'Script size', name: 'script-size', pattern: /^\d+ Kb$/},
];

export async function configure(page: Page, config: object) {
    const token = process.env.TEST_TOKEN;
    if (!token) {
        throw new Error('TEST_TOKEN is not set');
    }

    const response = await page.request.post(
        '/loki_functional_tests/index/configure?token=' + encodeURIComponent(token),
        {form: {config: JSON.stringify(config)}}
    );

    const body = await response.text();
    expect(response.status(), 'Configure response: ' + body).toBe(200);

    const data = JSON.parse(body);
    expect(data.error, 'Configure error').toBeUndefined();
    expect(data.result?.theme?.errors, 'Theme errors').toBeUndefined();
}

export async function getStatValue(page: Page, name: string): Promise<string> {
    return ((await page.locator('#loki-page-stats-' + name).textContent()) ?? '').trim();
}
