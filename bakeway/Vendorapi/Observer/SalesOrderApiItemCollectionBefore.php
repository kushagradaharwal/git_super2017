<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;

class SalesOrderApiItemCollectionBefore implements ObserverInterface {

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $productImageHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var OrderItemExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @var \Bakeway\HomeDeliveryshipping\Helper\Data
     */
    protected $_deliveryshoppinghelper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ProductRepository $productRepository
     * @param \Magento\Catalog\Helper\ImageFactory
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Store\Model\App\Emulation
     * @param OrderItemExtensionFactory $extensionFactory
     * @param Bakeway\HomeDeliveryshipping\Helper\Data $deliveryshoppinghelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager, ProductRepository $productRepository, ProductImageHelper $productImageHelper, StoreManager $storeManager, AppEmulation $appEmulation, OrderItemExtensionFactory $extensionFactory, \Bakeway\HomeDeliveryshipping\Helper\Data $deliveryshoppinghelper
    ) {
        $this->_objectManager = $objectManager;
        $this->productRepository = $productRepository;
        $this->productImageHelper = $productImageHelper;
        $this->storeManager = $storeManager;
        $this->appEmulation = $appEmulation;
        $this->extensionFactory = $extensionFactory;
        $this->_deliveryshoppinghelper = $deliveryshoppinghelper;
    }

    /**
     * sales order place after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $observer->getOrderItemCollection()->addFieldToFilter('parent_item_id', ['null'=>true]);
        return;
    }

}
