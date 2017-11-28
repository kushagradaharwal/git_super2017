<?php

namespace Bakeway\PaymentsApi\Model;

/*
 * including razorpay class file
 */
require(BP . "/vendor/razorpay-php/Razorpay.php");

use Razorpay\Api\Api;
use Bakeway\Vendorapi\Model\OrderStatus as BakewayOrderStatus;

class Capture extends \Magento\Framework\Model\AbstractModel {

    CONST TXNTYPE = "capture";

    /*
     * Magento\Sales\Model\Order $order
     */

    protected $_order;

    /*
     * Bakeway\Razorpay\Model\Configr $config
     */
    protected $_config;

    /*
     * Magento\Sales\Model\Order\Payment $payment
     */
    protected $_payment;


    /*
     * Magento\Framework\ObjectManagerInterfacet $objectManager
     */
    protected $_objectManager;

    /*
     *  \Magento\Sales\Model\Order\Payment\Transaction $transcation
     */
    protected $_transcation;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Sales\Model\Order $order, \Bakeway\Razorpay\Model\Config $config, \Magento\Sales\Model\Order\Payment $payment, \Magento\Sales\Model\Order\Payment\Transaction $transcation) {
        $this->_order = $order;
        $this->_config = $config;
        $this->_payment = $payment;
        $this->_objectManager = $objectManager;
        $this->_transcation = $transcation;
    }

    /*
     * capture payment for orderId 
     * @param int orderId
     */
     public function capturePayment($orderId) {
        $_amount = "";
        $logObj = $this->getLogfilename();
        if (isset($orderId)):
            $orderObj = $this->_order->load($orderId);
            $paymentObj = $this->_payment;
            $apikeyid = $this->_config->getKeyId();
            $apikeysecret = $this->_config->getKeySecret();

            /* get obj of Razor pay api */
            $api = new Api($apikeyid, $apikeysecret);
            
            $filterOrderId = $paymentObj->getCollection()
                             ->addFieldToFilter('parent_id',["eq"=>$orderId])
                             ->getFirstItem();
            $checkOrderintranscationTable = $this->_transcation->getCollection()
                                          ->addFieldToFilter('order_id',["eq"=>$orderId])
                                          ->getFirstItem();
            if(!empty($filterOrderId->getRazorpayPaymentId())):
            $paymentId = $filterOrderId->getRazorpayPaymentId();
            if ($paymentId):
                try {
                    $payment = $api->payment->fetch($paymentId);
                } catch (\Exception $e) {

                    echo $e->getMessage();
                }

                $_amount = $payment->amount;
                try {
                    $payment->capture(array('amount' => $_amount));
                    $id = $payment->id;
                    $order_id = $payment->order_id;
                    /* add entry on transcation table */
                    
                    if(empty($checkOrderintranscationTable->getOrderId())):
                        $transObj = $this->_transcation;
                            $transObj->setOrderId($orderObj->getId());
                            $transObj->setPaymentId($filterOrderId->getId());
                            $transObj->setTxnId($id);
                            $transObj->setTxnType(self::TXNTYPE);
                            $transObj->setRazorpayOrderId($order_id);
                            $transObj->setRazorpayPaymentId($id);
                            $transObj->setRazorpaySignature($filterOrderId->getRazorpaySignature());
                            try {
                                $transObj->save();
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                        endif;
                    $orderObj->addStatusToHistory($orderObj->getStatus(), 'Payment captured by partner successfully.');
                    $orderObj->save();
                    $logObj->info("order id =>" . $orderId . " paymentid=>" . $paymentId . " has cauputred");
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            endif;

             endif;
        endif;
    }

    public function getLogfilename() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/capture_log.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        return $logger;
    }

}
