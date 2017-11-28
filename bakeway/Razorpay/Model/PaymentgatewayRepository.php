<?php

namespace Bakeway\Razorpay\Model;

/*
 * including razorpay class file
 */
require(BP . "/vendor/razorpay-php/Razorpay.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Bakeway\Razorpay\Api\PaymentgatewayRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

class PaymentgatewayRepository implements PaymentgatewayRepositoryInterface {
    /*
     * \Razorpay\Magento\Model\Config $config
     */

    protected $_config;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Api\GuestCartRepositoryInterface
     */
    protected $guestcartRepository;

    public function __construct(
    \Bakeway\Razorpay\Model\Config $config, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    , \Magento\Quote\Api\CartRepositoryInterface $cartRepository, \Magento\Quote\Api\GuestCartRepositoryInterface $guestcartRepository) {
        $this->_config = $config;
        $this->orderRepository = $orderRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartRepository = $cartRepository;
        $this->guestcartRepository = $guestcartRepository;
    }

    /**
     * @api
     * @param string $order_id
     * @param string $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function order($order_id, $cartId) {
        $_orderId = $order_id;
        $cartid = $cartId;
        $_apikeyid = $this->_config->getKeyId();

        $_apikeysecret = $this->_config->getKeySecret();

        $quoteObj = $this->cartRepository->get($cartid);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $_grandtotal = $quoteObj->getBaseGrandTotal();  //base grand total

        $amount = (int) (round($_grandtotal, 2) * 100);

        $receipt_id = $quoteObj->getEntityId(); //get quote id

        $rzpmainobj = new Api($_apikeyid, $_apikeysecret);

        try {
            $order = $rzpmainobj->order->create([
                'amount' => $amount,
                'receipt' => $receipt_id,
                'currency' => $this->_config::CURRENCY,
                'payment_capture' => 0 // auto-capture set 1 when needs auto capture
            ]);


            $responseContent = [
                'message' => 'Unable to create your order. Please contact support.',
                'parameters' => []
            ];

            if ($order !== null && !empty($order->id)) {
                $responseContent = [
                    'razorpay_order_id' => $order->id,
                    'receipt_id' => $receipt_id,
                ];

                $code = 200;

                $quoteObj->setRazorpayOrderId($order->id);
                $quoteObj->save();
            }
        } catch (\Razorpay\Api\Errors\Error $e) {
            $responseContent = [
                'message' => $e->getMessage(),
                'parameters' => []
            ];
        } catch (\Exception $e) {
            $responseContent = [
                'message' => $e->getMessage(),
                'parameters' => []
            ];
        }

        $_response = json_decode(json_encode($responseContent, false));
        return $_response;
    }

    /**
     * @api
     * @param string $order_id
     * @param string $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function guestorder($order_id, $cartId) {
        $_orderId = $order_id;

        $guestQuote = $this->guestcartRepository->get($cartId);

        $cartid = $guestQuote->getId();

        $_apikeyid = $this->_config->getKeyId();

        $_apikeysecret = $this->_config->getKeySecret();

        $quoteObj = $this->cartRepository->get($cartid);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $_grandtotal = $quoteObj->getBaseGrandTotal();  //base grand total

        $amount = (int) (round($_grandtotal, 2) * 100);

        $receipt_id = $quoteObj->getEntityId(); //get quote id

        $rzpmainobj = new Api($_apikeyid, $_apikeysecret);

        try {
            $order = $rzpmainobj->order->create([
                'amount' => $amount,
                'receipt' => $receipt_id,
                'currency' => $this->_config::CURRENCY,
                'payment_capture' => 0 // auto-capture set 1 when needs auto capture
            ]);


            $responseContent = [
                'message' => 'Unable to create your order. Please contact support.',
                'parameters' => []
            ];

            if ($order !== null && !empty($order->id)) {
                $responseContent = [
                    'razorpay_order_id' => $order->id,
                    'receipt_id' => $receipt_id,
                ];

                $code = 200;

                $quoteObj->setRazorpayOrderId($order->id);
                $quoteObj->save();
            }
        } catch (\Razorpay\Api\Errors\Error $e) {
            $responseContent = [
                'message' => $e->getMessage(),
                'parameters' => []
            ];
        } catch (\Exception $e) {
            $responseContent = [
                'message' => $e->getMessage(),
                'parameters' => []
            ];
        }

        $_response = json_decode(json_encode($responseContent, false));
        return $_response;
    }

    /**
     * @api
     * @param string $cartId
     * @param string $razorpay_payment_id
     * @param string $razorpay_order_id
     * @param string $razorpay_signature
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function processcapture($cartId, $razorpay_payment_id, $razorpay_order_id, $razorpay_signature) {

        $success = true;
        $error = "Payment Failed";
        if (empty($razorpay_payment_id) === false) {
            $_apikeyid = $this->_config->getKeyId();
            $_apikeysecret = $this->_config->getKeySecret();
            $rzpmainobj = new Api($_apikeyid, $_apikeysecret);
            try {
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razorpay_payment_id,
                    'razorpay_signature' => $razorpay_signature
                );

                $rzpmainobj->utility->verifyPaymentSignature($attributes);
                $quoteObj = $this->cartRepository->get($cartId);
                $quoteObj->setRazorpayOrderId($razorpay_order_id);
                $quoteObj->setRazorpayPaymentId($razorpay_payment_id);
                $quoteObj->setRazorpaySignature($razorpay_signature);
                $quoteObj->save();
            } catch (SignatureVerificationError $e) {
                $success = false;
                $errorCode = \Bakeway\Razorpay\Model\Config::ERROR_CODE;
                $paymenterrorText = \Bakeway\Razorpay\Model\Config::ERROR_SERVER_MESSAGE;
                $errorMessage = $e->getMessage();
                header('HTTP/1.1 ' . $errorCode . $paymenterrorText);
                header('Content-type: application/json');
                $error = array();
                $response = array(
                     'message' => $errorMessage
                );
                echo json_encode($response);
                exit;
            }
        }

        if ($success === true) {
            $responseContent = [
                'status' => true,
                'razorpay_payment_id' => $razorpay_payment_id,
            ];
        } else {

            $responseContent = [
                'error' => $error,
            ];
        }

        $_response = json_decode(json_encode($responseContent, false));
        return $_response;
    }

    /**
     * @api
     * @param string $cartId
     * @param string $razorpay_payment_id
     * @param string $razorpay_order_id
     * @param string $razorpay_signature
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function processcaptureguest($cartId, $razorpay_payment_id, $razorpay_order_id, $razorpay_signature) {
        $success = true;
        $error = "Payment Failed";
        if (empty($razorpay_payment_id) === false) {
            $_apikeyid = $this->_config->getKeyId();
            $_apikeysecret = $this->_config->getKeySecret();
            $rzpmainobj = new Api($_apikeyid, $_apikeysecret);
            try {
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razorpay_payment_id,
                    'razorpay_signature' => $razorpay_signature
                );

                $rzpmainobj->utility->verifyPaymentSignature($attributes);
                /*actule quote id from mask id*/
                $guestQuote = $this->guestcartRepository->get($cartId);
                $cartid = $guestQuote->getId();
                $quoteObj = $this->cartRepository->get($cartid);
                $quoteObj->setRazorpayOrderId($razorpay_order_id);
                $quoteObj->setRazorpayPaymentId($razorpay_payment_id);
                $quoteObj->setRazorpaySignature($razorpay_signature);
                $quoteObj->save();
            } catch (SignatureVerificationError $e) {
                $success = false;
                $errorCode = \Bakeway\Razorpay\Model\Config::ERROR_CODE;
                $paymenterrorText = \Bakeway\Razorpay\Model\Config::ERROR_SERVER_MESSAGE;
                $errorMessage = $e->getMessage();
                header('HTTP/1.1 ' . $errorCode . $paymenterrorText);
                header('Content-type: application/json');
                $error = array();
                $response = array(
                    'message' => $errorMessage
                );
                echo json_encode($response);
                exit;
            }
        }

        if ($success === true) {
            $responseContent = [
                'status' => true,
                'razorpay_payment_id' => $razorpay_payment_id,
            ];
        } else {

            $responseContent = [
                'error' => $error,
            ];
        }

        $_response = json_decode(json_encode($responseContent, false));
        return $_response;
    }

}
