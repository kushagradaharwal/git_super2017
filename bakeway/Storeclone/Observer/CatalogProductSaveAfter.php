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
use Webkul\Marketplace\Model\Seller as SellerModel;
use Webkul\Marketplace\Model\Product as SellerProductModel;
use Magento\Catalog\Model\ProductFactory;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Bakeway\ProductApi\Helper\Data as ProductApiHelper;

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var SellerModel
     */
    protected $sellerModel;

    /**
     * @var SellerProductModel
     */
    protected $sellerProductModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var IndexerCollectionFactory
     */
    protected $indexerCollectionFactory;

    /**
     * @var ProductApiHelper
     */
    protected $productApiHelper;

    /**
     * CatalogProductSaveAfter constructor.
     * @param SellerModel $sellerModel
     * @param SellerProductModel $sellerProductModel
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param ProductFactory $productFactory
     * @param IndexerFactory $indexerFactory
     * @param IndexerCollectionFactory $indexerCollectionFactory
     * @param ProductApiHelper $productApiHelper
     */
    public function __construct(
        SellerModel $sellerModel,
        SellerProductModel $sellerProductModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        IndexerFactory $indexerFactory,
        IndexerCollectionFactory $indexerCollectionFactory,
        ProductApiHelper $productApiHelper
    )
    {
        $this->sellerModel = $sellerModel;
        $this->sellerProductModel = $sellerProductModel;
        $this->_date = $date;
        $this->productFactory = $productFactory;
        $this->objectManager = $objectManager;
        $this->indexerFactory = $indexerFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->productApiHelper = $productApiHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $bunch = $observer->getBunch();
        foreach ($bunch as $row) {
            $sku = $row['sku'];
            $sellerId = $row['seller_id'];
            $product = $this->productFactory->create();
            $productId = $product->getIdBySku($sku);
            if ($productId) {
                $product->load($productId);
                $newProductId = $productId;

                $sellerExist = $this->isSellerExist($sellerId);

                if ($sellerExist > 0) {
                    $assignProduct = 1;
                    $collection = $this->sellerProductModel->getCollection()
                        ->addFieldToFilter('mageproduct_id', $newProductId);
                    foreach ($collection as $sellerProduct) {
                        $assignProduct = 0;
                        if ($sellerId == $sellerProduct['seller_id']) {
                            $sellerProduct->setAdminassign(1);
                            $sellerProduct->setAdminPendingNotification(2);
                            $sellerProduct->save();
                        }
                    }

                    if ($assignProduct) {
                        $sellerProductFactory = $this->objectManager->create(
                            'Webkul\Marketplace\Model\Product'
                        );
                        $sellerProductFactory->setMageproductId($newProductId);
                        $sellerProductFactory->setSellerId($sellerId);
                        $sellerProductFactory->setStatus(2);
                        $sellerProductFactory->setAdminassign(1);
                        $sellerProductFactory->setAdminPendingNotification(2);
                        $sellerProductFactory->setCreatedAt($this->_date->gmtDate());
                        $sellerProductFactory->setUpdatedAt($this->_date->gmtDate());
                        $sellerProductFactory->save();
                    }
                    /**
                     * Setting SKU for Bakeway
                     */
                    $product->setSku($sellerId."-".$productId);

                    /**
                     * Setting the product url
                     */
                    $urlKey = $this->productApiHelper->createProductUrl($productId);
                    $product->setUrlKey($urlKey);

                    $product->save();

                    /**
                     *Creating Custom Options for photo cakes
                     */
                    $this->productApiHelper->createProductCustomOptions($productId);
                }
            }
        }
    }

    public function isSellerExist($sellerId)
    {
        $model = $this->sellerModel
                ->getCollection()
                ->addFieldToFilter('seller_id', $sellerId);
        return $model->getSize();
    }
}