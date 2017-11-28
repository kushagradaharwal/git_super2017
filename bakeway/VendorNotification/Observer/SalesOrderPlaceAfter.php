<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Observer;

require(BP . "/vendor/razorpay-php/Razorpay.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as SalesOrder;

class SalesOrderPlaceAfter implements ObserverInterface {

    const AWATING_CONFIRMATION = 'pending';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Bakeway\VendorNotification\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Bakeway\Razorpay\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Bakeway\VendorNotification\Helper\Data $helper
     */
    public function __construct(
    \Magento\Framework\ObjectManagerInterface $objectManager, \Bakeway\VendorNotification\Helper\Data $helper, \Bakeway\Razorpay\Model\Config $config
    ) {

        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        $this->_config = $config;
    }

    /**
     * sales order place after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $lastOrderId = $observer->getOrder()->getId();
        $sellerId = 0;
        $endpoints = array();

        $sellerOrder = $this->_objectManager->create(
                        'Webkul\Marketplace\Model\Orders'
                )
                ->getCollection()
                ->addFieldToFilter('order_id', $lastOrderId)
                ->addFieldToFilter('seller_id', ['neq' => 0]);

        $sellorderCount = $sellerOrder->count();
        if ($sellorderCount > 0) {
            $sellerId = $sellerOrder->getFirstItem()->getSellerId();
        }

        $sellerDevices = $this->_objectManager->create(
                        'Bakeway\VendorNotification\Model\Sellerdevicedata'
                )
                ->getCollection()
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('is_dnd', '0')
                ->addFieldToFilter('platform_endpoint', ['neq' => Null])
                ->addFieldToSelect(['platform_endpoint', 'platform']);

        $deviceCount = $sellerDevices->count();
        if ($deviceCount > 0) {
            $endpoints = $sellerDevices;
        }

        /**
         * push notification script when order status update
         * awaiting confirmation/state(new) / status code(pending)
         */
        $_order = $objectManager->create('\Magento\Sales\Model\Order')
                ->load($lastOrderId);

        /** get order status* */
        $getOrderStatus = $_order->getStatus();
        switch ($getOrderStatus):
            case "pending_payment":
                break;
            case "pending":
                $messageText = "Bakeway new order received of Rs." . $observer->getOrder()->getGrandTotal();
                $this->_helper->sendPushNotification($endpoints, $messageText, $lastOrderId);
                $this->_helper->sendNewOrderSms($sellerId, $lastOrderId);
                break;
        endswitch;
        return;
    }
}
