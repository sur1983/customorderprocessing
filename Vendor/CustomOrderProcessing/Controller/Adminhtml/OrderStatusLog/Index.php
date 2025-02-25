<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Controller\Adminhtml\OrderStatusLog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Order Status Log Admin Controller
 */
class Index extends Action
{
    /** @var string */
    public const ADMIN_RESOURCE = 'Vendor_CustomOrderProcessing::order_status_log';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute method for Order Status Log grid.
     *
     * @return Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Vendor_CustomOrderProcessing::order_status_log');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Status Logs'));

        return $resultPage;
    }
}
