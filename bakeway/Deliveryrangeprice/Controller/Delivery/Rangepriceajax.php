<?php
/**
 *
 * Copyright © 2015 Bakewaycommerce. All rights reserved.
 */
namespace Bakeway\Deliveryrangeprice\Controller\Delivery;

use Magento\Framework\App\RequestInterface;

class Rangepriceajax extends \Magento\Framework\App\Action\Action
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

        $_Responce = array();

        $this->resultPage = $this->resultPageFactory->create();
        $this->resultRedirectPage = $this->resultRedirectFactory->create();
        $this->resultPage->getConfig()->getTitle()->set('Delivery Range & Price');
        $_sellerid = $this->getRequest()->getParam('seller_id');
        $_fieldid = $this->getRequest()->getParam('field_id');
        $_status = $this->getRequest()->getParam('is_active');

        if ($_status == "Enable"):
            $_status = 1;
        else:
            $_status = 0;
        endif;

        $_fromkms = $this->getRequest()->getParam('from_kms');
        $_tokms = $this->getRequest()->getParam('to_kms');
        $_deliveryprice = $this->getRequest()->getParam('delivery_price');
        $data = $this->getRequest()->getPostValue();
        $_Collection = $this->rangepriceFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $this->_customerSession->getCustomerId());
        $_Model = $this->rangepriceFactory->create();
        $_Model->setSellerId($_sellerid);
        $_Model->setFieldId($_fieldid);
        $_Model->setIsActive($_status);
        $_Model->setFromKms($_fromkms);
        $_Model->setToKms($_tokms);
        $_Model->setDeliveryPrice($_deliveryprice);
        $_Model->setDeliveryDeleted(false);

        try {
            $_Model->save();

            if (!empty($_sellerid) && !empty($_fromkms) && !empty($_tokms) && !empty($_deliveryprice)):
            endif;
        } catch (\Exception $e) {

        }


        $_CollectionUpdate = $this->rangepriceFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $this->_customerSession->getCustomerId())
            ->getLastItem();
        $_Responce['id'] = $_CollectionUpdate['id'];
        $_Responce['fkms'] = $_CollectionUpdate['from_kms'];
        $_Responce['tkms'] = $_CollectionUpdate['to_kms'];
        $_Responce['price'] = $_CollectionUpdate['delivery_price'];
        $_Responce['status'] = $_CollectionUpdate['is_active'];

        $_Responce['savedsuccess'] = $_CollectionUpdate['id'];

        echo json_encode($_Responce);
        die;
    }

    public function getCollectionseller()
    {
        $_Collection1 = $this->rangepriceFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $this->_customerSession->getCustomerId());
    }
}
