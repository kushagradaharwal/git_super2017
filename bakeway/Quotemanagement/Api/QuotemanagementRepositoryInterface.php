<?php

namespace Bakeway\Quotemanagement\Api;

/**
 * Razor Quote management API.
 */
interface QuotemanagementRepositoryInterface {

    /**
     * @api
     * @param int $cartId
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return array
     */
    public function create($cartId);
    
     /**
     * @api
     * @param string $cartId
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return array
     */
    public function createguest($cartId);
}
