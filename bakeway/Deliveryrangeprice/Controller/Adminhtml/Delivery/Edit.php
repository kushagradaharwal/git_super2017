<?php
namespace Bakeway\Deliveryrangeprice\Controller\Adminhtml\Delivery;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    )
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;


    }

    public function execute()
    {

        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->getConfig()->getTitle()->set('Delivery Range & Price');
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');

        $model = $this->_objectManager->create('Bakeway\Deliveryrangeprice\Model\Rangeprice');

        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This row no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }


        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $registryObject->register('deliveryrangeprice_rangeprice', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();


    }
}
