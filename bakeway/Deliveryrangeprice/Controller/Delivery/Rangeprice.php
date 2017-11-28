<?php
/**
 *
 * Copyright © 2015 Bakewaycommerce. All rights reserved.
 */
namespace Bakeway\Deliveryrangeprice\Controller\Delivery;

use Magento\Framework\App\RequestInterface;

class Rangeprice extends \Magento\Framework\App\Action\Action
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
    public $_customerSession;
    protected $logger;

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
        \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->rangepriceFactory = $rangepriceFactory;
        $this->_customerSession = $customerSession;
        $this->logger = $logger;

    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get(
            'Magento\Customer\Model\Url'
        )->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }


    /**
     * Flush cache storage
     *
     */
    public function execute()
    {

        $this->resultPage = $this->resultPageFactory->create();
        $this->resultRedirectPage = $this->resultRedirectFactory->create();
        $this->resultPage->getConfig()->getTitle()->set('Manage Delivery Fee');
        $_sellerid = $this->getRequest()->getParam('seller_id');
        $_fieldid = $this->getRequest()->getParam('field_id');
        $_status = $this->getRequest()->getParam('is_active');
        $_fromkms = $this->getRequest()->getParam('from_kms');
        $_tokms = $this->getRequest()->getParam('to_kms');
        $_deliveryprice = $this->getRequest()->getParam('delivery_price');

        $data = $this->getRequest()->getPostValue();

        $_Collection = $this->rangepriceFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $this->_customerSession->getCustomerId());

        if (count($_Collection) < 8):


            if (!empty($_sellerid) && !empty($_fromkms) && !empty($_tokms) && !empty($_deliveryprice)):
                $_Model = $this->rangepriceFactory->create();

                try {
                    if (!empty($_sellerid) && !empty($_fromkms) && !empty($_tokms) && !empty($_deliveryprice)):
                    else:

                    endif;

                    return $this->resultPage;

                } catch (\Exception $e) {

                }
            endif;

        else:
            if (!empty($_sellerid) && !empty($_fromkms) && !empty($_tokms) && !empty($_deliveryprice)):
            endif;
        endif;
        return $this->resultPage;

    }
}
