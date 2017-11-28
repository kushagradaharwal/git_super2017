<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_GuestOrder
 * @author    Bakeway
 */

namespace Bakeway\GuestOrder\Api;


interface GuestCartsEditRepositoryInterface
{
     /**
     * Edit Guest Quote.
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function editcart($cartId, \Magento\Quote\Api\Data\CartInterface $quote);

}