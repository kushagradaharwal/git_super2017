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
use Magento\Sales\Api\Data\OrderExtensionFactory;

class SalesOrderLoadAfter implements ObserverInterface
{
    /**
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(
        OrderExtensionFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * sales order get after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $extensionAttributes = $order->getExtensionAttributes();
        if($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $deliveryType = $order->getData('delivery_type');
        $deliveryTime = $order->getData('delivery_time');
        $customerNotes = $order->getData('customer_notes');
        $conviencefees = $order->getData('fee');
        $storeUniqueName = $order->getData('store_unique_name');
        $extensionAttributes->setDeliveryType($deliveryType);
        $extensionAttributes->setDeliveryTime($deliveryTime);
        $extensionAttributes->setCustomerNotes($customerNotes);
        $extensionAttributes->setStoreUniqueName($storeUniqueName);
        $conviencefeesArray = ["fee"=>$conviencefees];
        $extensionAttributes->setAdditionalCharges([$conviencefeesArray]);
        $order->setExtensionAttributes($extensionAttributes);

        return;
    }
}
