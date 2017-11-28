<?php
/**
 * Copyright © 2015 Bakeway . All rights reserved.
 */
namespace Bakeway\Deliveryrangeprice\Block\Delivery;

use Magento\Customer\Model\Customer;

class Rangeprice extends \Magento\Framework\View\Element\Template
{
    public $hello = 'Hello World';

    /**
     * @var \Magento\Customer\Model\Customer
     */
    public $customer;
    /**
     * @var Session
     */
    public $_customerSession;

    /**
     * @var ObjectManagerInterface
     */
    public $_objectManager;

    public $deliverydata;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    protected $rangepriceFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Bakeway\Deliveryrangeprice\Helper\Data $deliverydata,
        \Magento\Framework\Registry $coreRegistry,
        \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory,
        array $data = []
    )
    {
        $this->Customer = $customer;
        $this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;
        $this->deliverydata = $deliverydata;
        $this->coreRegistry = $coreRegistry;
        $this->rangepriceFactory = $rangepriceFactory;
        parent::__construct($context, $data);
    }


    public function getFormAction()
    {
        return $this->getUrl('deliveryrangeprice/delivery/rangeprice', ['_secure' => true]);
    }

    public function getSellerData()
    {
        $_Collection = $this->deliverydata->getCurrentSellerData()
            ->addFieldToFilter('delivery_deleted',0)
            ->addFieldToFilter('seller_id', $this->getCustomerId());
        
        
        if (count($_Collection) > 0) {
            return $_Collection->getData();
        } else {
            return false;
        }
    }
   
   public function getSellerCcCount()
    {
        $_Collection = $this->deliverydata->getCurrentSellerData()
            ->addFieldToFilter('delivery_deleted',0)
            ->addFieldToFilter('seller_id', $this->getCustomerId());
        
        
        if (count($_Collection) > 0) {
            return $_Collection;
        } else {
            return false;
        }
    }
   
    
    public function getCustomerId()
    {
        return $this->_customerSession->getCustomerId();
    }

    public function getSellerInformation()
    {
        // will return current seller id  to  edit
        $_Sellerdata = $this->rangepriceFactory->create()->load($this->getSellerid());
        return $_Sellerdata->getData();
    }

    public function getSellerid()
    {
        // will return current seller id  to  edit
        return $this->coreRegistry->registry('sellerid');
    }

    public function getSellerEmail($id)
    {
        return $this->deliverydata->getEmail($id);
    }

    public function getDeliveryvalue($id)
    {
        return $this->deliverydata->getSelleredelivery($id);
    }
  
    public function setDeliveryStatusfunctionName() {
        $_id =  $this->_customerSession->getCustomerId();
        $_sellerSave = $this->_objectManager->create('Webkul\Marketplace\Model\SellerFactory')->load($_id);
        $_sellerSave->setDelivery(0); //will change the input on api
        $_sellerSave->save();
    }
}
