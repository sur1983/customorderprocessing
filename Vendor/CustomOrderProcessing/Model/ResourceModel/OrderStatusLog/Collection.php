<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Vendor\CustomOrderProcessing\Model\OrderStatusLog as Model;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;

/**
 * Collection class for Order Status Log
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource model for the collection
     *
     * Associates the model and resource model for the `order_status_log` table.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
