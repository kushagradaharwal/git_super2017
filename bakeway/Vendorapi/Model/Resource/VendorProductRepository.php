<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Model\Resource;

use Bakeway\Vendorapi\Api\VendorProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Webkul\Marketplace\Model\Product as VendorProduct;
use Magento\Framework\App\ResourceConnectionFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Catalog\Model\Product\Visibility as CatalogVisibility;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class VendorProductRepository
 * @package Bakeway\Vendorapi\Model
 */
class VendorProductRepository implements VendorProductRepositoryInterface {

    /**
     * @var ResourceConnectionFactory
     */
    protected $_resourceConnection;

    /**
     * @var ProductCollectionFactory
     */
    protected $_productCollection;

    /**
     * @var VendorProduct
     */
    protected $_vendorProduct;

    /**
     * @var \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $metadataService;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

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
     * @var CatalogVisibility
     */
    protected $catalogVisibility;

    /**
     * @var \Bakeway\ProductApi\Helper\Data
     */
    protected $productapihelper;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * VendorProductRepository constructor.
     *
     * @param ResourceConnectionFactory $_resourceConnection
     * @param ProductCollectionFactory $_productCollection
     * @param VendorProduct $_vendorProduct
     * @param \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param \Magento\Catalog\Helper\ImageFactory
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Store\Model\App\Emulation
     * @param \Bakeway\ProductApi\Helper\Data
     * @param CatalogVisibility $catalogVisibility
     * @param MarketplaceHelper $marketplaceHelper
     */
    public function __construct(
    ResourceConnectionFactory $_resourceConnection, ProductCollectionFactory $_productCollection, VendorProduct $_vendorProduct, \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor, ProductImageHelper $productImageHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Store\Model\App\Emulation $appEmulation, CatalogVisibility $catalogVisibility
    , \Bakeway\ProductApi\Helper\Data $productapiHelper, MarketplaceHelper $marketplaceHelper) {
        $this->_resourceConnection = $_resourceConnection;
        $this->_productCollection = $_productCollection;
        $this->_vendorProduct = $_vendorProduct;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->metadataService = $metadataServiceInterface;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->productImageHelper = $productImageHelper;
        $this->storeManager = $storeManager;
        $this->appEmulation = $appEmulation;
        $this->catalogVisibility = $catalogVisibility;
        $this->productapihelper = $productapiHelper;
        $this->marketplaceHelper = $marketplaceHelper;
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

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
    \Magento\Framework\Api\Search\FilterGroup $filterGroup, Collection $collection
    ) {
        $fields = [];
        $categoryFilter = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

            if ($filter->getField() == 'category_id') {
                $categoryFilter[$conditionType][] = $filter->getValue();
                continue;
            }
            $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
        }

        if ($categoryFilter) {
            $collection->addCategoriesFilter($categoryFilter);
        }

        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }

    /**
     * @param int $vendorId
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return object|array
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts($vendorId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null) {

        $isLiveReady = $this->marketplaceHelper->getIsLiveReady($vendorId);

        if ($isLiveReady === false) {
            throw new LocalizedException(__('Bakery is not live ready'));
        }

        $storeCollection = $this->_vendorProduct->getCollection()
                        ->addFieldToFilter(
                                'seller_id', $vendorId
                        )
                        ->addFieldToFilter(
                                'status', 1
                        )->addFieldToSelect(
                ['mageproduct_id']
        );
        $storeCollection->getSelect()->group('mageproduct_id');

        $storeProductIDs = $storeCollection->getAllIds();

        $collection = $this->_productCollection->create()
                        ->addFieldToFilter(
                                'entity_id', ['in' => $storeProductIDs]
                        )->addFieldToSelect('*');
        $collection->setVisibility($this->catalogVisibility->getVisibleInSiteIds());
        /**
         * Setting sort order according to BKWYADMIN-155
         */
        $collection->addAttributeToSort('likely_available', 'DESC');
        $collection->addAttributeToSort('listing_position', 'ASC');
        $collection->addAttributeToSort('updated_at', 'DESC');
        $this->extensionAttributesJoinProcessor->process($collection);

        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        if ($searchCriteria !== null) {
            //Add filters from root filter group to the collection
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
            /** @var SortOrder $sortOrder */
            foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                        $field, ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
            $collection->setCurPage($searchCriteria->getCurrentPage());
            $collection->setPageSize($searchCriteria->getPageSize());
        }

        $collection->load();

        /* Refining the result */
        $products = array();
        $result = array();
        $i = 0;
        foreach ($collection as $product) {
            $products[$i]['name'] = $product->getName();
            $products[$i]['id'] = $product->getEntityId();
            $products[$i]['type_id'] = $product->getTypeId();
            $products[$i]['sku'] = $product->getSku();
            $products[$i]['price'] = $product->getPrice();
            $_CfLabel = $this->getAttributeLabel($product, 'cake_flavour');
            $_CiLabel = $this->getAttributeLabel($product, 'cake_ingredients');
            $_CwLabel = $this->getAttributeLabel($product, 'cake_weight');
            $_ProCats = $product->getCategoryIds();
            $_CategoryJson = $this->productapihelper->getCategoryName($_ProCats); //tmp
            $_ProductSdes = "";
            if (!empty($_CategoryJson)) {
                $_ProductSdes = $_CategoryJson;
            } else {
                $_ProductSdes = 'null';
            }

            $_typeId = $product->getTypeId();
            $ExtensionAttObject = $product->getExtensionAttributes();
            switch ($_typeId):
                case "simple":

                    $products[$i]['extension_attributes'] = array(
                        "product_attributes" => array(
                            array("label" => $_CfLabel, "values" => array($product->getAttributeText("cake_flavour"))),
                            array("label" => $_CiLabel, "values" => array($product->getAttributeText("cake_ingredients"))),
                            array("label" => $_CwLabel, "values" => array($product->getAttributeText("cake_weight"))),
                        ),
                        "special_price" => number_format($product->getSpecialPrice(), 2),
                        "price_excl_tax" => number_format($product->getPrice(), 2),
                        "price_incl_tax" => number_format($this->productapihelper->getProductTaxPrice($product), 2), //tmp
                        "description" => $product->getDescription(),
                        "short_description" => $product->getShortDescription(),
                        "seo_url" => $this->productapihelper->getProductSeoUrl($product->getId()),
                        "categories" => array(
                            $_ProductSdes
                        ),
                        'media' => array(
                            'small' => $this->getImageUrl($product, 'product_small_image'),
                            'large' => $this->getImageUrl($product, 'product_page_image_large'),
                            'thumb' => $this->getImageUrl($product, 'product_thumbnail_image'),
                    ));
                    break;

                case "configurable": 
                    /* calculaion min price of children products of configurable product */
                    $_minTaxprice = $this->productapihelper->getMinproductPrice($product);
                    $_exclTaxprice = $_minTaxprice['min_price'];
                    $_inclTaxprice = $this->productapihelper->getMinProductTaxPrice($_minTaxprice['product_id']);
                    if (!empty($_exclTaxprice)) {
                        $_ProductPriceExTax = $_exclTaxprice;
                    } else {
                        $_ProductPriceExTax = '0.00';
                    }
                    if (!empty($_inclTaxprice)) {
                        $_ProductPriceInTax = $_inclTaxprice;
                    } else {
                        $_ProductPriceInTax = '0.00';
                    }

                    $products[$i]['extension_attributes'] = array(
                        "product_attributes" => array(
                            array("label" => $_CfLabel, "values" => array($product->getAttributeText("cake_flavour"))),
                            array("label" => $_CiLabel, "values" => array($product->getAttributeText("cake_ingredients"))),
                            array("label" => $_CwLabel, "values" => array($product->getAttributeText("cake_weight"))),
                        ),
                        "special_price" => number_format($product->getSpecialPrice(), 2),
                        "price_excl_tax" => number_format($_ProductPriceExTax,2),
                        "price_incl_tax" => $_ProductPriceInTax, //tmp
                        "description" => $product->getDescription(),
                        "short_description" => $product->getShortDescription(),
                        "seo_url" => $this->productapihelper->getProductSeoUrl($product->getId()),
                        "categories" => array(
                            $_ProductSdes
                        ),
                        'media' => array(
                            'small' => $this->getImageUrl($product, 'product_small_image'),
                            'large' => $this->getImageUrl($product, 'product_page_image_large'),
                            'thumb' => $this->getImageUrl($product, 'product_thumbnail_image'),
                    ));
                    break;
            endswitch;

            $i++;
        }
        $result['products'] = $products;
        $result['current_page'] = $collection->getCurPage();
        $result['total_count'] = $collection->getSize();
        return json_decode(json_encode($result, false));
    }

    /*
     * return attribute label name from attribute code
     */

    public function getAttributeLabel($product, $code) {
        $_AttData = $product->getResource()->getAttribute($code)->getStoreLabel();
        return $_AttData;
    }

}
