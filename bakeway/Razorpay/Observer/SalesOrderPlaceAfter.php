<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\Razorpay\Observer;

require(BP . "/vendor/razorpay-php/Razorpay.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as SalesOrder;

class SalesOrderPlaceAfter implements ObserverInterface {

    const AWATING_CONFIRMATION = 'pending';

    /**
     * @var \Bakeway\Razorpay\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Bakeway\Razorpay\Model\Config $config
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Bakeway\Razorpay\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * sales order place after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        
        $quoteId = $order->getQuoteId();
        $quoteData = $this->objectManager->create('\Magento\Quote\Model\Quote')
            ->load($quoteId);
        /**
         * check if payment method is razorpay
         */
        $method = $order->getPayment()->getMethod();

        if ($method == 'razorpay') {
            $razororderid = $quoteData['razorpay_order_id'];
            $razorpaymentid = $quoteData['razorpay_payment_id'];
            $razorsignatureid = $quoteData['razorpay_signature'];
            $orderStatus = $this->processcaptureforOrderStatus($razororderid, $razorpaymentid, $razorsignatureid);
            if ($orderStatus === true):
                try {
                    /* update sales order table */
                    $order->getPayment()->setRazorpayOrderId($quoteData['razorpay_order_id']);
                    $order->getPayment()->setRazorpayPaymentId($quoteData['razorpay_payment_id']);
                    $order->getPayment()->setRazorpaySignature($quoteData['razorpay_signature']);
                } catch (\Exception $e) {
                    $e->getError();
                }
            else:
                $state = SalesOrder::STATE_PAYMENT_REVIEW;
                $status = SalesOrder::STATUS_FRAUD;
                $order->setState($state, true);
                $order->setStatus($status);
                $order->save();
            endif;
        }

    }

    /**
     * @param string $razorpay_payment_id
     * @param string $razorpay_order_id
     * @param string $razorpay_signature
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function processcaptureforOrderStatus($razorpay_order_id, $razorpay_payment_id, $razorpay_signature) {

        $success = true;
        if (empty($razorpay_payment_id) === false) {
            $_apikeyid = $this->config->getKeyId();
            $_apikeysecret = $this->config->getKeySecret();
            $rzpmainobj = new Api($_apikeyid, $_apikeysecret);
            try {
                $attributes = [
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razorpay_payment_id,
                    'razorpay_signature' => $razorpay_signature
                ];

                $rzpmainobj->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
            }
        } else {
            $success = false;
        }

        return $success;
    }

}
