<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Api;

interface OrderStatusUpdateInterface
{
    /**
     * Update the order status by order increment ID.
     *
     * @param string $orderIncrementId The order increment ID
     * @param string $newStatus The new status to be assigned
     * @return array{success: bool, message: string, order_id: int, new_status: string}
     * Response array with status update details
     * @throws \Magento\Framework\Exception\LocalizedException
     * If the order is already in the requested status or order not found
     */
    public function updateOrderStatus(string $orderIncrementId, string $newStatus): array;
}
