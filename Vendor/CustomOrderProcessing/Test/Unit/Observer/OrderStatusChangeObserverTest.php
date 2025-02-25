<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Vendor\CustomOrderProcessing\Model\OrderStatusLog;
use Vendor\CustomOrderProcessing\Model\OrderStatusLogFactory;
use Vendor\CustomOrderProcessing\Observer\OrderStatusChangeObserver;

class OrderStatusChangeObserverTest extends TestCase
{
    /**
     * @var \Vendor\CustomModule\Observer\OrderStatusObserver
     */
    private $observerInstance;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var \Vendor\CustomModule\Model\OrderStatusLogFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderStatusLogFactoryMock;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $transportBuilderMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit\Framework\MockObject\MockObject
     */
    private $observerMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->orderStatusLogFactoryMock = $this->createMock(OrderStatusLogFactory::class);
        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->observerMock = $this->createMock(Observer::class);
        $this->orderMock = $this->createMock(Order::class);

        $this->observerInstance = new OrderStatusChangeObserver(
            $this->loggerMock,
            $this->orderStatusLogFactoryMock,
            $this->transportBuilderMock,
            $this->storeManagerMock
        );
    }

    public function testOrderStatusChangeLogging()
    {
        $this->orderMock->method('getId')->willReturn(123);
        $this->orderMock->method('getIncrementId')->willReturn('100000001');
        $this->orderMock->method('getOrigData')->with('status')->willReturn('pending');
        $this->orderMock->method('getStatus')->willReturn('processing');

        // Pass order object correctly
        $this->observerMock->method('getEvent')->willReturn(new Event(['order' => $this->orderMock]));

        $orderStatusLogMock = $this->createMock(OrderStatusLog::class);
        $this->orderStatusLogFactoryMock->method('create')->willReturn($orderStatusLogMock);
        $orderStatusLogMock->expects($this->once())->method('setData');
        $orderStatusLogMock->expects($this->once())->method('save');

        $this->loggerMock->expects($this->once())->method('info')
            ->with("Order 100000001 status changed from pending to processing");

        $this->observerInstance->execute($this->observerMock);
    }
}
