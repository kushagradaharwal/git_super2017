<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Helper;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Webkul\Marketplace\Model\Product as VendorProduct;
use Bakeway\ProductApi\Helper\Data as ProductApiHelper;

/**
 * Bakeway VendorNotifcation Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\Product\Copier
     */
    protected $productCopier;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollection;

    /**
     *@var VendorProduct
     */
    protected $vendorProduct;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var ProductApiHelper
     */
    protected $productApiHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product\Copier $productCopier
     * @param ProductCollectionFactory $productCollection
     * @param VendorProduct $vendorProduct
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param ProductApiHelper $productApiHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Copier $productCopier,
        ProductCollectionFactory $productCollection,
        VendorProduct $vendorProduct,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        ProductApiHelper $productApiHelper
    ) {
        $this->productCopier = $productCopier;
        $this->productCollection = $productCollection;
        $this->vendorProduct = $vendorProduct;
        $this->objectManager = $objectManager;
        $this->_date = $date;
        $this->productApiHelper = $productApiHelper;
        parent::__construct($context);
    }

    /**
     * @param int $storeId
     * @param int $id Customer'sId
     * @return void
     */
    public function cloneStoreByStoreId($storeId, $id)
    {
        $storeCollection = $this->vendorProduct->getCollection()
            ->addFieldToFilter(
                'seller_id',
                $storeId
            )->addFieldToSelect(
                ['mageproduct_id']
            );
        $storeCollection->getSelect()->group('mageproduct_id');

        $storeProductIDs = $storeCollection->getAllIds();

        $collection = $this->productCollection->create()
            ->addFieldToFilter(
                'entity_id',
                ['in'=>$storeProductIDs]
            )->addFieldToSelect('*');

        $configurableAssociatedIdsArr = [];
        $oldNewSimpleProductIds = [];
        $newConfigChildren = [];
        $review = $this->scopeConfig->getValue('vendor_app_settings/storeclone/store_clone_review', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        foreach ($collection as $product) {
            $isCustomOptions = false;
            $oldProduct = $this->objectManager
                ->create('Magento\Catalog\Model\Product')
                ->load($product->getId());
            $customOptions = $this->objectManager->get('Magento\Catalog\Model\Product\Option')
                            ->getProductOptionCollection($oldProduct);
            if ($customOptions->count() > 0) {
                $isCustomOptions = true;
            }
            $typeId = $oldProduct->getTypeId();

            $newProduct = $this->productCopier->copy($oldProduct);
            $newProductId = $newProduct->getId();
            $newProduct->setStatus(1);
            /*if($review){
                $newProduct->setStatus(2);
            }else{
                $newProduct->setStatus(1);
            }*/
            $newProduct->setStatus(1);
            $newProduct->setWebsiteIds(array(1));
            $newProduct->setMediaGallery(array());
            $newProduct->setStockData(array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 0,
                    'is_in_stock' => 1,
                    'qty' => 1
                )
            );
            $newProduct->save();
            /**
             * Setting SKU for Bakeway
             */
            $newProduct->setSku($id."-".$newProductId);
            $newProduct->save();

            if ($typeId == 'simple') {
                $oldNewSimpleProductIds[$oldProduct->getSku()] = $newProduct->getId();
            }

            if ($newProductId) {
                $sellerProduct = $this->objectManager->create(
                    'Webkul\Marketplace\Model\Product'
                );
                $sellerProduct->setMageproductId($newProductId);
                $sellerProduct->setSellerId($id);
                $sellerProduct->setStatus($newProduct->getStatus());
                $sellerProduct->setAdminassign(1);
                if ($review) {
                    $sellerProduct->setAdminPendingNotification(2);
                    $sellerProduct->setStatus(2);
                }
                $sellerProduct->setCreatedAt($this->_date->gmtDate());
                $sellerProduct->setUpdatedAt($this->_date->gmtDate());
                $sellerProduct->save();

                /**
                 * Setting the product url
                 */
                $urlKey = $this->productApiHelper->createProductUrl($newProductId);
                $newProduct->setUrlKey($urlKey);
                $newProduct->save();

                if ($isCustomOptions === true) {
                    $this->productApiHelper->createProductCustomOptions($newProductId);
                }

                if ($typeId == 'configurable') {
                    $skuArr = [];
                    $children = $newProduct->getTypeInstance()->getUsedProducts($newProduct);
                    foreach ($children as $child) {
                        $skuArr[] = $child->getSku();
                    }
                    $newConfigChildren[$newProduct->getSku()] = $skuArr;
                }
            }
        }
        foreach ($newConfigChildren as $sku=>$childs) {
            if (is_array($childs) && !empty($childs)) {
                $idsToAssociates = [];
                foreach ($childs as $simpleProductSku) {
                    if (isset($oldNewSimpleProductIds[$simpleProductSku])) {
                        $idsToAssociates[] = $oldNewSimpleProductIds[$simpleProductSku];
                    } else {
                        $newSimpleId = $this->createNewSimpleProduct($simpleProductSku, $id);
                        if ($newSimpleId !== false) {
                            $idsToAssociates[] = $newSimpleId;
                        }
                    }
                }
                $productModel = $this->objectManager
                    ->create('Magento\Catalog\Model\Product');
                $prodId = $productModel->getIdBySku($sku);
                if (isset($prodId) && $prodId != '') {
                    $configurableProduct = $productModel->load($prodId);
                    $configurableProduct->setAssociatedProductIds($idsToAssociates);
                    $configurableProduct->save();
                }

            }
        }
        return;
    }

    /**
     * @param string $simpleProductSku
     * @return bool|int
     */
    public function createNewSimpleProduct($simpleProductSku, $sellerId)
    {
        $productModel = $this->objectManager
            ->create('Magento\Catalog\Model\Product');
        $id = $productModel->getIdBySku($simpleProductSku);
        $review = $this->scopeConfig->getValue('vendor_app_settings/storeclone/store_clone_review', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($id) && $id != '') {
            $isCustomOptions = false;
            $oldProduct = $productModel->load($id);
            $customOptions = $this->objectManager->get('Magento\Catalog\Model\Product\Option')
                ->getProductOptionCollection($oldProduct);
            if ($customOptions->count() > 0) {
                $isCustomOptions = true;
            }

            $newProduct = $this->productCopier->copy($oldProduct);
            $newProductId = $newProduct->getId();
            $newProduct->setStatus(1);
            $newProduct->setStatus(1);
            $newProduct->setWebsiteIds(array(1));
            $newProduct->setMediaGallery(array());
            $newProduct->setStockData(array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 0,
                    'is_in_stock' => 1,
                    'qty' => 1
                )
            );
            $newProduct->save();
            /**
             * Setting SKU for Bakeway
             */
            $newProduct->setSku($sellerId."-".$newProductId);
            $newProduct->save();

            if ($newProductId) {
                $sellerProduct = $this->objectManager->create(
                    'Webkul\Marketplace\Model\Product'
                );
                $sellerProduct->setMageproductId($newProductId);
                $sellerProduct->setSellerId($sellerId);
                $sellerProduct->setStatus($newProduct->getStatus());
                $sellerProduct->setAdminassign(1);
                if ($review) {
                    $sellerProduct->setAdminPendingNotification(2);
                    $sellerProduct->setStatus(2);
                }
                $sellerProduct->setCreatedAt($this->_date->gmtDate());
                $sellerProduct->setUpdatedAt($this->_date->gmtDate());
                $sellerProduct->save();

                /**
                 * Setting the product url
                 */
                $urlKey = $this->productApiHelper->createProductUrl($newProductId);
                $newProduct->setUrlKey($urlKey);
                $newProduct->save();

                if ($isCustomOptions === true) {
                    $this->productApiHelper->createProductCustomOptions($newProductId);
                }

                return $newProductId;
            }
            return false;
        }
        return false;
    }
}
