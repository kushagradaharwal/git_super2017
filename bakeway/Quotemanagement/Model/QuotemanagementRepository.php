<?php

namespace Bakeway\Quotemanagement\Model;

use Bakeway\Quotemanagement\Api\QuotemanagementRepositoryInterface;

class QuotemanagementRepository implements QuotemanagementRepositoryInterface {

    protected $cartRepository;
    protected $guestcartRepository;
    
    /*
     * \Magento\Quote\Model\Quote $quoteObject
     */
    protected $quoteObject;
 
    public function __construct(
    \Magento\Quote\Api\CartRepositoryInterface $cartRepository, \Magento\Quote\Api\GuestCartRepositoryInterface $guestcartRepository
    ,\Magento\Quote\Model\Quote $quoteObject) {
        $this->cartRepository = $cartRepository;
        $this->guestcartRepository = $guestcartRepository;
        $this->quoteObject = $quoteObject;
    }

    /**
     * @api
     * @param int $cartId
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return array
     */
    public function create($cartId) {
        $responce = array();
        $quoteObj = $this->quoteObject->load($cartId);
        $quoteObj->reserveOrderId();
        $this->cartRepository->save($quoteObj);
        /* get reserved order id from quote */
        $reserved_order_id = $quoteObj->getReservedOrderId();
        try {
            $responce['status'] =  true;
            $responce['order_id'] = $reserved_order_id;
        } catch (\Exception $e) {
            $responce['status'] =  false;
            $responce['error'] = $e->getMessage();
        }
        return json_decode(json_encode($responce),false);
    }
    
    /**
     * @api
     * @param string $cartId
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return array
     */
    public function createguest($cartId) {
        $responce = array();
        $guestQuote = $this->guestcartRepository->get($cartId);
        $cartId = $guestQuote->getId();
        $quoteObj = $this->quoteObject->load($cartId);
        $quoteObj->reserveOrderId();
        $this->cartRepository->save($quoteObj);
        /* get reserved order id from quote */
        $reserved_order_id = $quoteObj->getReservedOrderId();
        try {
            $responce['status'] =  true;
            $responce['order_id'] = $reserved_order_id;
        } catch (\Exception $e) {
            $responce['status'] =  false;
            $responce['error'] = $e->getMessage();
        }
        return json_decode(json_encode($responce),false);
    }
    

}
