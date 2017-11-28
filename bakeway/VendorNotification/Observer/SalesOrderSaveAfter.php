<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfter implements ObserverInterface {

    const AWATING_CONFIRMATION = 'pending';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Bakeway\VendorNotification\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Bakeway\VendorNotification\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Bakeway\VendorNotification\Helper\Data $helper
    ) {
        $this->objectManager = $objectManager;
        $this->helper = $helper;
    }

    /**
     * sales order place after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderId = $observer->getOrder()->getId();
        $sellerId = 0;

        $sellerOrder = $this->objectManager
                        ->create('Webkul\Marketplace\Model\Orders')
                        ->getCollection()
                        ->addFieldToFilter('order_id', $orderId)
                        ->addFieldToFilter('seller_id', ['neq' => 0]);

        $sellerOrderCount = $sellerOrder->count();
        if ($sellerOrderCount > 0) {
            $sellerId = $sellerOrder->getFirstItem()->getSellerId();
        }

        if ($sellerId !== 0) {
            $this->helper->sendOrderStatusChangeSms($sellerId, $observer->getOrder());
        }

    }

}
