<?php
namespace Bakeway\Deliveryrangeprice\Controller\Adminhtml\Delivery;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $auth;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->auth = $context->getAuth();
    }

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {

        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select product(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->get('Bakeway\Deliveryrangeprice\Model\Rangeprice')->load($id);
                    $row->setDeliveryDeleted(true);
                    $row->setSellerLog($this->getCurrentadminemail());
                    $row->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function getCurrentadminemail()
    {
        $loginUserEmail = $this->auth->getUser()->getEmail();
        if (!empty($loginUserEmail)) {
            return $loginUserEmail;
        }
        return;
    }
}
