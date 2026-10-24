<?php declare(strict_types=1);

namespace Loki\PageStats\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

trait LayoutLoader
{
    /**
     * @param string[] $handles
     */
    private function loadLayout(array $handles, string $theme = 'Magento/luma'): LayoutInterface
    {
        $objectManager = ObjectManager::getInstance();

        /** @var DesignInterface $design */
        $design = $objectManager->get(DesignInterface::class);
        $design->setDesignTheme($theme, 'frontend');

        /** @var PageFactory $pageFactory */
        $pageFactory = $objectManager->get(PageFactory::class);

        /** @var Page $page */
        $page = $pageFactory->create(true);
        $page->getConfig()->setPageLayout('1column');
        $page->addHandle($handles);

        $layout = $page->getLayout();
        $layout->getBlock('root');

        return $layout;
    }

    private function createLayout(): LayoutInterface
    {
        /** @var LayoutInterface $layout */
        $layout = ObjectManager::getInstance()->create(LayoutInterface::class);

        return $layout;
    }
}
