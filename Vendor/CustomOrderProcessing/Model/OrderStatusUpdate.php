<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Vendor\CustomOrderProcessing\Api\OrderStatusUpdateInterface;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

/**
 * Class OrderStatusUpdate
 * Handles order status updates through the API.
 */
class OrderStatusUpdate implements OrderStatusUpdateInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * Constructor
     *
     * @param OrderRepositoryInterface $orderRepository Order repository instance
     * @param SearchCriteriaBuilder $searchCriteriaBuilder Search criteria builder
     * @param FilterBuilder $filterBuilder Filter builder
     * @param LoggerInterface $logger Logger instance
     * @param OrderResource $orderResource orderResource database
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        LoggerInterface $logger,
        OrderResource $orderResource
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->logger = $logger;
        $this->orderResource = $orderResource;
    }

    /**
     * Update the order status by order increment ID.
     *
     * @param string $orderIncrementId The order increment ID
     * @param string $newStatus The new status to be assigned
     * @return array<string, mixed> Response with success status and updated order details
     * @throws LocalizedException If the order is already in the requested status or order not found
     */
    public function updateOrderStatus(string $orderIncrementId, string $newStatus): array
    {
        try {
            $this->logger->info("Updating order status for Order ID: " . $orderIncrementId);
            
            // Find the order
            $filter = $this->filterBuilder
                ->setField('increment_id')
                ->setValue($orderIncrementId)
                ->setConditionType('eq')
                ->create();
    
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilters([$filter])
                ->create();
    
            $orders = $this->orderRepository->getList($searchCriteria)->getItems();
    
            if (empty($orders)) {
                $this->logger->error("Order not found: " . $orderIncrementId);
                throw new LocalizedException(__('Order with increment ID %1 not found.', $orderIncrementId));
            }
    
            /** @var Order $order */
            $order = reset($orders);
            $this->logger->info("Current Order Status: " . $order->getStatus());
    
            // Validate status transition
            if ($order->getStatus() === $newStatus) {
                $this->logger->info("Order is already in the requested status: " . $newStatus);
                throw new LocalizedException(__('Order is already in the requested status.'));
            }
    
            // Ensure the status exists in Magento
            if (!$this->isValidOrderStatus($newStatus)) {
                $this->logger->error("Invalid order status: " . $newStatus);
                throw new LocalizedException(__('Invalid order status: %1', $newStatus));
            }
            $this->logger->info("Before Save: " . json_encode($order->getData()));
            // Update order status and state
            $order->setState($this->getStateByStatus($newStatus));
            $order->setStatus($this->getStateByStatus($newStatus));
            $order->addStatusHistoryComment(__('Status changed to %1', $newStatus))->setIsCustomerNotified(0);
   
            $this->orderResource->save($order);
            $this->logger->info("After Save: " . json_encode($order->getData()));
            $this->logger->info("Order status updated successfully: " . $order->getStatus());
    
            return [
                'success' => true,
                'message' => __('Order status updated successfully.')->render(),
                'order_id' => $order->getId(),
                'new_status' => $order->getStatus()
            ];
        } catch (LocalizedException $e) {
            $this->logger->error("LocalizedException: " . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error("Error updating order status: " . $e->getMessage());
            throw new LocalizedException(__("Could not update order status."));
        }
    }
    

    /**
     * Get order state based on status.
     *
     * @param string $status The order status
     * @return string The corresponding order state
     */
    private function getStateByStatus($status)
    {
        $statusStateMap = [
            'pending'      => Order::STATE_NEW,
            'processing'   => Order::STATE_PROCESSING,
            'complete'     => Order::STATE_COMPLETE,
            'canceled'     => Order::STATE_CANCELED,
            'closed'       => Order::STATE_CLOSED,
            'holded'       => Order::STATE_HOLDED,
            'payment_review' => Order::STATE_PAYMENT_REVIEW
        ];

        return $statusStateMap[$status] ?? Order::STATE_NEW;
    }

    /**
 * Check if the order status exists in Magento
 *
 * @param string $status
 * @return bool
 */
private function isValidOrderStatus(string $status): bool
{
    $validStatuses = [
        'pending', 'processing', 'complete', 'canceled', 'closed', 'holded', 'payment_review', 
        'custom_status' // Add your custom statuses here
    ];
    return in_array($status, $validStatuses, true);
}

}
