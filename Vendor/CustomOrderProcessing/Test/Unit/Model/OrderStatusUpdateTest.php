<?php
namespace Vendor\CustomOrderProcessing\Test\Unit\Model;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Vendor\CustomOrderProcessing\Model\OrderStatusUpdate;

class OrderStatusUpdateTest extends TestCase
{
    /**
     * @var OrderStatusUpdate
     */
    private $orderStatusUpdate;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $filterBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $loggerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $orderMock;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->filterBuilderMock = $this->createMock(FilterBuilder::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->orderMock = $this->createMock(Order::class);

        $this->orderStatusUpdate = new OrderStatusUpdate(
            $this->orderRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filterBuilderMock,
            $this->loggerMock
        );
    }

    public function testUpdateOrderStatusSuccessfully()
    {
        $orderIncrementId = '100000001';
        $newStatus = 'processing';

        $filterMock = $this->createMock(Filter::class);
        $this->filterBuilderMock->method('setField')->willReturnSelf();
        $this->filterBuilderMock->method('setValue')->willReturnSelf();
        $this->filterBuilderMock->method('setConditionType')->willReturnSelf();
        $this->filterBuilderMock->method('create')->willReturn($filterMock);

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->method('addFilters')->willReturnSelf();
        $this->searchCriteriaBuilderMock->method('create')->willReturn($searchCriteriaMock);

        $searchResultsMock = $this->createMock(SearchResults::class);
        $searchResultsMock->method('getItems')->willReturn([$this->orderMock]);
        $this->orderRepositoryMock->method('getList')->willReturn($searchResultsMock);

        // Set up order mock behavior
        $this->orderMock->method('getStatus')->willReturnOnConsecutiveCalls('pending', $newStatus);
        $this->orderMock->method('setStatus')->willReturnCallback(function ($status) {
            $this->orderMock->method('getStatus')->willReturn($status);
        });

        $this->orderMock->expects($this->once())->method('setStatus')->with($newStatus);
        $this->orderMock->expects($this->once())->method('setState')->with(Order::STATE_PROCESSING);
        $this->orderRepositoryMock->expects($this->once())->method('save')->with($this->orderMock);
        $this->orderMock->method('getEntityId')->willReturn(1);

        $result = $this->orderStatusUpdate->updateOrderStatus($orderIncrementId, $newStatus);

        $this->assertTrue($result['success']);
        $this->assertEquals($newStatus, $result['new_status']); // This will now pass
    }

    public function testUpdateOrderStatusOrderNotFound()
    {
        $orderIncrementId = '100000002';
        $newStatus = 'processing';

        $filterMock = $this->createMock(Filter::class);
        $this->filterBuilderMock->method('setField')->willReturnSelf();
        $this->filterBuilderMock->method('setValue')->willReturnSelf();
        $this->filterBuilderMock->method('setConditionType')->willReturnSelf();
        $this->filterBuilderMock->method('create')->willReturn($filterMock);

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->method('addFilters')->willReturnSelf();
        $this->searchCriteriaBuilderMock->method('create')->willReturn($searchCriteriaMock);

        $searchResultsMock = $this->createMock(SearchResults::class);
        $searchResultsMock->method('getItems')->willReturn([]);
        $this->orderRepositoryMock->method('getList')->willReturn($searchResultsMock);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("Order with increment ID 100000002 not found.");

        $this->orderStatusUpdate->updateOrderStatus($orderIncrementId, $newStatus);
    }

    public function testUpdateOrderStatusAlreadyInRequestedStatus()
    {
        $orderIncrementId = '100000003';
        $newStatus = 'complete';

        $filterMock = $this->createMock(Filter::class);
        $this->filterBuilderMock->method('setField')->willReturnSelf();
        $this->filterBuilderMock->method('setValue')->willReturnSelf();
        $this->filterBuilderMock->method('setConditionType')->willReturnSelf();
        $this->filterBuilderMock->method('create')->willReturn($filterMock);

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->method('addFilters')->willReturnSelf();
        $this->searchCriteriaBuilderMock->method('create')->willReturn($searchCriteriaMock);

        $searchResultsMock = $this->createMock(SearchResults::class);
        $searchResultsMock->method('getItems')->willReturn([$this->orderMock]);
        $this->orderRepositoryMock->method('getList')->willReturn($searchResultsMock);

        $this->orderMock->method('getStatus')->willReturn($newStatus);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("Order is already in the requested status.");

        $this->orderStatusUpdate->updateOrderStatus($orderIncrementId, $newStatus);
    }

    public function testUpdateOrderStatusHandlesExceptions()
    {
        $orderIncrementId = '100000004';
        $newStatus = 'processing';

        $filterMock = $this->createMock(Filter::class);
        $this->filterBuilderMock->method('setField')->willReturnSelf();
        $this->filterBuilderMock->method('setValue')->willReturnSelf();
        $this->filterBuilderMock->method('setConditionType')->willReturnSelf();
        $this->filterBuilderMock->method('create')->willReturn($filterMock);

        $this->searchCriteriaBuilderMock->method('addFilters')->willReturnSelf();
        $this->searchCriteriaBuilderMock->method('create')->willThrowException(new \Exception("Test Exception"));

        $this->loggerMock->expects($this->once())->method('error')->with($this->stringContains("Test Exception"));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage("Could not update order status.");

        $this->orderStatusUpdate->updateOrderStatus($orderIncrementId, $newStatus);
    }
}
