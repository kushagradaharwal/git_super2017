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
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartItemExtensionFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Bakeway\HomeDeliveryshipping\Helper\Data as DeliveryShippingHelper;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;

class SalesQuoteCollectionAfter implements ObserverInterface {

    /**
     * @var CartExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @var CartItemExtensionFactory
     */
    protected $itemExtensionFactory;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var DeliveryShippingHelper
     */
    protected $deliveryShippingHelper;

    /**
     * @var ProductImageHelper
     */
    protected $productImageHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Bakeway\ProductApi\Helper\Data
     */
    protected $productapihelper;

    /**
     * @param CartExtensionFactory $extensionFactory
     * @param CartItemExtensionFactory $itemExtensionFactory
     * @param StoreManager $storeManager
     * @param AppEmulation $appEmulation
     * @param DeliveryShippingHelper $deliveryShippingHelper
     * @param ProductImageHelper $productImageHelper
     * @param ProductRepository $productRepository
     */
    public function __construct(
    CartExtensionFactory $extensionFactory, CartItemExtensionFactory $itemExtensionFactory, StoreManager $storeManager, AppEmulation $appEmulation, DeliveryShippingHelper $deliveryShippingHelper, ProductImageHelper $productImageHelper, ProductRepository $productRepository
    , \Bakeway\ProductApi\Helper\Data $productapiHelper) {
        $this->extensionFactory = $extensionFactory;
        $this->itemExtensionFactory = $itemExtensionFactory;
        $this->storeManager = $storeManager;
        $this->appEmulation = $appEmulation;
        $this->deliveryShippingHelper = $deliveryShippingHelper;
        $this->productImageHelper = $productImageHelper;
        $this->productRepository = $productRepository;
        $this->productapihelper = $productapiHelper;
    }

    /**
     * sales quote get after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $quote = $observer->getQuote();

        $extensionAttributes = $quote->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $deliveryType = $quote->getData('delivery_type');
        $deliveryTime = $quote->getData('delivery_time');
        $customerNotes = $quote->getData('customer_notes');
        $storeUniqueName = $quote->getData('store_unique_name');
        $extensionAttributes->setDeliveryType($deliveryType);
        $extensionAttributes->setDeliveryTime($deliveryTime);
        $extensionAttributes->setCustomerNotes($customerNotes);
        $extensionAttributes->setStoreUniqueName($storeUniqueName);
        $quote->setExtensionAttributes($extensionAttributes);

        /**
         * Code to add the items attribute to extension_attributes
         */
        foreach ($quote->getAllItems() as $quoteItem) {
            $itemExtAttr = $quoteItem->getExtensionAttributes();
            if ($itemExtAttr === null) {
                $itemExtAttr = $this->itemExtensionFactory->create();
            }
            $customMessage = $quoteItem->getData('custom_message');
            $itemExtAttr->setCustomMessage($customMessage);

            /**
             * assign business name to quote item
             */
            $sellerId = $this->deliveryShippingHelper->getSellerid($quoteItem->getProductId());
            $shopTitle = $this->deliveryShippingHelper->getSellerShoptitle($sellerId);
            if (isset($shopTitle)):
                $itemExtAttr->setBusinessName($shopTitle);
            endif;

            /**
             * assign image url to quote item
             */
            $sku = $quoteItem->getSku();
            $productObj = $this->productRepository->get($sku);
            $itemImageUrl = $this->getImageUrl($productObj, 'product_page_image_large');
            $itemExtAttr->setItemImageUrl($itemImageUrl);

            $imageTypes = ['image/png', 'image/jpg', 'image/jpeg'];
            $buyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            if (!empty($buyRequest)) {
                $buyRequest = unserialize($buyRequest->getValue());
                if (is_array($buyRequest) && isset($buyRequest['options'])) {
                    $options = $buyRequest['options'];
                    foreach ($options as $option) {
                        if (isset($option['type']) && in_array($option['type'], $imageTypes)) {
                            $quotePath = $option['quote_path'];
                            $mediaUrl = $this->storeManager->getStore()
                                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                            $url = $mediaUrl . $quotePath;
                            $itemExtAttr->setPhotoUrl($url);
                        }
                    }
                }
            }

            /*
             * get and set price
             */
            if ($productObj->getSpecialPrice()) {
                $_ProductPriceSP = $itemExtAttr->setSpecialPrice($this->productapihelper->setDecimalPrice($productObj->getSpecialPrice()));
            } else {
                $_ProductPriceSP = $itemExtAttr->setSpecialPrice('0');
            }

            $priceWithoutTaxNotRound = $this->productapihelper->setDecimalPrice($productObj->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue());
            $_ExclTaxprice = $this->productapihelper->getProductTaxPrice($productObj);

            if (!empty($priceWithoutTaxNotRound)) {
                $_ProductPriceExTax = $itemExtAttr->setPriceExclTax($priceWithoutTaxNotRound);
            } else {
                $_ProductPriceExTax = $itemExtAttr->setPriceExclTax('0');
            }

            if (!empty($_ExclTaxprice)) {
                $_ProductPriceInTax = $itemExtAttr->setPriceInclTax($_ExclTaxprice);
            } else {
                $_ProductPriceInTax = $itemExtAttr->setPriceInclTax('0');
            }

            /*
             * get and set price
             * end
             */

            /*
             * product attribute extension attribute
             * start
             */
            $_ProductApiOp = array();
            $_CfLabel = $this->getAttributeLabel($productObj, 'cake_flavour');
            $_CiLabel = $this->getAttributeLabel($productObj, 'cake_ingredients');
            $_CwLabel = $this->getAttributeLabel($productObj, 'cake_weight');
            $_ProductApiOp['flavour'] = array("label" => $_CfLabel, "values" => array($productObj->getAttributeText("cake_flavour")));
            $_ProductApiOp['ingredients'] = array("label" => $_CiLabel, "values" => array($productObj->getAttributeText("cake_ingredients")));
            $_ProductApiOp['cake_weight'] = array("label" => $_CwLabel, "values" => array($productObj->getAttributeText("cake_weight")));

            $_ProductPriceInTax = $itemExtAttr->setProductAttributes($_ProductApiOp);

            /*
             * product attribute extension attribute
             * end 
             */
            $quoteItem->setExtensionAttributes($itemExtAttr);
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

    /*
     * return attribute label name from attribute code
     * $param $product
     * $param $code
     */

    public function getAttributeLabel($product, $code) {
        $_AttData = $product->getResource()->getAttribute($code)->getStoreLabel();
        return $_AttData;
    }

}
