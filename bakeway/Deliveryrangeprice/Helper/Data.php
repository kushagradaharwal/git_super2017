<?php

namespace Bakeway\Deliveryrangeprice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Bakeway\Deliveryrangeprice\Model\RangepriceFactory
     */
    protected $rangepriceFactory;

    /**
     * @var  \Magento\Customer\Model\CustomerFactory
     */
    protected $_sellerFactory;
    protected $vendorFactory;

    /**
     * @var  \Magento\Customer\Block\Account\Dashboard\Info
     */
    protected $customerdash;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory, \Magento\Customer\Model\CustomerFactory $sellerFactory, \Webkul\Marketplace\Model\SellerFactory $vendorFactory, \Magento\Customer\Block\Account\Dashboard\Info $customerdash
    ) {
        parent::__construct($context);
        $this->rangepriceFactory = $rangepriceFactory;
        $this->_sellerFactory = $sellerFactory;
        $this->vendorFactory = $vendorFactory;
        $this->customerdash = $customerdash;
    }

    /**
     * seller email collection
     * @return array
     */
    public function getSellercollection() {

        $_Collection = $this->_sellerFactory->create()->getCollection()
                ->addAttributeToSelect('email');
        $_SellerArray = [];
        foreach ($_Collection as $_CollectionData) {

            $_SellerArray[$_CollectionData['entity_id']] = $_CollectionData['email'];
        }

        return $_SellerArray;
    }

    public function getCurrentSellerData() {
        return $this->rangepriceFactory->create()->getCollection();
    }

    public function checkCollectionforid($id) {

        $_Collection = $this->rangepriceFactory->create()->getCollection()
                ->addFieldToFilter('delivery_deleted', 0)
                ->addFieldToFilter('seller_id', $id);
        return count($_Collection);
    }

    public function getEmail($id) {
        $_Email = [];
        $_Collection = $this->_sellerFactory->create()->getCollection()
                ->addAttributeToFilter('entity_id', $id);
        foreach ($_Collection as $_Collection1) {
            $_Email[] = $_Collection1['email'];
        }

        return $_Email[0];
    }

    public function getidFromEmail($email) {
        $_Email = $_EntityId = [];

        $_Collection = $this->_sellerFactory->create()->getCollection()
                ->addAttributeToFilter('email', array("like" => '%' . $email . '%'));
        if (count($_Collection) > 0) {
            foreach ($_Collection as $_Collection1) {
                $_EntityId[] = $_Collection1['entity_id'];
            }

            return $_EntityId[0];
        } else {
            return "";
        }
    }

    public function getSelleredelivery($id) {
        $_Seller = $this->vendorFactory->create()->getCollection()
                ->addFieldToFilter("seller_id", $id);
        return $_Seller->getFirstItem()->getData('delivery');
    }

    public function getSellerEntityid($id) {
        $_Seller = $this->vendorFactory->create()->getCollection()
                ->addFieldToFilter("seller_id", $id);
        return $_Seller->getFirstItem()->getData('entity_id');
    }

    public function getPasswordurl() {
        return $this->customerdash->getChangePasswordUrl();
    }

    public function getSellerDeliverychargesdetails($vendorId) {
        $_Collection = $this->getCurrentSellerData()
                ->addFieldToFilter('delivery_deleted', 0)
                ->addFieldToFilter('seller_id', $vendorId);
        if (count($_Collection) > 0) {
            return $_Collection->getData();
        } else {
            return false;
        }
    }

    /*
     * @param int $vendorId
     * return bool
     */

    public function autoDeliveryChage($vendorId) {
        $_Collection = $this->getCurrentSellerData()
                ->addFieldToFilter('delivery_deleted', 0)
                ->addFieldToFilter('seller_id', $vendorId);
        if (count($_Collection) > 0) {
            return true;
        } else {
            return false;
        }
    }

}
