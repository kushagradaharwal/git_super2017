<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_ProductApi
 * @author    Bakeway
 */

namespace Bakeway\ProductApi\Model;

use Bakeway\ProductApi\Api\VendorDeliveryChragesRepositoryInterface;
use \Magento\Framework\Exception\NotFoundException;

class VendorDeliveryChragesRepository implements VendorDeliveryChragesRepositoryInterface {
    /*
     * @param \Bakeway\Deliveryrangeprice\Helper\Data 
     */

    protected $_deliveryrangepricehelper;

    public function __construct(
    \Bakeway\Deliveryrangeprice\Helper\Data $deliveryrangepricehelper
    ) {
        $this->_deliveryrangepricehelper = $deliveryrangepricehelper;
    }

    /**
     * Get Vendor Delivery Charges Details
     * @param int $vendorId
     * @return array
     * @return empty []
     */
    public function getDeliverycharges($vendorId) {
        $deliveryCharges = [];
        $collection = $this->_deliveryrangepricehelper->getSellerDeliverychargesdetails($vendorId);
        if (!empty($collection)):
            foreach ($collection as $key => $value) {
                $deliveryCharges[] = [
                    "from_kms" => $value['from_kms'],
                    "to_kms" => $value['to_kms'],
                    "charges" => $value['delivery_price']
                ];
            }
        endif;

        return $deliveryCharges;
    }

}
