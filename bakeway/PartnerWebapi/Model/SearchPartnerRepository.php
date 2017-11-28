<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PartnerWebapi
 * @author    Bakeway
 */

namespace Bakeway\PartnerWebapi\Model;

use \Bakeway\PartnerWebapi\Api\SearchPartnerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;
use \Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use \Bakeway\PartnerWebapi\Helper\Data as WebapiHelper;
use \Bakeway\Cities\Helper\Data as BakewayCityHelper;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use \Bakeway\ProductApi\Helper\Data as BakewayProductapiHelper;
use \Magento\Framework\Exception\NotFoundException;
use \Bakeway\Partnerlocations\Model\ResourceModel\Partnerlocations\CollectionFactory as LocationCollection;

class SearchPartnerRepository implements SearchPartnerInterface {

    const SEARCH_RADIUS = 5;
    const MAX_PAGE_SIZE = 20;
    const MAX_PAGE_NUMBER = 5;
    const PRODUCT_ATTR = ['cake_ingredients'];
    const PRODUCT_ATTR_PRODUCT_LISTING_FILTER = ['cake_flavour', 'cake_ingredients'];

    /**
     * @var BakewayCityHelper
     */
    protected $bakewayCityHelper;

    /**
     * @var SellerCollection
     */
    protected $sellerCollection;

    /**
     * @var MarketplaceHelper;
     */
    protected $marketplaceHelper;

    /**
     * @var WebapiHelper;
     */
    protected $webapiHelper;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollection;

    /**
     * @var BakewayProductapiHelper
     */
    protected $productapihelper;

    protected $locationsCollection;

    /**
     * SearchPartnerRepository constructor.
     * @param SellerCollection $sellerCollection
     * @param MarketplaceHelper $marketplaceHelper
     * @param WebapiHelper $webapiHelper
     * @param BakewayCityHelper $bakewayCityHelper
     * @param ProductCollectionFactory $productCollection
     * @param BakewayProductapiHelper $productapihelper
     * @param LocationCollection $locationsCollection
     */
    public function __construct(
        SellerCollection $sellerCollection,
        MarketplaceHelper $marketplaceHelper,
        WebapiHelper $webapiHelper,
        BakewayCityHelper $bakewayCityHelper,
        ProductCollectionFactory $productCollection,
        BakewayProductapiHelper $productapihelper,
        LocationCollection $locationsCollection
    ) {
        $this->sellerCollection = $sellerCollection;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->webapiHelper = $webapiHelper;
        $this->bakewayCityHelper = $bakewayCityHelper;
        $this->productCollection = $productCollection;
        $this->productapihelper = $productapihelper;
        $this->locationsCollection = $locationsCollection;
    }

    /**
     * Get Partner List.
     *
     * @api
     * @param string|null $city
     * @param string|null $lat
     * @param string|null $long
     * @param string|null $deliverydate
     * @param string|null $searchterm
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getSearchList($city = null, $lat = null, $long = null, $deliverydate = null, $searchterm = null, \Magento\Framework\Api\SearchCriteria $searchCriteria = null) {
        return $this->partnerSearch(
                        $city, $lat, $long, $deliverydate, $searchterm, $searchCriteria
        );
    }

    /**
     * @param string|null $city
     * @param string|null $lat
     * @param string|null $long
     * @param string|null $deliverydate
     * @param string|null $searchterm
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @param bool $autocomplete
     * @return array
     */
    public function partnerSearch($city = null, $lat = null, $long = null, $deliverydate = null, $searchterm = null, \Magento\Framework\Api\SearchCriteria $searchCriteria = null, $autocomplete = false) {
        $result = [];

        $deliveryDate = date('Y-m-d', strtotime($deliverydate));
        $today = date('Y-m-d', strtotime('today'));

        try{
            if ($deliverydate === null || $today > $deliveryDate) {
                return $result;
            }

            $cityId = $this->bakewayCityHelper->getCityIdByName($city);

//            $collection = $this->sellerCollection->create();
//
//            if ($autocomplete === false) {
//                $collection->addFieldToSelect(['entity_id', 'seller_id', 'shop_title', 'bakery_type', 'store_street_address',
//                    'shop_url', 'company_description', 'contact_number', 'business_name', 'store_city',
//                    'contact_email', 'store_city', 'store_locality_area', 'average_ratings', 'shop_open_timing', 'shop_close_timing',
//                    'advanced_order_intimation_time', 'delivery_time', 'known_for', 'store_highlights',
//                    'merchant_name', 'business_name', 'banner_pic', 'logo_pic', 'delivery',
//                    'meta_keyword', 'meta_description',  'is_pickup'
//                ]);
//            } else {
//                $collection->addFieldToSelect(['seller_id', 'shop_title', 'store_locality_area', 'logo_pic']);
//                $collection->removeFieldFromSelect('entity_id');
//                $collection->getSelect()->limit(10);
//            }
//
//
//            if ($cityId !== false) {
//                $collection->addFieldToFilter('store_city', $cityId);
//            } else {
//                $collection->addFieldToFilter('store_city', 0);
//            }
//
//            $collection->addFieldToFilter('userdata_shop_operatational_status', 0);
//            $collection->addFieldToFilter('is_live_ready', 1);
            //$collection->addFieldToFilter('is_conglomerate', 0);
            /**
             * Code to calculate the distance between store and the delivery area
             * Sorted the stores in ASC order of distance
             */
//            if ($lat != '' && $long != '') {
//                $collection->getSelect()->columns(
//                    [
//                        'distance' => new \Zend_Db_Expr("ST_Distance_Sphere(POINT(" . $lat . ", " . $long . "), store_geo_point)/1000")
//                    ]);
//                $collection->setOrder('distance', 'ASC');
//            }

            /**
             * Added condition to search on searchterm
             * shop_url, shop_title, business_name, merchant_name considered
             */
//            if ($searchterm != '') {
//                $collection->addFieldToFilter(['shop_url', 'shop_title', 'business_name', 'merchant_name'], [['like' => "%$searchterm%"], ['like' => "%$searchterm%"], ['like' => "%$searchterm%"], ['like' => "%$searchterm%"]]);
//            }

            /**
             * If no specific bakery get searched
             * Fetching hyper local bakeries within predefined radius currently 10kms
             */
//            if ($lat != '' && $long != '' && $searchterm == '' && $autocomplete === false) {
//                $collection->getSelect()->where(new \Zend_Db_Expr("MBRContains (
//                    LineString (
//                        Point (
//                            $lat + " . self::SEARCH_RADIUS . " / ( 111.1 / COS(RADIANS($long))),
//                            $long + " . self::SEARCH_RADIUS . " / 111.1
//                        ),
//                        Point (
//                            $lat - " . self::SEARCH_RADIUS . " / ( 111.1 / COS(RADIANS($long))),
//                            $long - " . self::SEARCH_RADIUS . " / 111.1
//                        )
//                     ),
//                     store_geo_point
//                )"));
//            }

            /**
             * Added filters passed in the search criteria to collection
             */
//            if ($searchCriteria != null && $autocomplete === false) {
//                $prodFields = [];
//                $prodConditions = [];
//                foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
//                    $fields = [];
//                    $conditions = [];
//                    foreach ($filterGroup->getFilters() as $filter) {
//                        $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
//                        $filterField = $filter->getField();
//                        $filterValue = $filter->getValue();
//                        $conditions[] = [$condition => $filterValue];
//                        $fields[] = $filterField;
//                        if (in_array($filter->getField(), self::PRODUCT_ATTR)) {
//                            $prodFields[] = ['attribute' => $filter->getField(), $condition => $filter->getValue()];
//                        } else {
//                            $filterField = $filter->getField();
//                            $filterValue = $filter->getValue();
//                            /**
//                             * Check delivery filter
//                             */
//                            if ($filterField == WebapiHelper::FILTER_DELIVERY_CODE) {
//                                switch ($filterValue) {
//                                    case WebapiHelper::DELIVERY_OPTION_PICKUP :
//                                        $filterField = 'is_pickup';
//                                        $filterValue = '1';
//                                        break;
//                                    case WebapiHelper::DELIVERY_OPTION_DELIVERY :
//                                        $filterField = 'delivery';
//                                        $filterValue = '1';
//                                        break;
//                                    case WebapiHelper::DELIVERY_OPTION_BOTH :
//                                        $filterField = 'delivery';
//                                        $filterValue = '1';
//                                        $collection->addFieldToFilter('is_pickup', ['eq' => '1']);
//                                        break;
//                                }
//                                $conditions[] = [$condition => $filterValue];
//                                $fields[] = $filterField;
//                            } else {
//                                $conditions[] = [$condition => $filterValue];
//                                $fields[] = $filterField;
//                            }
//                        }
//                    }
//                    if ($fields) {
//                        $collection->addFieldToFilter($fields, $conditions);
//                    }
//                }
//                if ($searchCriteria->getPageSize() < self::MAX_PAGE_SIZE) {
//                    $collection->setPageSize($searchCriteria->getPageSize());
//                    $result['page_size'] = $searchCriteria->getPageSize();
//                } else {
//                    $collection->setPageSize(self::MAX_PAGE_SIZE);
//                    $result['page_size'] = self::MAX_PAGE_SIZE;
//                }
//
//                if ($searchCriteria->getCurrentPage() > self::MAX_PAGE_NUMBER) {
//                    $collection->setCurPage(self::MAX_PAGE_NUMBER);
//                    $result['current_page'] = self::MAX_PAGE_NUMBER;
//                } else {
//                    $collection->setCurPage($searchCriteria->getCurrentPage());
//                    $result['current_page'] = $searchCriteria->getCurrentPage();
//                }

                /**
                 * applying product filters
                 */
                /*if (!empty($prodFields)) {
                    $sellerIds = $this->applyProductFiltersOnBakery($prodFields, $prodConditions);
                    $collection->addFieldToFilter('seller_id', ['in' => $sellerIds]);
                }*/

                /**
                 * apply date filters ie bakeries available on the date provided
                 */
//                if ($deliverydate !== null) {
//                    $sellerIds = $this->webapiHelper->getClosedBakeriesByDate($deliverydate);
//                    if (!empty($sellerIds)) {
//                        $collection->addFieldToFilter('seller_id', ['nin' => $sellerIds]);
//                    }
//                }
//            }
            $locationsResult = $this->multipleLocationSearch(
                $cityId,
                $lat,
                $long,
                $deliverydate,
                $searchterm,
                $searchCriteria
            );

            //$result['total_count'] = $collection->getSize() + $locationsResult['total_count'];
            $result['total_count'] = $locationsResult['total_count'];
            $result['current_page'] = $locationsResult['current_page'];
            $result['page_size'] = $locationsResult['page_size'];
            $partners = [];
//            $i = 0;
//            foreach ($collection as $seller) {
//                $partners[$i] = $seller->getData();
//
//                $logo = $seller->getLogoPic() != '' ? $seller->getLogoPic() : "noimage.png";
//                $banner = $seller->getBannerPic() != '' ? $seller->getBannerPic() : "banner-image.png";
//
//                /** Adding SEO URL */
//                $url = $this->productapihelper->getSellerSeoUrl($seller->getSellerId());
//                $partners[$i]['seo_url'] = $url;
//
//                if ($autocomplete === false) {
//                    $media = [];
//                    $media['logo_pic'] = $this->getImageArray($logo);
//                    $media['banner_pic'] = $this->getImageArray($banner);
//                    $partners[$i]['media'] = $media;
//
//                    /** adding city value */
//                    $partners[$i]['store_city'] = $this->bakewayCityHelper->getCityNameById($seller->getStoreCity());
//                } else {
//                    $partners[$i]['small_image'] = $this->webapiHelper->resize($logo, 25, 25);
//                }
//
//                $i++;
//            }
            $partners = array_merge($partners, $locationsResult['partners']);
            usort($partners, function ($partnersA, $partnersB) {
                return $partnersA['distance'] <=> $partnersB['distance'];
            });
            if ($autocomplete === false) {
                $result['partners'] = $partners;
                return json_decode(json_encode($result, false));
            } else {
                return json_decode(json_encode($partners, false));
            }
        } catch (Exception $e) {
            throw new NotFoundException(__('Something went wrong please try again later.'));
        }

    }

    public function multipleLocationSearch(
        $cityId = null,
        $lat = null,
        $long = null,
        $deliverydate = null,
        $searchterm = null,
        \Magento\Framework\Api\SearchCriteria $searchCriteria = null
    ) {
        $result = [];
        $collection = $this->locationsCollection->create();

        $collection->addFieldToSelect(['seller_id','store_unique_name','store_locality_area','store_latitude','store_longitude','store_street_address']);
        if ($cityId !== false) {
            $collection->addFieldToFilter('store_city', $cityId);
        } else {
            $collection->addFieldToFilter('store_city', 0);
        }
        $collection->addFieldToFilter('is_active', 1);
        /**
         * Code to calculate the distance between store and the delivery area
         * Sorted the stores in ASC order of distance
         */
        if ($lat != '' && $long != '') {
            $collection->getSelect()->columns(
                [
                    'distance' => new \Zend_Db_Expr("ST_Distance_Sphere(POINT(" . $lat . ", " . $long . "), locations_store_geo_point)/1000")
                ]);
            $collection->setOrder('distance', 'ASC');
        }
        /**
         * Fetching hyper local bakeries within predefined radius currently 10kms
         */
        if ($lat != '' && $long != '') {
            $collection->getSelect()->where(new \Zend_Db_Expr("MBRContains (
                    LineString (
                        Point (
                            $lat + " . self::SEARCH_RADIUS . " / ( 111.1 / COS(RADIANS($long))),
                            $long + " . self::SEARCH_RADIUS . " / 111.1
                        ),
                        Point (
                            $lat - " . self::SEARCH_RADIUS . " / ( 111.1 / COS(RADIANS($long))),
                            $long - " . self::SEARCH_RADIUS . " / 111.1
                        )
                     ),
                     locations_store_geo_point
                )"));
        }

        $collection->getSelect()->joinLeft(
            ['mp_udata' => $collection->getTable('marketplace_userdata')],
            'main_table.seller_id=mp_udata.seller_id',
            ['shop_title', 'bakery_type',
                'shop_url', 'company_description', 'contact_number', 'business_name', 'store_city',
                'contact_email', 'store_city', 'average_ratings', 'shop_open_timing', 'shop_close_timing',
                'advanced_order_intimation_time', 'delivery_time', 'known_for', 'store_highlights',
                'merchant_name', 'business_name', 'banner_pic', 'logo_pic', 'delivery',
                'meta_keyword', 'meta_description',  'is_pickup','is_conglomerate']
        );

        $collection->getSelect()->where('mp_udata.is_seller=?', 1);
        $collection->getSelect()->where('mp_udata.is_live_Ready=?', 1);
        $collection->getSelect()->where('mp_udata.userdata_shop_operatational_status=?', 0);

        /**
         * Added condition to search on searchterm
         * shop_url, shop_title, business_name, merchant_name considered
         */
        if ($searchterm != '' && $searchterm !== null) {
            $collection->getSelect()->where('mp_udata.business_name LIKE "%'.$searchterm.'%"');
        }

        if ($searchCriteria != null) {
            foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                foreach ($filterGroup->getFilters() as $filter) {
                    $filterField = $filter->getField();
                    $filterValue = $filter->getValue();
                    if ($filterField == 'bakery_type') {
                        $collection->getSelect()->where('mp_udata.bakery_type=?', $filterValue);
                    }
                    if ($filterField == 'business_name') {
                        $collection->getSelect()->where('mp_udata.business_name LIKE "%'.$filterValue.'%"');
                    }
                }
            }
        }

        if ($searchCriteria->getPageSize() < self::MAX_PAGE_SIZE) {
            $collection->setPageSize($searchCriteria->getPageSize());
            $result['page_size'] = $searchCriteria->getPageSize();
        } else {
            $collection->setPageSize(self::MAX_PAGE_SIZE);
            $result['page_size'] = self::MAX_PAGE_SIZE;
        }

        if ($searchCriteria->getCurrentPage() > self::MAX_PAGE_NUMBER) {
            $collection->setCurPage(self::MAX_PAGE_NUMBER);
            $result['current_page'] = self::MAX_PAGE_NUMBER;
        } else {
            $collection->setCurPage($searchCriteria->getCurrentPage());
            $result['current_page'] = $searchCriteria->getCurrentPage();
        }
        /**
         * apply date filters ie bakeries available on the date provided
         */
        if ($deliverydate !== null) {
            $sellerIds = $this->webapiHelper->getClosedBakeriesByDate($deliverydate);
            if (!empty($sellerIds)) {
                $collection->addFieldToFilter('mp_udata.seller_id', ['nin' => $sellerIds]);
            }
        }
        $result['total_count'] = $collection->getSize();
        $partners = [];
        $i = 0;
        foreach ($collection as $seller) {
            $partners[$i] = $seller->getData();

            $logo = $seller->getLogoPic() != '' ? $seller->getLogoPic() : "noimage.png";
            $banner = $seller->getBannerPic() != '' ? $seller->getBannerPic() : "banner-image.png";

            /** Adding SEO URL */
            $url = $this->productapihelper->getSellerSeoUrl($seller->getSellerId());
            $isConglomerate = $seller->getData('is_conglomerate');
            if ($isConglomerate == 1) {
                $partners[$i]['seo_url'] = $url.'?store='.$seller->getData('store_unique_name');
            } else {
                $partners[$i]['seo_url'] = $url;
            }

            $media = [];
            $media['logo_pic'] = $this->getImageArray($logo);
            $media['banner_pic'] = $this->getImageArray($banner);
            $partners[$i]['media'] = $media;

            /** adding city value */
            $partners[$i]['store_city'] = $this->bakewayCityHelper->getCityNameById($seller->getStoreCity());

            $i++;
        }

        $result['partners'] = $partners;
        return $result;
    }

    /**
     * @param string $image
     * @return array
     */
    public function getImageArray($image) {
        $result = [];
        $result["small"] = $this->webapiHelper->resize($image, 25, 25);
        $result["thumb"] = $this->webapiHelper->resize($image, 150, 150);
        $result["large"] = $this->webapiHelper->resize($image);
        return $result;
    }

    /**
     * Get Autocomplete List.
     *
     * @api
     * @param string|null $city
     * @param string|null $lat
     * @param string|null $long
     * @param string|null $deliverydate
     * @param string|null $searchterm
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getSearchAutoCompleteList($city = null, $lat = null, $long = null, $deliverydate = null, $searchterm = null) {
        return $this->partnerSearch(
                        $city, $lat, $long, $deliverydate, $searchterm, null, true
        );
    }

    /**
     * Get Partner Details.
     * @param int $vendorId
     * @param string|null $store
     * @return array
     */
    public function getPartnerDetails($vendorId, $store=null) {
        $partners = [];
        $collection = $this->sellerCollection->create()
                ->addFieldToSelect(['entity_id', 'seller_id', 'shop_title', 'bakery_type', 'store_street_address',
                    'shop_url', 'company_description', 'contact_number', 'business_name', 'store_city',
                    'contact_email', 'store_city', 'store_locality_area', 'average_ratings', 'shop_open_timing',
                    'shop_close_timing','advanced_order_intimation_time', 'delivery_time', 'known_for',
                    'store_highlights', 'is_pickup', 'merchant_name', 'business_name', 'banner_pic', 'logo_pic',
                    'delivery','shop_open_AMPM', 'shop_close_AMPM','meta_keyword', 'meta_description',
                    'is_conglomerate'
                ])
                ->addFieldToFilter('seller_id', $vendorId);


        if (count($collection) > 0) {
            foreach ($collection as $seller) {
                $partners[0] = $seller->getData();

                $isConglomerate = $seller->getData('is_conglomerate');
                /** Adding SEO URL */
                $url = $this->productapihelper->getSellerSeoUrl($seller->getSellerId());

                if ($isConglomerate == 1) {
                    if ($store === null || trim($store) == '') {
                        return [];
                    } else {
                        $locationsColl = $this->locationsCollection->create()
                                        ->addFieldToSelect(['seller_id','store_unique_name','store_locality_area','store_street_address'])
                                        ->addFieldToFilter('seller_id',$vendorId)
                                        ->addFieldToFilter('store_unique_name',$store);
                        if ($locationsColl->count() > 0) {
                            $location = $locationsColl->getFirstItem();
                            $partners[0]['store_locality_area'] = $location->getData('store_locality_area');
                            $partners[0]['store_street_address'] = $location->getData('store_street_address');
                            $partners[0]['seo_url'] = $url.'?store='.$location->getData('store_unique_name');
                        } else {
                            return [];
                        }
                    }
                } else {
                    if ($store !== null || trim($store) != '') {
                        return [];
                    }
                    $partners[0]['seo_url'] = $url;
                }

                $partners[0]['categories'] = $this->productapihelper->getMainandAddoncategory();
                $logo = $seller->getLogoPic() != '' ? $seller->getLogoPic() : "noimage.png";
                $banner = $seller->getBannerPic() != '' ? $seller->getBannerPic() : "banner-image.png";
                $media = [];
                $media['logo_pic'] = $this->getImageArray($logo);
                $media['banner_pic'] = $this->getImageArray($banner);
                $partners[0]['media'] = $media;

                /** adding city value */
                $partners[0]['store_city'] = $this->bakewayCityHelper->getCityNameById($seller->getStoreCity());
            }
            return json_decode(json_encode($partners[0], false));
        } else {

            $partners = array("error" => 'no data found');
            return $partners;
        }
    }

    /**
     * Get Partner Filters
     * @return array
     */
    public function getPartnerFilters() {
        $result = $this->webapiHelper->getProductAttributeFilters(self::PRODUCT_ATTR);
        $deliveryFilter = $this->webapiHelper->getBakeryDeliveryFilter();
        $bakeryTypeFilter = $this->webapiHelper->getBakeryTypeFilter();
        array_push($result, $deliveryFilter);
        array_push($result, $bakeryTypeFilter);
        return json_decode(json_encode($result, false));
    }

    /**
     * Get Product Filters
     * @return array
     */
    public function getProductFilters() {
        $result = $this->webapiHelper->getProductAttributeFilters(self::PRODUCT_ATTR_PRODUCT_LISTING_FILTER);
        return json_decode(json_encode($result, false));
    }

    /**
     * @param $fields
     * @param $conditions
     * @return mixed
     */
    public function applyProductFiltersOnBakery($fields, $conditions) {
        $collection = $this->productCollection->create()
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter($fields);
        $collection->getSelect()->joinInner(['mark_prod' => $collection->getTable('marketplace_product')], 'e.entity_id=mark_prod.mageproduct_id', ['seller_id']);
        $collection->getSelect()->where('mark_prod.status=?', 1);
        $collection->getSelect()->group('seller_id');
        return $collection->getColumnValues('seller_id');
    }

}
