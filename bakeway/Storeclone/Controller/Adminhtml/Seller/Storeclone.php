<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Inspection\Exception;

/**
 * Bakeway Storeclone controller.
 */
class Storeclone extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Bakeway\Storeclone\Helper\Data
     */
    protected $helper;

    /**
     * @param Context       $context
     * @param PageFactory   $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Bakeway\Storeclone\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Bakeway\Storeclone\Helper\Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Clone Store Products action.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $sellerId = $this->getRequest()->getParam('sellerId');
        $id = $this->getRequest()->getParam('id');
        try{
            $this->helper->cloneStoreByStoreId($sellerId, $id);
            $this->messageManager->addSuccess(__('Products are cloned successfully.'));
            $this->getResponse()->representJson('success');
        }catch (Exception $e) {
            $this->getResponse()->representJson('error');
        }
    }
}