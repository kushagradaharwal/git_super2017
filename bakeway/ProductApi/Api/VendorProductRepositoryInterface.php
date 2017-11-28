<?php

namespace Bakeway\ProductApi\Api;

/**
 * Product Information interface.
 */
interface VendorProductRepositoryInterface {

    /**
     * Get vendor Product details
     * @api
     * @param int $id
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsdetails($id, $sku);
}
