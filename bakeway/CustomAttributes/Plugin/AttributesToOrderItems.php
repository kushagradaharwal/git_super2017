<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\CustomAttributes\Plugin;

use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;

class AttributesToOrderItems {
    /*
     * \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
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

    public function __construct(\Magento\Catalog\Api\ProductRepositoryInterface $productRepository, ProductImageHelper $productImageHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Store\Model\App\Emulation $appEmulation
    ) {
        $this->productRepository = $productRepository;
        $this->productImageHelper = $productImageHelper;
        $this->storeManager = $storeManager;
        $this->appEmulation = $appEmulation;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return Item
     */
    public function aroundConvert(
    \Magento\Quote\Model\Quote\Item\ToOrderItem $subject, \Closure $proceed, \Magento\Quote\Model\Quote\Item\AbstractItem $item, $additional = []
    ) {
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
        /** code to set attributes values * */
        $_Sku = $item->getSku();
        $_ProModel = $this->productRepository->get($_Sku);
        $_ItemImageUrl = $this->getImageUrl($_ProModel, 'product_page_image_large');
        $_ItemFlavour = $_ProModel->getAttributeText("cake_flavour");
        $_ItemWeight = $_ProModel->getAttributeText("cake_weight");
        $_ItemIngre = $_ProModel->getAttributeText("cake_ingredients");
        $orderItem->setItemImageUrl($_ItemImageUrl);
        $orderItem->setItemFlavour($_ItemFlavour);
        $orderItem->setItemWeight($_ItemWeight);
        $orderItem->setItemIngredient($_ItemIngre);
        /** code to set attributes values  * */
        $orderItem->setCustomMessage($item->getCustomMessage());
        return $orderItem;
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
