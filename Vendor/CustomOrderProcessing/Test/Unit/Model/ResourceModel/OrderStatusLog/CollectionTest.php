<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Test\Unit\Model\ResourceModel\OrderStatusLog;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Vendor\CustomOrderProcessing\Model\OrderStatusLog;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\Collection;

/**
 * Unit Test for OrderStatusLog Collection
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AdapterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $connectionMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        // Mock DB Connection
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        // Mock Select object to prevent errors from `from()`
        $selectMock = $this->createMock(Select::class);
        $this->connectionMock->method('select')->willReturn($selectMock);

        // Mock ResourceModel to return the connection
        $resourceMock = $this->createMock(ResourceModel::class);
        $resourceMock->method('getConnection')->willReturn($this->connectionMock);

        // Mock EntityFactory
        $entityFactoryMock = $this->createMock(EntityFactoryInterface::class);

        // Create collection instance with mocked dependencies
        $this->collection = $objectManager->getObject(
            Collection::class,
            [
                'entityFactory' => $entityFactoryMock,
                'resource' => $resourceMock,
                'connection' => $this->connectionMock
            ]
        );
    }

    public function testCollectionInitialization()
    {
        $this->assertInstanceOf(
            \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::class,
            $this->collection
        );
    }
}
