<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Bakeway\ProductApi\Helper\Data as ProductApiHelper;
use Bakeway\CustomAttributes\Helper\Data as CustomAttributeHelper;

class AdminProductSaveAfter implements ObserverInterface
{
    /**
     * @var ProductApiHelper
     */
    protected $productApiHelper;

    /**
     * @var CustomAttributeHelper
     */
    protected $customAttributeHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CatalogProductSaveAfter constructor.
     * @param ProductApiHelper $productApiHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager,
     * @param CustomAttributeHelper $customAttributeHelper
     */
    public function __construct(
        ProductApiHelper $productApiHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CustomAttributeHelper $customAttributeHelper
    ) {
        $this->productApiHelper = $productApiHelper;
        $this->objectManager = $objectManager;
        $this->customAttributeHelper = $customAttributeHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        /**
         *Creating Custom Options for photo cakes
         */
        $this->productApiHelper->createProductCustomOptions($product->getId());

        /**
         * Setting the product url
         */
        $sellerId = $this->customAttributeHelper->getSellerIdBySku($product->getSku());
        if ($sellerId !== false) {
            $urlKey = $this->productApiHelper->createProductUrl($product->getId());
            $urlProduct = $this->objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
            $urlProduct->setUrlKey($urlKey);
            $urlProduct->save();
        }

        return;
    }
}