<?php
namespace Vendor\CustomOrderProcessing\Controller\Adminhtml\OrderStatusLog;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class MassDelete
 *
 * Controller for deleting multiple order status log entries from the admin grid.
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param Action\Context $context
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute method to handle mass deletion of logs.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $ids = (array) $this->getRequest()->getParam('selected', []);

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        if (empty($ids)) {
            $this->messageManager->addErrorMessage(__('Please select log(s) to delete.'));
            return $resultRedirect;
        }

        try {
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('log_id', ['in' => $ids]);

            $deletedCount = 0;
            foreach ($collection as $item) {
                $item->delete();
                $deletedCount++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 log(s) have been deleted.', $deletedCount)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while deleting logs: %1', $e->getMessage()));
        }

        return $resultRedirect;
    }

    /**
     * Check if user has access to mass delete action.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Vendor_CustomOrderProcessing::log_mass_delete');
    }
}
