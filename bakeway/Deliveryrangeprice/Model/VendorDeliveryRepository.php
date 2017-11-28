<?php

namespace Bakeway\Deliveryrangeprice\Model;

use Bakeway\Deliveryrangeprice\Api\VendorDeliveryRepositoryInterface;
use Webkul\Marketplace\Helper\Data as SellerHelper;

class VendorDeliveryRepository implements VendorDeliveryRepositoryInterface {

    /**
     * @param SellerHelper $sellerHelper
     */
    protected $sellerHelper;

    /**
     * @param \Webkul\Marketplace\Model\SellerFactory $sellerFactory
     */
    protected $sellerFactory;

    /**
     * @param \Bakeway\Deliveryrangeprice\Helper\Data $_deliveryrang eHelper
     */
    protected $deliveryrangeHelper;

    /**
     * VendorInformationRepository constructor.
     * @param SellerHelper $sellerHelper
     */
    public function __construct(
    SellerHelper $sellerHelper, \Webkul\Marketplace\Model\SellerFactory $sellerFactory, \Bakeway\Deliveryrangeprice\Helper\Data $deliveryrangeHelper
    ) {
        $this->sellerHelper = $sellerHelper;
        $this->sellerFactory = $sellerFactory;
        $this->deliveryrangeHelper = $deliveryrangeHelper;
    }

    /**
     * @api
     * @param int $vendorId
     * @param string $status
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateDelivery($vendorId, $status) {

        $_entityId = $this->deliveryrangeHelper->getSellerEntityid($vendorId);
        $_sellerObj = $this->sellerFactory->create();
        if ($status == true):
            $status = 1;
        elseif ($status == false):
            $status = 0;
        endif;
        $_sellerSave = $_sellerObj->load($_entityId);
        $_sellerSave->setDelivery($status); //will change the input on api

        try {
            $_sellerSave->save();
            if ($status == false):
                $result['status'] = false;
            else:
                $result['status'] = true    ;
            endif;
        } catch (\Exception $e) {
            
        }

        return $result;
    }

    /**
     * @api
     * @param int $vendorId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getDeliverystatus($vendorId) {
        $result = [];
        $_DeliveryStatus = $this->deliveryrangeHelper->getSelleredelivery($vendorId);
        if ($_DeliveryStatus == 0):
            $result['status'] = false;
        elseif ($_DeliveryStatus == 1):
            $result['status'] = true;
        endif;
        return $result;
    }

}
