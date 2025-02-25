<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Test\Unit\Model\ResourceModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog;

/**
 * Unit Test for OrderStatusLog Resource Model
 */
class OrderStatusLogTest extends TestCase
{
    /**
     * @var OrderStatusLog
     */
    private $resourceModel;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceModel = $objectManager->getObject(OrderStatusLog::class);
    }

    public function testResourceModelInitialization()
    {
        // Use Reflection to access protected property `_mainTable`
        $reflection = new \ReflectionClass($this->resourceModel);
        $property = $reflection->getProperty('_mainTable');
        $property->setAccessible(true);
        $mainTable = $property->getValue($this->resourceModel);

        $this->assertEquals('order_status_log', $mainTable, 'The database table name should be order_status_log');

        // Check primary key
        $property = $reflection->getProperty('_idFieldName');
        $property->setAccessible(true);
        $idField = $property->getValue($this->resourceModel);

        $this->assertEquals('log_id', $idField, 'The primary key should be log_id');
    }
}
