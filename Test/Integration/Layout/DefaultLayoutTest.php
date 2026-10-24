<?php declare(strict_types=1);

namespace Loki\PageStats\Test\Integration\Layout;

use Loki\PageStats\Test\Integration\Traits\LayoutLoader;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class DefaultLayoutTest extends TestCase
{
    use LayoutLoader;

    #[AppIsolation(true)]
    public function testPageStatsBlockIsAddedToDefaultHandle(): void
    {
        $layout = $this->loadLayout(['default']);

        $block = $layout->getBlock('loki.page-stats');
        $this->assertInstanceOf(Template::class, $block);
        $this->assertSame('Loki_PageStats::stats.phtml', $block->getTemplate());
        $this->assertSame('before.body.end', $layout->getParentName('loki.page-stats'));
    }

    #[AppIsolation(true)]
    public function testBeforeBodyEndRendersPageStats(): void
    {
        $layout = $this->loadLayout(['default']);
        $html = $layout->renderElement('before.body.end');

        $this->assertStringContainsString('class="loki-page-stats"', $html);
    }

    public function testLayoutFilesUseValidSchemaLocation(): void
    {
        $layoutFiles = glob(dirname(__DIR__, 3) . '/view/frontend/layout/*.xml');
        $this->assertNotEmpty($layoutFiles);

        foreach ($layoutFiles as $layoutFile) {
            $xml = simplexml_load_file($layoutFile);
            $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
            $schemaLocation = (string)$xml->attributes('http://www.w3.org/2001/XMLSchema-instance')
                ->noNamespaceSchemaLocation;

            $this->assertSame(
                'urn:magento:framework:View/Layout/etc/page_configuration.xsd',
                $schemaLocation,
                basename($layoutFile)
            );
        }
    }
}
