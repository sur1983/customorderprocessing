<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Vendor\CustomOrderProcessing\Model\OrderStatusLogFactory;

/**
 * Class OrderStatusChangeObserver
 * Observes order status changes and logs them.
 */
class OrderStatusChangeObserver implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OrderStatusLogFactory
     */
    protected $orderStatusLogFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * OrderStatusChangeObserver constructor.
     *
     * @param LoggerInterface $logger
     * @param OrderStatusLogFactory $orderStatusLogFactory
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        OrderStatusLogFactory $orderStatusLogFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->orderStatusLogFactory = $orderStatusLogFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute observer for order status change.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $oldStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();
        $orderId = $order->getId();

        if ($oldStatus !== $newStatus) {
            $this->logStatusChange($orderId, $oldStatus, $newStatus);
            $this->logger->info("Order {$order->getIncrementId()} status changed from {$oldStatus} to {$newStatus}");
        }

        if ($newStatus === Order::STATE_COMPLETE) {
            $this->sendShipmentEmail($order);
        }
    }

    /**
     * Log order status change in the custom table.
     *
     * @param int $orderId
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    private function logStatusChange(int $orderId, string $oldStatus, string $newStatus): void
    {
        $orderStatusLog = $this->orderStatusLogFactory->create();
        $orderStatusLog->setData([
            'order_id'   => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'timestamp'  => date('Y-m-d H:i:s')
        ]);
        $orderStatusLog->save();
    }

    /**
     * Send shipment email to the customer when the order is marked as shipped.
     *
     * @param Order $order
     * @return void
     */
    private function sendShipmentEmail(Order $order): void
    {
        try {
            $customerEmail = $order->getCustomerEmail();
            $customerName = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
            $storeId = $this->storeManager->getStore()->getId();

            $trackingNumber = '';
            foreach ($order->getShipmentsCollection() as $shipment) {
                foreach ($shipment->getTracksCollection() as $track) {
                    $trackingNumber = $track->getTrackNumber();
                }
            }

            $templateVars = [
                'customer_name'     => $customerName,
                'order_increment_id' => $order->getIncrementId(), // Ensure order ID is passed explicitly
                'tracking_number'   => $trackingNumber ?: 'N/A',
                'store'             => $this->storeManager->getStore()
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('order_shipment_notification')
                ->setTemplateOptions([
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ])
                ->setTemplateVars($templateVars)
                ->setFrom([
                    'name'  => 'Support',
                    'email' => 'support@example.com'
                ])
                ->addTo($customerEmail, $customerName)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error('Shipment Email Error: ' . $e->getMessage());
        }
    }
}
