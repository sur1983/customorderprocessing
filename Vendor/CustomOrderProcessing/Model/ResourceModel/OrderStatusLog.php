<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource Model for Order Status Log
 */
class OrderStatusLog extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * Maps to the database table `order_status_log` with primary key `log_id`
     */
    protected function _construct()
    {
        $this->_init('order_status_log', 'log_id'); // Table name and primary key
    }
}
