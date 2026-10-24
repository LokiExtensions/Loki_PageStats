<?php declare(strict_types=1);

namespace Loki\PageStats\Test\Integration;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testModuleIsRegistered(): void
    {
        $componentRegistrar = new ComponentRegistrar();
        $this->assertArrayHasKey('Loki_PageStats', $componentRegistrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testModuleIsEnabled(): void
    {
        /** @var ModuleList $moduleList */
        $moduleList = ObjectManager::getInstance()->get(ModuleList::class);
        $this->assertTrue($moduleList->has('Loki_PageStats'));
    }
}
