<?php

namespace Bakeway\Razorpay\Api;

/**
 * Razor payment gateway interface.
 */

interface PaymentgatewayRepositoryInterface
{
    /**
     * @api
     * @param string $order_id
     * @param string $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function order($order_id,$cartId);
    
    /**
     * @api
     * @param string $order_id
     * @param string $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function guestorder($order_id,$cartId);
    
    
    /**
     * @api
     * @param string $cartId
     * @param string $razorpay_payment_id
     * @param string $razorpay_order_id
     * @param string $razorpay_signature
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function processcapture($cartId,$razorpay_payment_id,$razorpay_order_id,$razorpay_signature);
    
    
     /**
     * @api
     * @param string $cartId
     * @param string $razorpay_payment_id
     * @param string $razorpay_order_id
     * @param string $razorpay_signature
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function processcaptureguest($cartId,$razorpay_payment_id,$razorpay_order_id,$razorpay_signature);
    
}