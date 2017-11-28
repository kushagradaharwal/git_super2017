<?php

namespace Bakeway\Shopapi\Model\Resource;

use Bakeway\Shopapi\Api\ShopRepositoryInterface;
use Webkul\Marketplace\Model\Seller as VendorShop;
use Magento\Framework\App\ResourceConnectionFactory;
use Magento\Framework\Exception\NotFoundException;
use Bakeway\Shopapi\Model\CustomShopFactory;

class ShopRepository implements ShopRepositoryInterface {

    /**
     * @var ResourceConnectionFactory
     */
    protected $_resourceConnection;

    /**
     * @var Shopapi
     */
    protected $_vendorShop;
    protected $_shopStatus;

    public function __construct(
    ResourceConnectionFactory $_resourceConnection, VendorShop $_vendorShop, CustomShopFactory $_customShopFactory
    ) {
        $this->_resourceConnection = $_resourceConnection;
        $this->_vendorShop = $_vendorShop;
        $this->_customShopFactory = $_customShopFactory;
    }

    /**
     * @param int $vendorId
     * @return \Bakeway\Shopapi\Api\Data\ShopStatusInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getShopStatus($vendorId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $current_date = date('Y/m/d');
        $statusModel = $this->_customShopFactory->create();
        $sellerStatus = $statusModel->getCollection()
                        ->addFieldToFilter(
                                'seller_id', $vendorId
                        )->addFieldToFilter(
                        'date', $current_date
                )->addFieldToSelect('*')->load();

        if ($sellerStatus->count() > 0) {
            $shopStatusId = $sellerStatus->getFirstItem()->getId();
            $statusModel->load($shopStatusId);
            return $statusModel;
        } else {
            return $this->setShopStatus($vendorId, $status = \Bakeway\Shopapi\Model\CustomShop::STATUS_CLOSE);
        }
    }

    /**
     * @param int $vendorId
     * @param int $status
     * @return \Bakeway\Shopapi\Api\Data\ShopStatusInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function setShopStatus($vendorId, $status) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $current_date = date('Y/m/d');
        $statusModel = $this->_customShopFactory->create();
        $sellerStatus = $statusModel->getCollection()
                        ->addFieldToFilter(
                                'seller_id', $vendorId
                        )->addFieldToFilter(
                        'date', $current_date
                )->addFieldToSelect('*')->load();

        if ($sellerStatus->count() > 0) {
            $shopStatusId = $sellerStatus->getFirstItem()->getId();
            $statusModel->load($shopStatusId);
            $openCloseLogString = $statusModel->getData('open_close_logs');
        } else {
            $openCloseLogString = serialize(array());
            $statusModel->setData('seller_id', $vendorId);
            $statusModel->setData('date', $current_date);
        }
        $statusModel->setData('status', $status);
        $openCloseLogs = unserialize($openCloseLogString);
        if ($status == \Bakeway\Shopapi\Model\CustomShop::STATUS_OPEN) {
            $statusModel->setData('open_time', $date);
            $statusModel->setData('open_by', $vendorId);
            $logs = array('opened'=>$date);
        } else {
            $statusModel->setData('close_time', $date);
            $statusModel->setData('close_by', $vendorId);
            $logs = array('closed'=>$date);
        }
        
        if(is_array($openCloseLogs)){
            array_push($openCloseLogs,$logs);
        }
        $statusModel->setData('open_close_logs',serialize($openCloseLogs));

        $statusModel->save();
        return $statusModel;
    }

}
