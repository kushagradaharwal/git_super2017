<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\CustomAttributes\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteSubmitBefore implements ObserverInterface
{
    /**
     * sales quote submit before event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        $deliveryType = $quote->getData('delivery_type');
        $deliveryTime = $quote->getData('delivery_time');
        $customerNotes = $quote->getData('customer_notes');
        $storeUniqueName = $quote->getData('store_unique_name');
        $order->setData('delivery_type', $deliveryType);
        $order->setData('delivery_time', $deliveryTime);
        $order->setData('customer_notes', $customerNotes);
        $order->setData('store_unique_name', $storeUniqueName);
        return;
    }
}
