<?php

/**
 * @category  Bakeway
 * @package   Bakeway_Shopapi
 * @author    Bakeway
 */

namespace Bakeway\Shopapi\Api;

/**
 * Shop Repository  interface.
 */
interface ShopRepositoryInterface {

    /**
     * @param int $vendorId
     * @return \Bakeway\Shopapi\Api\Data\ShopStatusInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getShopStatus($vendorId);

    /**
     * @param int $vendorId
     * @param int $status
     * @return \Bakeway\Shopapi\Api\Data\ShopStatusInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function setShopStatus($vendorId, $status);
}
