<?php
/**
 *
 * Copyright © 2015 Bakewaycommerce. All rights reserved.
 */
namespace Bakeway\Deliveryrangeprice\Controller\Delivery;

class Delete extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $rangepriceFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->rangepriceFactory = $rangepriceFactory;

    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultRedirectPage = $this->resultRedirectFactory->create();    
        $this->resultPage->getConfig()->getTitle()->set('Delivery Range & Price');
        $_sellerid = $this->getRequest()->getParam('id');
        $_email = $this->getRequest()->getParam('email');
        $_Model =  $this->rangepriceFactory->create()->load( $_sellerid);
        $_Model->setDeliveryDeleted(true);
        $_Model->setSellerLog($_email);
        try {
            $_Model->save();
        
        }catch( \Exception $e){
        }
        
        die;
    }
}
