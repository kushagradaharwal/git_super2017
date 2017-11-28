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

class SalesOrderItemCollectionAfter implements ObserverInterface {

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
        $itemCollection = $observer->getOrderItemCollection();
        foreach ($itemCollection as $item) {
            $product = $this->productRepository->create()->getById($item->getProductId());
            $extensionAttributes = $item->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->extensionFactory->create();
            }

            $cakeFlavour = $item->getData('item_flavour');
            $cakeWeight = $item->getData('item_weight');
            $cakeIngredient = $item->getData('item_ingredient');
            $cakeImageUrl = $item->getData('item_image_url');

            if (isset($cakeFlavour)) {
                $extensionAttributes->setItemFlavour($cakeFlavour);
            }

            if (isset($cakeWeight)) {
                $extensionAttributes->setItemWeight($cakeWeight);
            }

            if (isset($cakeIngredient)) {
                $extensionAttributes->setItemIngredient($cakeIngredient);
            }

            if (isset($cakeImageUrl)) {
                $extensionAttributes->setItemImageUrl($cakeImageUrl);
            }

            $extensionAttributes->setCustomMessage($item->getCustomMessage());

            /* shop title */
            $_Sellerid = $this->_deliveryshoppinghelper->getSellerid($item->getProductId());
            $_Shoptitle = $this->_deliveryshoppinghelper->getSellerShoptitle($_Sellerid);
            if (isset($_Shoptitle)):
                $extensionAttributes->setBusinessName($_Shoptitle);
            endif;

            $imageTypes = ['image/png', 'image/jpg', 'image/jpeg'];
            $productOptions = $item->getProductOptions();
            if (!empty($productOptions) && isset($productOptions['info_buyRequest'])) {
                $buyRequest = $productOptions['info_buyRequest'];
                if (is_array($buyRequest) && isset($buyRequest['options'])) {
                    $options = $buyRequest['options'];
                    foreach ($options as $option) {
                        if (isset($option['type']) && in_array($option['type'], $imageTypes)) {
                            $quotePath = $option['order_path'];
                            $mediaUrl = $this->storeManager->getStore()
                                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                            $url = $mediaUrl . $quotePath;
                            $extensionAttributes->setPhotoUrl($url);
                        }
                    }
                }
            }

            $item->setExtensionAttributes($extensionAttributes);
        }
        return;
    }

    /**
     * Helper function that provides full cache image url
     * @param \Magento\Catalog\Model\Product
     * @return string
     */
    protected function getImageUrl($product, string $imageType = '') {
        $storeId = $this->storeManager->getStore()->getId();

        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $imageUrl = $imageUrl = $this->productImageHelper->create()->init($product, $imageType)->getUrl();

        $this->appEmulation->stopEnvironmentEmulation();

        return $imageUrl;
    }

}
