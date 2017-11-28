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
 * Seller Information interface.
 */

interface VendorInformationRepositoryInterface
{
    /**
     * Get Vendor Information.
     *
     * @api
     * @param int $vendorId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getDetails($vendorId);
}