<?php

/**
 * Copyright © 2015 Bakeway . All rights reserved.
 */

namespace Bakeway\ShopAvailability\Block\Availability;

class Index extends \Magento\Framework\View\Element\Template {
    /*
     * \Bakeway\CommissionLog\Model\CommissionLogFactory
     */

    protected $commissionLogCollection;

    /*
     * \Magento\Customer\Model\Session
     */
    protected $customerSesssion;

    /*
     * \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $objectManager;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSesssion, \Magento\Framework\ObjectManagerInterface $objectManager, array $data = []
    ) {
        $this->_customerSession = $customerSesssion;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function _prepareLayout() {
        $this->pageConfig->getTitle()->set(__('Shop Availability'));
        return parent::_prepareLayout();
    }

    /**
     * @return vednor details
     */
    public function getVendorData() {
        $model = $this->_objectManager->create(
                                'Webkul\Marketplace\Model\Seller'
                        )
                        ->getCollection()
                        ->addFieldToFilter(
                                'seller_id', $this->_customerSession->getCustomerId()
                        )->getLastItem();

        return $model->getData();
    }

    public function getCustomerId() {
        return $this->_customerSession->getCustomerId();
    }

}
