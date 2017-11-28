<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Observer;

use Magento\Framework\Event\ObserverInterface;
use Bakeway\Vendorcounter\Model\CounterStatusFactory;

class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CounterStatusFactory
     */
    protected $_counterStatusFactory;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CounterStatusFactory $_counterStatusFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_counterStatusFactory = $_counterStatusFactory;
    }

    /**
     * sales order place after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $lastOrderId = $order->getId();

        $orderItems = $order->getAllItems();

        $createdDate = date('Y/m/d', strtotime($order->getCreatedAt()));
        $deliveryDate = date('Y/m/d', strtotime($order->getDeliveryTime()));

        if($createdDate == $deliveryDate) {
            $skus = array();
            foreach($orderItems as $item) {
                $skus[] = $item->getSku();
            }

            if(!empty($skus)) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
                $date = $objDate->gmtDate();
                $current_date = date('Y/m/d');
                $counterModel = $this->_counterStatusFactory->create();
                $counterCollection = $counterModel->getCollection()
                    ->addFieldToFilter('sku', ['in'=>$skus])
                    ->addFieldToFilter('date', $current_date)
                    ->addFieldToSelect(['id']);
                foreach($counterCollection as $counter) {
                    $counterModel = $this->_counterStatusFactory->create()->load($counter->getId());
                    $qty = $counterModel->getQty();
                    $onlineSale = $counterModel->getData('online_sale');
                    $counterModel->setData('online_sale',$onlineSale+1);
                    $counterModel->setQty($qty-1);
                    $counterModel->save();
                }
            }
        }
        return;
    }
}
