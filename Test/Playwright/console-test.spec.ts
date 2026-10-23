import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, getStatValue, THEMES} from './helpers/configure';

// Missing or misconfigured static assets of the test environment are unrelated to this module
const isAssetLoadingError = (text: string): boolean =>
    text.startsWith('Refused to apply style from') || text.startsWith('Failed to load resource');

for (const theme of THEMES) {
    test.describe(`Loki page stats browser console (${theme})`, () => {
        test('should not produce JavaScript errors or CSP violations', async ({page}) => {
            const errors: string[] = [];
            page.on('pageerror', error => errors.push('pageerror: ' + error.message));
            page.on('console', message => {
                if (message.type() === 'error' && !isAssetLoadingError(message.text())) {
                    errors.push('console: ' + message.text());
                }
            });

            await page.addInitScript(() => {
                (window as any).lokiCspViolations = [];
                document.addEventListener('securitypolicyviolation', (event) => {
                    (window as any).lokiCspViolations.push(event.violatedDirective + ' ' + event.blockedURI);
                });
            });

            await configure(page, {theme});
            await openTestPage(page);
            await expect.poll(() => getStatValue(page, 'script-size')).toMatch(/^\d+ Kb$/);

            const cspViolations = await page.evaluate(() => (window as any).lokiCspViolations);
            expect(cspViolations, 'CSP violations').toEqual([]);
            expect(errors, 'JavaScript errors').toEqual([]);
        });
    });
}
