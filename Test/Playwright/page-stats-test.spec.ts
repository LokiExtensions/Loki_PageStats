import {openTestPage} from '@loki/open-test-page';
import {test, expect} from '@loki/test';
import {configure, getStatValue, STATISTICS, THEMES} from './helpers/configure';

for (const theme of THEMES) {
    test.describe(`Loki page stats (${theme})`, () => {
        test.beforeEach(async ({page}) => {
            await configure(page, {theme});
            await openTestPage(page);
        });

        test('should show a fixed box in the bottom right corner', async ({page}) => {
            const box = page.locator('.loki-page-stats');
            await expect(box).toBeVisible();
            await expect(box).toHaveCSS('position', 'fixed');

            const boundingBox = await box.boundingBox();
            const viewport = page.viewportSize();
            expect(boundingBox).not.toBeNull();
            expect(viewport).not.toBeNull();
            expect(boundingBox!.x + boundingBox!.width).toBeGreaterThan(viewport!.width - 50);
            expect(boundingBox!.y + boundingBox!.height).toBeGreaterThan(viewport!.height - 50);
        });

        test('should show all statistic labels', async ({page}) => {
            const labels = await page.locator('.loki-page-stats dt').allTextContents();
            expect(labels.map(label => label.trim())).toEqual(STATISTICS.map(statistic => statistic.label));
        });

        for (const statistic of STATISTICS) {
            test(`should fill in the statistic "${statistic.label}"`, async ({page}) => {
                await expect.poll(() => getStatValue(page, statistic.name)).toMatch(statistic.pattern);
            });
        }

        test('should count elements and scripts of the page', async ({page}) => {
            await expect.poll(() => getStatValue(page, 'element-count')).toMatch(/^\d+$/);

            const elementCount = parseInt(await getStatValue(page, 'element-count'), 10);
            const actualCount = await page.evaluate(() => document.getElementsByTagName('*').length);
            expect(elementCount).toBeGreaterThan(0);
            expect(elementCount).toBeLessThanOrEqual(actualCount);

            const scriptCount = parseInt(await getStatValue(page, 'script-count'), 10);
            expect(scriptCount).toBeGreaterThan(0);
        });
    });
}
