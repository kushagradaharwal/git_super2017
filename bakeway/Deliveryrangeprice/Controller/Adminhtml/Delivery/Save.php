<?php
namespace Bakeway\Deliveryrangeprice\Controller\Adminhtml\Delivery;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    protected $helper;
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bakeway\Deliveryrangeprice\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;

    }

    public function execute()
    {

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model = $this->_objectManager->create('Bakeway\Deliveryrangeprice\Model\Rangeprice');

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $_SellerEmailId = $this->getRequest()->getParam('seller_id');

            $_CheckUser = $this->helper->getidFromEmail($_SellerEmailId);

            $_connt = $this->helper->checkCollectionforid($_CheckUser);
            if (empty($_CheckUser)) {
                $this->messageManager->addError(__('Sorry, there is no seller with this email!!!.'));
                $this->_redirect('deliveryrangeprice/delivery/new');
                return;
            }else if($_connt == 8)
            {
                $this->messageManager->addError(__('Please delete some record you have reached maximum limit to enter record!.'));
                $this->_redirect('deliveryrangeprice/delivery/index');
                return;
            }
            $model->setData($data);
            $model->setSellerId($_CheckUser);
            $model->setDeliveryDeleted(false);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Information is Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('customer/index/edit/', array('id' => $model->getSellerId(), '_current' => true));
                    return;
                }
                $this->_redirect('customer/index/edit/', array('id' => $model->getSellerId(), '_current' => true));
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('customer/index/edit/', array('id' => $model->getSellerId(), '_current' => true));
            return;
        }
        $this->_redirect('*/*/');
    }


}
