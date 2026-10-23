<?php declare(strict_types=1);

namespace Loki\PageStats\Test\Integration\Template;

use Loki\PageStats\Test\Integration\Traits\LayoutLoader;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class StatsTemplateTest extends TestCase
{
    use LayoutLoader;

    #[DataProvider('getStatisticData')]
    public function testRendersStatisticLabel(string $label, string $id): void
    {
        $this->assertMatchesRegularExpression(
            '#<dt>' . preg_quote($label, '#') . '</dt>\s*<dd id="' . preg_quote($id, '#') . '"></dd>#',
            $this->render()
        );
    }

    public static function getStatisticData(): array
    {
        return [
            ['Body size', 'loki-page-stats-body-size'],
            ['DOM complete', 'loki-page-stats-dom-complete'],
            ['Elements found', 'loki-page-stats-element-count'],
            ['Event counts', 'loki-page-stats-events'],
            ['Script count', 'loki-page-stats-script-count'],
            ['Script size', 'loki-page-stats-script-size'],
        ];
    }

    public function testRendersFixedPositionStyle(): void
    {
        $html = $this->render();

        $this->assertStringContainsString('<div class="loki-page-stats">', $html);
        $this->assertStringContainsString('.loki-page-stats {', $html);
        $this->assertStringContainsString('position: fixed;', $html);
        $this->assertSame(1, substr_count($html, '<style>'));
    }

    public function testRendersStatsScript(): void
    {
        $html = $this->render();

        $this->assertSame(1, substr_count($html, '<script>'));
        $this->assertStringContainsString("performance.getEntriesByType('navigation')", $html);
        $this->assertStringContainsString("performance.getEntriesByType('resource')", $html);
        $this->assertStringContainsString("document.getElementById('loki-page-stats-' + name)", $html);
    }

    private function render(): string
    {
        $block = $this->createLayout()->createBlock(Template::class, 'loki.page-stats.test.' . uniqid(), [
            'data' => ['template' => 'Loki_PageStats::stats.phtml'],
        ]);

        return (string)$block->toHtml();
    }
}
