<?php

/**
 *
 * Copyright © 2015 Bakewaycommerce. All rights reserved.
 */

namespace Bakeway\ShopAvailability\Controller\Availability;

use Magento\Framework\App\RequestInterface;

class Save extends \Magento\Framework\App\Action\Action {
    /*
     * \Bakeway\CommissionLog\Model\CommissionLogFactory
     */

    protected $commissionLogCollection;

    /*
     * \Magento\Customer\Model\Session
     */
    protected $customerSesssion;/**
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

    /**
     * @var \Webkul\Marketplace\Model\Seller 
     */
    protected $webkulmodel;

    /**
     * @var \Bakeway\Deliveryrangeprice\Helper\Data
     */
    protected $deliveryhelper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Bakeway\CommissionLog\Model\CommissionLogFactory $commissionLogFactory, \Magento\Customer\Model\Session $customerSesssion
    , \Webkul\Marketplace\Model\SellerFactory $webkulmodelFactory, \Bakeway\Deliveryrangeprice\Helper\Data $deliveryhelper
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->commissionLogCollection = $commissionLogFactory;
        $this->customerSesssion = $customerSesssion;
        $this->webkulmodel = $webkulmodelFactory;
        $this->deliveryhelper = $deliveryhelper;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request) {
        $loginUrl = $this->_objectManager->get(
                        'Magento\Customer\Model\Url'
                )->getLoginUrl();

        if (!$this->customerSesssion->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Flush cache storage
     *
     */
    public function execute() {
        $postData = $this->getRequest()->getPostValue();

        $_Sellerid = $this->deliveryhelper->getSellerEntityid($this->getCustomerId());
        $_Model = $this->webkulmodel->create()->load($_Sellerid);
        // echo "<pre>";
        // print_r($_Model->getData());die;
        if (!empty($this->getCustomerId())):

            $_daysarray = "";
            if (isset($postData['userdata_operational_days_0']) || isset($postData['userdata_operational_days_1']) ||
                    isset($postData['userdata_operational_days_2']) || isset($postData['userdata_operational_days_3']) ||
                    isset($postData['userdata_operational_days_4']) || isset($postData['userdata_operational_days_5']) ||
                    isset($postData['userdata_operational_days_6'])):

                $_days = isset($postData['userdata_operational_days_0']) ? 1 : 0;
                $_days1 = isset($postData['userdata_operational_days_1']) ? 1 : 0;
                $_days2 = isset($postData['userdata_operational_days_2']) ? 1 : 0;
                $_days3 = isset($postData['userdata_operational_days_3']) ? 1 : 0;
                $_days4 = isset($postData['userdata_operational_days_4']) ? 1 : 0;
                $_days5 = isset($postData['userdata_operational_days_5']) ? 1 : 0;
                $_days6 = isset($postData['userdata_operational_days_6']) ? 1 : 0;
                $_daysarray = array($_days, $_days1, $_days2, $_days3, $_days4, $_days5, $_days6);

            endif;
            $_DaysVal = serialize($_daysarray);

            $_Model['userdata_operational_days'] = $_DaysVal;

            if (isset($postData['userdata_shop_temporarily_u_from'])):
                $_FromDate = $postData['userdata_shop_temporarily_u_from'];
            else:
                $_FromDate = "";

            endif;

            if (isset($postData['userdata_shop_temporarily_u_to'])):
                $_ToDate = $postData['userdata_shop_temporarily_u_to'];
            else:
                $_ToDate = "";
            endif;

            if (isset($postData['userdata_shop_operatational_status'])):
                $_OperaStatus = 1;
            else:
                $_OperaStatus = 0;
            endif;

            try {
                $_Model->setUserdataOperationalDays($_DaysVal);
                $_Model->setUserdataShopTemporarilyUFrom($_FromDate);
                $_Model->setUserdataShopTemporarilyUTo($_ToDate);
                $_Model->setUserdataShopOperatationalStatus($_OperaStatus);
                $_Model->save();
                $this->messageManager->addSuccess(
                        __('Updated successfully !')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->_redirect('shopavailability/availability/index');
        endif;
        $this->resultPage = $this->resultPageFactory->create();
        return $this->resultPage;
    }

    public function getCustomerId() {
        return $this->customerSesssion->getCustomerId();
    }

}
