<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_ProductApi
 * @author    Bakeway
 */
namespace Bakeway\ProductApi\Model;

use Bakeway\ProductApi\Api\CatalogSeoRepositoryInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\Catalog\Model\ProductFactory;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use \Magento\Framework\Exception\NotFoundException;
use Bakeway\Partnerlocations\Model\ResourceModel\Partnerlocations\CollectionFactory as PartnerLocationsFactory;

class CatalogSeoRepository implements CatalogSeoRepositoryInterface 
{
    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewrite;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var ProductFactory
     */
    protected $catalogProductModel;

    /**
     * @var PartnerLocationsFactory
     */
    protected $partnerLocations;

    /**
     * CatalogSeoRepository constructor.
     * @param UrlRewriteFactory $urlRewrite
     * @param MarketplaceHelper $marketplaceHelper
     * @param ProductFactory $catalogProductModel
     * @param PartnerLocationsFactory $partnerLocations
     */
    public function __construct(
        UrlRewriteFactory $urlRewrite,
        MarketplaceHelper $marketplaceHelper,
        ProductFactory $catalogProductModel,
        PartnerLocationsFactory $partnerLocations
    ) {
        $this->urlRewrite = $urlRewrite;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->catalogProductModel = $catalogProductModel;
        $this->partnerLocations = $partnerLocations;
    }

    /**
     * Get Url Details
     * @param string $url
     * @param string|null $store
     * @return array
     * @throws NotFoundException
     */
    public function getUrlDetails($url, $store = null) {
        $result = [];
        $collection = $this->urlRewrite->create()
                    ->getCollection()
                    ->addFieldToFilter('request_path', strtolower($url))
                    ->addFieldToFilter('target_path', strtolower($url));
        foreach ($collection as $entity) {
            if ($entity->getEntityType() == 'bakeway-product') {
                $details = $this->getSellerProductDetails($entity->getEntityId());
                if (isset($details['partner_id'])) {
                    $result['partner_id'] = $details['partner_id'];
                }
                if (isset($details['product_id'])) {
                    $result['product_id'] = $details['product_id'];
                }
                if (isset($details['sku'])) {
                    $result['sku'] = $details['sku'];
                }
            } elseif ($entity->getEntityType() == 'customer') {
                $details = $this->getSellerDetails($entity->getEntityId());
                if (isset($details['partner_id'])) {
                    $result['partner_id'] = $details['partner_id'];
                }
            }
        }
        if (isset($result['partner_id'])) {
            $isConglomerate = $this->marketplaceHelper->isConglomerate($result['partner_id']);
            if ($isConglomerate === true) {
                if ($store == null || trim($store) == '') {
                    throw new NotFoundException(__('Requested url does not match any route.'));
                }
                $locations = $this->partnerLocations->create()
                    ->addFieldToFilter('seller_id', $result['partner_id'])
                    ->addFieldToFilter('store_unique_name', $store);
                if ($locations->count() <= 0) {
                    throw new NotFoundException(__('Requested url does not match any route.'));
                }
            } else {
                if ($store !== null || trim($store) != '') {
                    throw new NotFoundException(__('Requested url does not match any route.'));
                }
            }

        } else {
            throw new NotFoundException(__('Requested url does not match any route.'));
        }
        if (empty($result)) {
            throw new NotFoundException(__('Requested url does not match any route.'));
        }
        return json_decode(json_encode($result, false));
        //return $result;
    }

    /**
     * @param int $sellerId
     * @return mixed
     */
    public function getSellerDetails($sellerId) {
        $result = [];
        $seller = $this->marketplaceHelper->getSellerDataBySellerId($sellerId)->getFirstItem();
        $sellerId = $seller->getData('seller_id');
        if (isset($sellerId)) {
            $result['partner_id'] = $sellerId;
        }
        return $result;
    }

    /**
     * @param int $productId
     * @return mixed
     */
    public function getSellerProductDetails($productId) {
        $result = [];
        $marketplaceProduct = $this->marketplaceHelper->getSellerProductDataByProductId($productId)->getFirstItem();
        $mageProductId = $marketplaceProduct->getData('mageproduct_id');
        if (isset($mageProductId)) {
            $product = $this->catalogProductModel->create()
                ->load($mageProductId);
            $result['sku'] = $product->getData('sku');
            $result['partner_id'] = $marketplaceProduct->getData('seller_id');
            $result['product_id'] = $mageProductId;
        }
        return $result;
    }
    
    
}