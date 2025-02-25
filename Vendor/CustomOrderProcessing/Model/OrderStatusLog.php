<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class OrderStatusLog
 * Represents a log entry for order status changes.
 */
class OrderStatusLog extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog::class);
    }
}
