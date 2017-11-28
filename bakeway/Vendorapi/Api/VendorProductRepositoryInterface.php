<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Api;

/**
 * Seller Product interface.
 */

interface VendorProductRepositoryInterface
{
	/**
     * Get Vendor Products.
     *
     * @api
     * @param int $vendorId
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts($vendorId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);
}
