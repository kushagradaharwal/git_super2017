<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_GuestOrder
 * @author    Bakeway
 */

namespace Bakeway\GuestOrder\Model;

use Bakeway\GuestOrder\Api\GuestCartsEditRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\GuestCart\GuestCartRepository;

class GuestCartsEditRepository implements GuestCartsEditRepositoryInterface {

    protected $cartRepository;
    
    protected $guestCartReporitory;
    
    public function __construct(
            CartRepositoryInterface $cartRepository,
            GuestCartRepository $guestCartReporitory
    ) {
        $this->cartRepository = $cartRepository;
        $this->guestCartReporitory = $guestCartReporitory;
    }
    /**
     * Edit Guest Quote.
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function editcart($cartId, \Magento\Quote\Api\Data\CartInterface $quote) {
        //echo $cartId;exit;
        $guestQuote = $this->guestCartReporitory->get($cartId);
        $quoteId = $guestQuote->getId();
        $quote->setId($quoteId);
        $this->cartRepository->save($quote);
        return;
        
    }

}
