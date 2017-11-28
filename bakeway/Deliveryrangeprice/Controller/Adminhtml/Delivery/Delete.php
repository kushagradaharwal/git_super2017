<?php
namespace Bakeway\Deliveryrangeprice\Controller\Adminhtml\Delivery;

class Delete extends \Magento\Backend\App\Action
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

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $_Model = $this->_objectManager->get('Bakeway\Deliveryrangeprice\Model\Rangeprice')->load($id);
            $_Model->setDeliveryDeleted(true);
            $_Model->setSellerLog($this->getCurrentadminemail());
            $_Model->save();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
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
