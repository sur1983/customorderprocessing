<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Vendor\CustomOrderProcessing\Model\OrderStatusLog;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;

class OrderStatusLogTest extends TestCase
{
    /**
     * @var OrderStatusLog
     */
    private $model;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ResourceModel
     */
    private $resourceMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        // Mock the resource model
        $this->resourceMock = $this->createMock(ResourceModel::class);

        // Inject the mock into the model
        $this->model = $objectManager->getObject(OrderStatusLog::class, [
            'resource' => $this->resourceMock, // Injecting the mocked resource model
        ]);
    }

    public function testResourceModel()
    {
        $this->assertInstanceOf(ResourceModel::class, $this->model->getResource());
    }
}
