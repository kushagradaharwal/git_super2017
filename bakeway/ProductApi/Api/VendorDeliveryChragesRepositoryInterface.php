<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_ProductApi
 * @author    Bakeway
 */
namespace Bakeway\ProductApi\Api;

/**
 * Vendor Delivery Charges Details.
 */
interface VendorDeliveryChragesRepositoryInterface {
    
    /**
     * Get Vendor Delivery Charges Details
     * @param int $vendorId
     * @return array
     * @return empty []
     */
    public function getDeliverycharges($vendorId);
}