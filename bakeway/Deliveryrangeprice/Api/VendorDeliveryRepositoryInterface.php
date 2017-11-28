<?php

namespace Bakeway\Deliveryrangeprice\Api;

/**
 * Seller Information interface.
 */

interface VendorDeliveryRepositoryInterface
{
    /**
     * Update Vendor Delivery status.
     *
     * @api
     * @param int $vendorId
     * @param string $status
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateDelivery($vendorId, $status);


    /**
     * Get vendor Delivery status
     * @api
     * @param int $vendorId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getDeliverystatus($vendorId);
}