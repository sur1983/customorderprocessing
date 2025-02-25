<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Test\Unit\Controller\Adminhtml\OrderStatusLog;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\TestCase;
use Vendor\CustomOrderProcessing\Controller\Adminhtml\OrderStatusLog\Index;

/**
 * Unit test for Index Controller in OrderStatusLog
 */
class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private $indexController;

    /**
     * @var Context|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * @var PageFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $pageFactoryMock;

    /**
     * @var Page|\PHPUnit\Framework\MockObject\MockObject
     */
    private $resultPageMock;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->contextMock = $this->createMock(Context::class);
        $this->pageFactoryMock = $this->createMock(PageFactory::class);
        $this->resultPageMock = $this->createMock(Page::class); // Corrected to Magento\Backend\Model\View\Result\Page

        // Set up PageFactory to return Page mock
        $this->pageFactoryMock->method('create')->willReturn($this->resultPageMock);

        // Instantiate the controller with mocks
        $this->indexController = new Index($this->contextMock, $this->pageFactoryMock);
    }

    public function testExecute()
    {
        // Expect setActiveMenu() to be called once (Fixed)
        $this->resultPageMock
            ->expects($this->once())
            ->method('setActiveMenu')
            ->with('Vendor_CustomOrderProcessing::order_status_log');

        // Mock title update
        $configMock = $this->createMock(\Magento\Framework\View\Page\Config::class);
        $titleMock = $this->createMock(\Magento\Framework\View\Page\Title::class);

        $this->resultPageMock
            ->method('getConfig')
            ->willReturn($configMock);

        $configMock
            ->method('getTitle')
            ->willReturn($titleMock);

        $titleMock
            ->expects($this->once())
            ->method('prepend')
            ->with(__('Order Status Logs'));

        // Execute controller action
        $result = $this->indexController->execute();

        // Assert the result is a Page instance
        $this->assertInstanceOf(Page::class, $result);
    }
}
