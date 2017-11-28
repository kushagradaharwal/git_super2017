<?php

namespace Bakeway\ProductApi\Helper;

use Braintree\Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Bakeway\Cities\Helper\Data as CitiesHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavAttribute;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const PRODUCT_ENABLE_STATUS = 1;
    const URL_KEY_ATTRIBUTE_CODE = 'url_key';
    const PRODUCT_VARCHAR_ENTITY_TABLE = 'catalog_product_entity_varchar';
    const PRODUCT_OPTION_TABLE = 'catalog_product_option';
    const PRODUCT_OPTION_TITLE_TABLE = 'catalog_product_option_title';

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var  \Magento\Customer\Model\CustomerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $productFactory;
    protected $vendorFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurableobj;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @param productRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Bakeway\HomeDeliveryshipping\Helper\Data
     */
    protected $_homedeliveryHelper;

    /**
     * @var $categoryModel
     */
    protected $_categoryModel;

    /**
     * @var $categoryfactory
     */
    protected $_categoryfactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ProductCustomOptionInterface
     */
    protected $customOptionRepository;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var CitiesHelper
     */
    protected $citiesHelper;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var EavAttribute
     */
    protected $eavAttribute;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Customer\Model\CustomerFactory $sellerFactory, \Webkul\Marketplace\Model\SellerFactory $vendorFactory, \Webkul\Marketplace\Model\ProductFactory $productFactory, \Magento\ConfigurableProduct\Model\Product\Type\Configurable $_configurableobj, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Helper\Data $catalogData, \Magento\Customer\Model\AddressFactory $addressFactory, \Bakeway\HomeDeliveryshipping\Helper\Data $homedeliveryHelper, \Magento\Catalog\Model\CategoryRepository $categoryModel, \Magento\Catalog\Model\CategoryFactory $categoryfactory, CategoryRepositoryInterface $categoryRepository, ProductCustomOptionInterface $customOptionRepository, ObjectManagerInterface $objectManager, CitiesHelper $citiesHelper, EavAttribute $eavAttribute, MarketplaceHelper $marketplaceHelper
    ) {
        parent::__construct($context);
        $this->_sellerFactory = $sellerFactory;
        $this->vendorFactory = $vendorFactory;
        $this->productFactory = $productFactory;
        $this->_configurableobj = $_configurableobj;
        $this->productRepository = $productRepository;
        $this->_catalogHelper = $catalogData;
        $this->_addressFactory = $addressFactory;
        $this->_homedeliveryHelper = $homedeliveryHelper;
        $this->_categoryModel = $categoryModel;
        $this->_categoryfactory = $categoryfactory;
        $this->categoryRepository = $categoryRepository;
        $this->customOptionRepository = $customOptionRepository;
        $this->objectManager = $objectManager;
        $this->citiesHelper = $citiesHelper;
        $this->eavAttribute = $eavAttribute;
        $this->marketplaceHelper = $marketplaceHelper;
    }

    /*
     * get vendor name from product id
     */

    public function getSellername($prodid) {
        $_collection = $this->productFactory->create()->getCollection();
        $_collection->getSelect()->joinLeft(['mp_userdata' => $_collection->getTable('marketplace_userdata')], 'main_table.seller_id = mp_userdata.seller_id', ['business_name'])
                ->where('main_table.mageproduct_id=' . $prodid);

        if (!empty($_collection->getSize())) {
            return $_collection->getFirstItem()->getData('business_name');
        } else {
            return NULL;
        }
    }

    /*
     * get delivery method on or off of vendor from product id
     */

    public function getSellerDeliveryStatus($prodid) {
        $_collection = $this->productFactory->create()->getCollection();
        $_collection->getSelect()->joinLeft(['mp_userdata' => $_collection->getTable('marketplace_userdata')], 'main_table.seller_id = mp_userdata.seller_id', ['delivery'])
                ->where('main_table.mageproduct_id=' . $prodid);
        if (!empty($_collection->getSize())) {
            return $_collection->getFirstItem()->getData('delivery');
        } else {
            return NULL;
        }
    }

    /*
     * get all child product skus
     */

    public function getChildrenSkus($product) {
        $_Sku = array();
        $_ChildProducts = $this->_configurableobj->getUsedProductCollection($product);
        $_ChildProducts
                ->setFlag('has_stock_status_filter', true)
                ->addAttributeToSelect($this->getCatalogConfig()->getProductAttributes())
                ->addFilterByRequiredOptions()
                ->setStoreId($product->getStoreId());
        foreach ($_ChildProducts as $simple_product) {
            $_Sku[] = $simple_product->getSku();
        }
        return $_Sku;
    }

    /**
     * Get Config instance
     * @return Config
     * @deprecated
     */
    private function getCatalogConfig() {
        if (!$this->catalogConfig) {
            $this->catalogConfig = ObjectManager::getInstance()->get(Config::class);
        }

        return $this->catalogConfig;
    }

    /*
     * filtering sku for options
     * return avaiable child sku 
     */

    public function getOptionsSkus($value_index, $_ChildSkus, $product) {


        $_Sku = $_MainSku = array();
        $_caleflv = $_caleIng = $_caleWeight = [];
        $_ChildProducts = $this->_configurableobj->getUsedProductCollection($product);

        $_ChildProducts
                ->setFlag('has_stock_status_filter', true)
                ->addAttributeToSelect($this->getCatalogConfig()->getProductAttributes())
                ->addFilterByRequiredOptions()
                ->addAttributeToFilter('status', self::PRODUCT_ENABLE_STATUS)
                ->setStoreId($product->getStoreId());
        foreach ($_ChildProducts as $simple_product) {
            $_caleflv[$simple_product->getSku()] = $simple_product->getCakeFlavour();
            $_caleIng[$simple_product->getSku()] = $simple_product->getCakeIngredients();
            $_caleWeight[$simple_product->getSku()] = $simple_product->getCakeWeight();
            $_SKus[] = $simple_product->getSku();
        }
        if (in_array($value_index, $_caleflv)) {
            if (in_array($value_index, $_caleflv)) {
                foreach ($_caleflv as $key => $_caleflv1) {
                    if ($_caleflv1 == $value_index) {
                        $_MainSku[] = $key;
                    }
                }
            }
        }

        if (in_array($value_index, $_caleIng)) {
            if (in_array($value_index, $_caleIng)) {
                foreach ($_caleIng as $key => $_caleIng1) {
                    if ($_caleIng1 == $value_index) {
                        $_MainSku[] = $key;
                    }
                }
            }
        }

        if (in_array($value_index, $_caleWeight)) {
            if (in_array($value_index, $_caleWeight)) {
                foreach ($_caleWeight as $key => $_caleWeight1) {
                    if ($_caleWeight1 == $value_index) {
                        $_MainSku[] = $key;
                    }
                }
            }
        }

        return $_MainSku;
    }

    /*
     * child sku
     * simple, special price
     * price excl tax
     */

    public function getSkusPrice($product) {
        $_Sku = array();
        $_ChildProducts = $this->_configurableobj->getUsedProductCollection($product);
        $_ChildProducts
                ->setFlag('has_stock_status_filter', true)
                ->addAttributeToSelect($this->getCatalogConfig()->getProductAttributes())
                ->addFilterByRequiredOptions()
                ->addAttributeToFilter('status', self::PRODUCT_ENABLE_STATUS)
                ->setStoreId($product->getStoreId());
        foreach ($_ChildProducts as $simple_product) {

            $priceExTax = number_format($simple_product->getPrice(), 2);
            /* check if product has special price */
            if ($simple_product->getSpecialPrice()):
                $priceExTax = number_format($simple_product->getSpecialPrice(), 2);
            endif;
            $priceIncTax = number_format($this->getProductTaxPrice($simple_product), 2);

            $_Sku[$simple_product->getSku()] = array(
                "price" => number_format($simple_product->getPrice(), 2),
                "special_price" => number_format($simple_product->getSpecialPrice(), 2),
                "price_excl_tax" => $priceExTax,
                "price_incl_tax" => $priceIncTax);
        }
        return $_Sku;
    }

    /*
     * calclating price with including tax
     * return @including tax price
     */

    public function getProductTaxPrice($product) {
        $_CatalogHelper = $this->_catalogHelper;
        $_Price = $product->getPrice();

        /** check if product has special price* */
        if ($product->getSpecialPrice()):
            $_Price = $product->getSpecialPrice();
        endif;

        /** get seller region and postcode * */
        $_SellerId = $this->_homedeliveryHelper->getSellerid($product->getId());
        $_SellerInfo = $this->_homedeliveryHelper->getSellerDetails($_SellerId);
        $_Zip = $_Country = $_City = $_State = "";
        if (!empty($_SellerInfo)):
            $_Zip = $_SellerInfo->getData('store_zipcode');
            $_Country = $_SellerInfo->getData('country_pic');
            $_City = $_SellerInfo->getData('store_city');
            $_State = $_SellerInfo->getData('state');
        endif;
        $address = $this->_addressFactory->create();
        $address->setCountryId($_Country)
                ->setFirstname("FirstName")
                ->setLastname("Lastname")
                ->setTelephone('1234567890')
                ->setPostcode($_Zip)
                ->setCity($_City)
                ->setStreet('Pune');

        $_Price = $_CatalogHelper->getTaxPrice($product, $_Price, true, $address);
        if (!empty($_Price)):
            return $_Price;
        else:
            return;
        endif;
    }

    /*
     * return category name from category id
     */

    public function getCategoryName($_Ids) {
        $_Model = $this->_categoryModel;
        $_ReturnData = [];
        $_PcakeFlag = "";
        if (!empty($_Ids)):
            foreach ($_Ids as $id) {
                $_getCategoryData = $_Model->get($id);
                $_Name = $_getCategoryData->getName();
                if ($_Name == 'Photo cake'):
                    $_ReturnData[] = array("id" => $id, "label" => $_Name, "upload_media" => true);
                else:
                    $_ReturnData[] = array("id" => $id, "label" => $_Name);
                endif;
            }
            return $_ReturnData;
        else:
            return;
        endif;
    }

    /*
     * return main cake category id and add on category id
     */

    public function getMainandAddoncategory() {

        $_CatArray = [];
        $_catname = "";
        $_Collection = $this->_categoryfactory->create()->getCollection()
                ->addAttributeToFilter(array(
            array('attribute' => 'name', 'eq' => 'Cakes'),
            array('attribute' => 'name', 'eq' => 'Add ons')
        ));
        foreach ($_Collection as $category):
            if ($category->getName() == 'Cakes'):
                $_catname = "cakes";
            else:
                $_catname = "addons";
            endif;
            $_CatArray[$_catname] = array("id" => $category->getId(), "label" => $category->getName(), "upload_media" => true);
        endforeach;
        return !empty($_CatArray) ? $_CatArray : "";
    }

    public function getSellerCity($productId) {
        $_collection = $this->productFactory->create()->getCollection();
        $_collection->getSelect()->joinLeft(['mp_userdata' => $_collection->getTable('marketplace_userdata')], 'main_table.seller_id = mp_userdata.seller_id', ['store_city'])
                ->where('main_table.mageproduct_id=' . $productId);

        if (!empty($_collection->getSize())) {
            $cityId = $_collection->getFirstItem()->getData('store_city');
            if ($cityId) {
                $city = $this->citiesHelper->getCityNameById($cityId);
                return $city;
            } else {
                return null;
            }
        }
        return null;
    }

    public function getSellerLocalityArea($productId) {
        $_collection = $this->productFactory->create()->getCollection();
        $_collection->getSelect()->joinLeft(['mp_userdata' => $_collection->getTable('marketplace_userdata')], 'main_table.seller_id = mp_userdata.seller_id', ['store_locality_area'])
                ->where('main_table.mageproduct_id=' . $productId);

        if (!empty($_collection->getSize())) {
            $locality = $_collection->getFirstItem()->getData('store_locality_area');
            if ($locality) {
                return $locality;
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * @param int $productId
     * @return void
     */
    public function createProductCustomOptions($productId) {
        if ($productId !== null || $productId != '') {
            $product = $this->objectManager->create('Magento\Catalog\Model\Product')
                    ->load($productId);
            $isCustomOption = $this->checkProductCategories($product);

            /**
             * Delete the product options if any
             */
            if ($product->getOptions() != '') {
                foreach ($product->getOptions() as $opt) {
                    $opt->delete();
                }
                $product->setHasOptions(0)->save();
            }

            if ($isCustomOption === true) {
                /**
                 * Create product option of file type.
                 */
                $customOption = $this->customOptionRepository;
                $customOption->setTitle('Photo Url')
                        ->setType('file')
                        ->setIsRequire(true)
                        ->setSortOrder(1)
                        ->setPriceType('fixed')
                        ->setProductSku($product->getSku());
                $customOptions[] = $customOption;
                $this->objectManager->create('Magento\Catalog\Model\Product')
                        ->load($productId)
                        ->setOptions($customOptions)
                        ->setHasOptions(1)
                        ->save();
                return;
            }
        }
        return;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     */
    public function checkProductCategories($product) {
        $categories = $product->getCategoryIds();
        foreach ($categories as $category) {
            $cat = $this->categoryRepository->get($category)->getName();
            if (strpos(strtolower($cat), 'photo') !== false) {
                return true;
            }
        }
        return false;
    }

    /*
     * set 2 decimal for product price.
     * @param $_Price
     */

    public function setDecimalPrice($_Price) {
        if ($_Price) {
            return number_format($_Price, 2);
        } else {
            return;
        }
    }

    /**
     * function to create product SEO urls
     * @param int $productId
     * @return string
     */
    public function createProductUrl($productId) {
        $sellerName = $this->getSellername($productId);

        /*         * * Removing default magento urls ** */
        $this->removeDefaultUrlRewrites($productId);

        /*         * * creating bakeway product seo url ** */
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
                ->load($productId);
        $productName = $product->getName();
        $sellerCity = $this->getSellerCity($productId);
        $locality = $this->getSellerLocalityArea($productId);

        $url = $this->getProcessedProductUrl($sellerName, $productName, $sellerCity, $locality);
        $initialCounter = 0;
        $counterIncrement = $this->checkProductUrlForDuplicates($productId, $url, $initialCounter);
        if ($counterIncrement !== 0) {
            while ($counterIncrement !== $initialCounter) {
                $initialCounter++;
                $url = $this->getProcessedProductUrl($sellerName, $productName, $sellerCity, $locality, $counterIncrement);
                $counterIncrement = $this->checkProductUrlForDuplicates($productId, $url, $initialCounter);
            }
            $url = $this->getProcessedProductUrl($sellerName, $productName, $sellerCity, $locality, $counterIncrement);
        }
        /*         * * adding bakeway product seo url to url_rewrite ** */
        $urlId = '';
        $collectionRequestUrl = '';
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('request_path', strtolower($url))
                ->addFieldToFilter('target_path', strtolower($url));
        foreach ($urlCollectionData as $value) {
            $urlId = $value->getId();
            $collectionRequestUrl = $value->getRequestPath();
        }
        if ($collectionRequestUrl != $url) {
            $idPath = rand(1, 100000);
            $this->objectManager->create('Magento\UrlRewrite\Model\UrlRewrite')
                    ->load($urlId)
                    ->setIsSystem(0)
                    ->setEntityType('bakeway-product')
                    ->setEntityId($productId)
                    ->setIdPath($idPath)
                    ->setTargetPath(strtolower($url))
                    ->setRequestPath(strtolower($url))
                    ->save();
        }
        return $url;
    }

    /**
     * Function to remove default magento's url rewrites
     * @param int $productId
     * @return void
     */
    public function removeDefaultUrlRewrites($productId) {
        $attributeId = $this->eavAttribute->getIdByCode('catalog_product', self::URL_KEY_ATTRIBUTE_CODE);
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('entity_type', 'product')
                ->addFieldToFilter('entity_id', $productId);
        foreach ($urlCollectionData as $value) {
            $value->delete();
        }

        $resource = $this->objectManager
                ->create('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName(self::PRODUCT_VARCHAR_ENTITY_TABLE);
        $sql = "DELETE FROM " . $tableName . " WHERE attribute_id = " . $attributeId . " AND entity_id = " . $productId;
        $connection->query($sql);

        return;
    }

    /**
     * function to create seller SEO urls
     * @param int $sellerId
     * @return string
     */
    public function createVendorUrl($sellerId) {
        $seller = $this->marketplaceHelper->getSellerDataBySellerId($sellerId)->getFirstItem();

        /*         * * Removing seller old urls ** */
        $this->removeSellerOldUrlRewrites($sellerId);

        /*         * * creating bakeway seller seo url ** */
        $cityId = $seller->getData('store_city');
        $sellerCity = $this->citiesHelper->getCityNameById($cityId);
        $locality = $seller->getData('store_locality_area');
        $sellerName = $seller->getData('business_name');

        $url = $this->getProcessedVendorUrl($sellerName, $locality, $sellerCity);
        $initialCounter = 0;
        $counterIncrement = $this->checkVendorUrlForDuplicates($sellerId, $url, $initialCounter);
        if ($counterIncrement !== 0) {
            while ($counterIncrement !== $initialCounter) {
                $initialCounter++;
                $url = $this->getProcessedVendorUrl($sellerName, $locality, $sellerCity, $counterIncrement);
                $counterIncrement = $this->checkVendorUrlForDuplicates($sellerId, $url, $initialCounter);
            }
            $url = $this->getProcessedVendorUrl($sellerName, $locality, $sellerCity, $counterIncrement);
        }
        if ($url) {
            /*             * * adding bakeway seller seo url to url_rewrite ** */
            $idPath = rand(1, 100000);
            $this->objectManager->create('Magento\UrlRewrite\Model\UrlRewrite')
                    ->setIsSystem(0)
                    ->setEntityType('customer')
                    ->setEntityId($sellerId)
                    ->setIdPath($idPath)
                    ->setTargetPath(strtolower($url))
                    ->setRequestPath(strtolower($url))
                    ->save();
        }
        return $url;
    }

    /**
     * Function to remove old url rewrites for seller
     * @param int $sellerId
     * @return void
     */
    public function removeSellerOldUrlRewrites($sellerId) {
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('entity_type', 'customer')
                ->addFieldToFilter('entity_id', $sellerId);
        foreach ($urlCollectionData as $value) {
            $value->delete();
        }
        return;
    }

    /**
     * Check if url is already exist with another user
     * Return the counter increment if it exist
     * @param int $sellerId
     * @param string $url
     * @param int $initialCounter
     * @return int
     */
    public function checkVendorUrlForDuplicates($sellerId, $url, $initialCounter) {
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('entity_type', 'customer')
                ->addFieldToFilter('request_path', $url)
                ->addFieldToFilter('target_path', $url);
        $counterIncrement = $initialCounter;
        foreach ($urlCollectionData as $value) {
            $entityId = $value->getEntityId();
            if ($entityId != $sellerId) {
                $counterIncrement++;
            }
        }
        return $counterIncrement;
    }

    public function getProcessedVendorUrl($sellerName, $locality, $sellerCity, $counterIncrement = 0) {
        if ($counterIncrement == 0) {
            $urlString = preg_replace('#[^0-9a-z]+#i', '-', $sellerName . " " . $locality);
        } else {
            $urlString = preg_replace('#[^0-9a-z]+#i', '-', $sellerName . " " . $counterIncrement . " " . $locality);
        }
        $url = $sellerCity . "-" . $urlString;
        $url = strtolower($url);
        return $url;
    }

    public function getProcessedProductUrl($sellerName, $productName, $sellerCity, $locality, $counterIncrement = 0) {
        if ($counterIncrement == 0) {
            $urlString = preg_replace('#[^0-9a-z]+#i', '-', $productName . " " . $sellerName . " " . $locality);
        } else {
            $urlString = preg_replace('#[^0-9a-z]+#i', '-', $productName . " " . $sellerName . " " . $counterIncrement . " " . $locality);
        }
        $url = $sellerCity . "-" . $urlString;
        $url = strtolower($url);
        return $url;
    }

    /**
     * Check if url is already exist with another product
     * Return the counter increment if it exist
     * @param int $productId
     * @param string $url
     * @param int $initialCounter
     * @return int
     */
    public function checkProductUrlForDuplicates($productId, $url, $initialCounter) {
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('entity_type', 'bakeway-product')
                ->addFieldToFilter('request_path', $url)
                ->addFieldToFilter('target_path', $url);
        $counterIncrement = $initialCounter;
        foreach ($urlCollectionData as $value) {
            $entityId = $value->getEntityId();
            if ($entityId != $productId) {
                $counterIncrement++;
            }
        }
        return $counterIncrement;
    }

    /**
     * @param int $sellerId
     * @return void
     */
    public function createSellerAllProductUrls($sellerId) {
        $collection = $this->productFactory->create()
                ->getCollection()
                ->addFieldToSelect('mageproduct_id')
                ->addFieldToFilter('seller_id', $sellerId);
        foreach ($collection as $marketplaceProduct) {
            $product = $this->objectManager
                    ->create('Magento\Catalog\Model\Product')
                    ->load($marketplaceProduct->getData('mageproduct_id'));
            /**
             * Setting the product url
             */
            $urlKey = $this->createProductUrl($marketplaceProduct->getData('mageproduct_id'));
            $product->setUrlKey($urlKey);
            $product->save();
        }
        return;
    }

    /**
     * Get Seller SEO Url
     * @param int $sellerId
     * @return null|string
     */
    public function getSellerSeoUrl($sellerId) {
        $url = null;
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('entity_type', 'customer')
                ->addFieldToFilter('entity_id', $sellerId);
        foreach ($urlCollectionData as $collection) {
            $url = $collection->getData('request_path');
        }
        if ($url !== null) {
            $seller = $this->marketplaceHelper->getSellerDataBySellerId($sellerId)->getFirstItem();
            $cityId = $seller->getData('store_city');
            $sellerCity = $this->citiesHelper->getCityNameById($cityId);
            $cityString = preg_replace('#[^0-9a-z]+#i', '-', strtolower($sellerCity));
            $url = str_replace($cityString . "-", $cityString . "/", $url);
        }
        return $url;
    }

    /**
     * Get Product SEO Url
     * @param int $productId
     * @return null|string
     */
    public function getProductSeoUrl($productId) {
        $url = null;
        $urlCollectionData = $this->objectManager
                ->create('Magento\UrlRewrite\Model\UrlRewrite')
                ->getCollection()
                ->addFieldToFilter('entity_type', 'bakeway-product')
                ->addFieldToFilter('entity_id', $productId);
        foreach ($urlCollectionData as $collection) {
            $url = $collection->getData('request_path');
        }
        if ($url !== null) {
            $sellerCity = $this->getSellerCity($productId);
            if ($sellerCity !== null) {
                $cityString = preg_replace('#[^0-9a-z]+#i', '-', strtolower($sellerCity));

                $url = str_replace($cityString . "-", $cityString . "/", $url);
            }
        }
        return $url;
    }

    /*
     * return min product price
     * @param int $product
     */

    public function getMinproductPrice($product) {
        $_price = [];
        $_ChildProducts = $this->_configurableobj->getUsedProductCollection($product);
        $_ChildProducts
                ->setFlag('has_stock_status_filter', true)
                ->addAttributeToSelect($this->getCatalogConfig()->getProductAttributes())
                ->addFilterByRequiredOptions()
                ->addAttributeToFilter('status', self::PRODUCT_ENABLE_STATUS)
                ->setStoreId($product->getStoreId());
        foreach ($_ChildProducts as $simple_product) {
            /* check if product has special price */
            if ($simple_product->getSpecialPrice()):
                $_price[$simple_product->getId()] = $simple_product->getSpecialPrice();
            else:
                $_price[$simple_product->getId()] = $simple_product->getPrice();
            endif;
        }
 
        if (!empty($_price)):
            $data = array(
                "product_id" => array_search(min($_price), $_price),
                "min_price" => min($_price));
            return $data;
        else:
            return;
        endif;
    }

    /*
     * calclating min price with including tax
     * @param int $productId
     */

    public function getMinProductTaxPrice($productId) {

        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
                ->load($productId);

        $_incltaxPrice = $this->getProductTaxPrice($product);
        return $_incltaxPrice;
    }

}
