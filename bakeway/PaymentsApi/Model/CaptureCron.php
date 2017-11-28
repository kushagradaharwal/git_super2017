<?php

namespace Bakeway\PaymentsApi\Model;

/*
 * including razorpay class file
 */
require(BP . "/vendor/razorpay-php/Razorpay.php");

use Razorpay\Api\Api;
use Bakeway\Vendorapi\Model\OrderStatus as BakewayOrderStatus;

class CaptureCron {

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

    /*
     * \Magento\Sales\Model\Order\Payment\TransactionFactory $transactionFactory
     */
    protected $_transactionFactory;

    /*
     * \Magento\Sales\Model\OrderFactory
     */
    protected $_paymentfactory;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Sales\Model\Order $order, \Bakeway\Razorpay\Model\Config $config, \Magento\Sales\Model\Order\Payment $payment, \Magento\Sales\Model\Order\Payment\Transaction $transcation, \Magento\Sales\Model\Order\Payment\TransactionFactory $transactionFactory, \Magento\Sales\Model\Order\PaymentFactory $paymentfactory) {
        $this->_order = $order;
        $this->_config = $config;
        $this->_payment = $payment;
        $this->_objectManager = $objectManager;
        $this->_transcation = $transcation;
        $this->_transactionFactory = $transactionFactory;
        $this->_paymentfactory = $paymentfactory;
    }

    /*
     * capture payment for orderId 
     * @param int orderId
     */

    public function capturePayment() {

        $_PaymentOrderId = $_trasCollectionOrderId = [];
        $_logObj = $this->getLogfilename();

        $_trasCollection = $this->_transactionFactory->create()->getCollection();
        $_paymentCollection = $this->_paymentfactory->create()->getCollection();

        if (!empty($_trasCollection)):
            foreach ($_trasCollection as $_trasCollectionData) {
                $_trasCollectionOrderId[] = $_trasCollectionData->getOrderId();
            }
        endif;

        if (!empty($_paymentCollection)):
            foreach ($_paymentCollection as $_paymentCollectionData) {
                $_PaymentOrderId = $_paymentCollectionData->getParentId();
                if (!in_array($_PaymentOrderId, $_trasCollectionOrderId)) {

                    $orderObj = $this->_order->load($_PaymentOrderId);
                    $paymentObj = $this->_payment;
                    $apikeyid = $this->_config->getKeyId();
                    $apikeysecret = $this->_config->getKeySecret();

                    /* get obj of Razor pay api */
                    $api = new Api($apikeyid, $apikeysecret);
                    $getPaymentid = $paymentObj->load($orderObj->getId());

                    $paymentId = $getPaymentid->getRazorpayPaymentId();
                    $payment = "";
                    if ($paymentId):
                        try {
                            $payment = $api->payment->fetch($paymentId);
                            $_amount = $payment->amount;
                        } catch (\Exception $e) {

                            echo $e->getMessage();
                        }

                        if (!empty($payment)):
                            try {
                                $payment->capture(array('amount' => $_amount));
                                $id = $payment->id;
                                $order_id = $payment->order_id;
                                /* add entry on transcation table */
                                $transObj = $this->_transcation;
                                $transObj->setOrderId($orderObj->getId());
                                $transObj->setPaymentId($getPaymentid->getId());
                                $transObj->setTxnId($id);
                                $transObj->setTxnType(self::TXNTYPE);
                                $transObj->setRazorpayOrderId($order_id);
                                $transObj->setRazorpayPaymentId($id);
                                $transObj->setRazorpaySignature($getPaymentid->getRazorpaySignature());
                                try {
                                    $transObj->save();
                                } catch (\Exception $e) {
                                    echo $e->getMessage();
                                }

                                $orderObj->addStatusToHistory($orderObj->getStatus(), 'Payment captured by partner successfully.');
                                $orderObj->save();
                                $logObj->info("order id =>" . $orderId . " paymentid=>" . $paymentId . " has cauputred");
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                        endif;
                    endif;
                }
            }
        endif;
    }

    public function getLogfilename() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/capture_cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        return $logger;
    }

}
