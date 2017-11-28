<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Api;

/**
 * Seller Device Data CRUD interface.
 */
interface SellerdevicedataRepositoryInterface
{
    /**
     * Create Seller Device Data.
     *
     * @api
     * @param \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData
     * @param int|null $sellerId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData, $sellerId = null);

    /**
     * Update Seller Device Data.
     *
     * @api
     * @param \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData
     * @param int|null $sellerId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update(\Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData, $sellerId = null);
}
