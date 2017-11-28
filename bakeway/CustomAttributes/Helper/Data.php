<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\CustomAttributes\Helper;

use Webkul\Marketplace\Model\ProductFactory as MarketplaceProductFactory;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as MarketplaceProductCollectionFactory;
use Magento\Catalog\Model\ProductRepository as ProductRepository;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory as MarketplaceSellerCollectionFactory;
use Bakeway\PartnerWebapi\Helper\Data as PartnerWebApiHelper;
use Magento\Framework\Exception\LocalizedException;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Bakeway\Vendorcontract\Model\ResourceModel\Vendorcontract\Collection as ContractCollection;
use Bakeway\Deliveryrangeprice\Model\ResourceModel\Rangeprice\Collection as DeliveryRangeCollection;
use \DatePeriod;
use \DateTime;
use \DateInterval;
use Magento\Framework\Exception\NotFoundException;
/**
 * Bakeway CustomAttributes Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var MarketplaceProductFactory
     */
    protected $marketplaceProduct;

    /**
     * @var MarketplaceProductCollectionFactory
     */
    protected $marketplaceProductCollection;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var MarketplaceSellerCollectionFactory
     */
    protected $marketplaceSellerCollection;

    /**
     * @var PartnerWebApiHelper
     */
    protected $partnerApiHelper;

    /**
     * @var ContractCollection
     */
    protected $contractCollection;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var DeliveryRangeCollection
     */
    protected $deliveryRangeCollection;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param MarketplaceProductFactory $marketplaceProduct
     * @param MarketplaceProductCollectionFactory $marketplaceProductCollection
     * @param ProductRepository $productRepository
     * @param MarketplaceSellerCollectionFactory $marketplaceSellerCollection
     * @param PartnerWebApiHelper $partnerApiHelper
     * @param ContractCollection $contractCollection
     * @param MarketplaceHelper $marketplaceHelper
     * @param DeliveryRangeCollection $deliveryRangeCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        MarketplaceProductFactory $marketplaceProduct,
        MarketplaceProductCollectionFactory $marketplaceProductCollection,
        ProductRepository $productRepository,
        MarketplaceSellerCollectionFactory $marketplaceSellerCollection,
        PartnerWebApiHelper $partnerApiHelper,
        ContractCollection $contractCollection,
        MarketplaceHelper $marketplaceHelper,
        DeliveryRangeCollection $deliveryRangeCollection
    )
    {
        $this->marketplaceProduct = $marketplaceProduct;
        $this->marketplaceProductCollection = $marketplaceProductCollection;
        $this->productRepository = $productRepository;
        $this->marketplaceSellerCollection = $marketplaceSellerCollection;
        $this->partnerApiHelper = $partnerApiHelper;
        $this->contractCollection = $contractCollection;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->deliveryRangeCollection = $deliveryRangeCollection;
        parent::__construct($context);
    }

    /**
     * @param int $productId
     * @return bool
     */
    public function addToCartCheck($productId) {
        return $this->checkUnapprovedProduct($productId);
    }

    /**
     * @param int $productId
     * @return bool
     */
    public function checkUnapprovedProduct($productId) {
        $productCollection = $this->marketplaceProductCollection->create()
                    ->addFieldToFilter('mageproduct_id', $productId);

        foreach ($productCollection as $product) {
            if ($product->getStatus() != 1) {
                return true;
            }
        }

        return false;
    }

    public function getSellerIdBySku($sku) {
        $sellerId = false;
        $productId  = $this->getProductIdBySku($sku);
        if ($productId !== false) {
            $productCollection = $this->marketplaceProductCollection->create()
                ->addFieldToFilter('mageproduct_id', $productId);
            foreach ($productCollection as $product) {
                $sellerId = $product->getSellerId();
            }
        }
        return $sellerId;
    }

    public function getProductIdBySku($sku) {
        $product = $this->productRepository->get($sku);
        if ($product->getId()) {
            return $product->getId();
        }
        return false;
    }

    public function getShopTimingsBySku($sku) {
        $sellerId = $this->getSellerIdBySku($sku);
        $timingArray = [];
        if ($sellerId !== false) {
            $seller = $this->marketplaceSellerCollection->create()
                                ->addFieldToFilter('seller_id', $sellerId)
                                ->getFirstItem();
            if ($seller->getId()) {
                /**
                 * Shop permanently closed check.
                 */
                if ($seller->getData('userdata_shop_operatational_status') == 1) {
                    return $timingArray;
                }
                $shopOpenTime = $seller->getShopOpenTiming();
                $shopOpenAmPm = $seller->getData('shop_open_AMPM');
                if ($shopOpenAmPm == 1) {
                    $shopOpenAmPm = "AM";
                } elseif ($shopOpenAmPm == 2) {
                    $shopOpenAmPm = "PM";
                }
                $shopCloseTime = $seller->getShopCloseTiming();
                $shopCloseAmPm = $seller->getData('shop_close_AMPM');
                if ($shopCloseAmPm == 1) {
                    $shopCloseAmPm = "AM";
                } elseif ($shopCloseAmPm == 2) {
                    $shopCloseAmPm = "PM";
                }
//                $period = new DatePeriod(
//                    new DateTime($shopOpenTime." ".$shopCloseAmPm),
//                    new DateInterval('PT1H'),
//                    new DateTime($shopCloseTime." ".$shopOpenAmPm)
//                );
//                foreach ($period as $date) {
//                    $timingArray[] = $date->format("h A");
//                }
//                $dates = array();
                //$current = new \Datetime('now', new \DateTimezone("Asia/Kolkata")) ;
                //$current = strtotime('+1 hour', strtotime($current->format("h A")));
                $shopOpen = strtotime($shopOpenTime." ".$shopOpenAmPm);
//                if ($current > $shopOpen) {
//                    $shopOpen = $current;
//                }

                $shopClose = strtotime($shopCloseTime." ".$shopCloseAmPm);
                if ($shopClose <= $shopOpen) {
                    $shopClose = strtotime("+1 Day", $shopClose);
                }

                while ( $shopOpen <= $shopClose ) {

                    $timingArray[] = date('h A', $shopOpen);
                    $shopOpen = strtotime('+1 hour', $shopOpen);
                }
            }
        }
        return $timingArray;
    }

    public function checkItemForDateTime($sku, $deliveryDate) {
        $result = true;
        $availableTimings = $this->getShopTimingsBySku($sku);
        $deliveryTime = date('h A', strtotime($deliveryDate));
        $deliveryDate = date('Y-m-d', strtotime($deliveryDate));

        /**
         * Shop open close timings check
         */
        if (!in_array($deliveryTime, $availableTimings)) {
            $result = false;
        }

        $sellerId = $this->getSellerIdBySku($sku);

        /**
         * Shop operational dates check
         */
        if ($sellerId !== false) {
            $sellerIdsArray = $this->partnerApiHelper->getClosedBakeriesByDate($deliveryDate);

            if (in_array($sellerId, $sellerIdsArray)) {
                $result = false;
            }
        }
        return $result;
    }

    public function applyQuoteValidations(
        \Magento\Quote\Api\Data\CartInterface $quote,
        $deliveryType,
        $deliveryTime,
        $lastQuoteItem = null
    )
    {
        if ($lastQuoteItem !== null) {
            $this->validateQuoteItem($lastQuoteItem, $deliveryType, $deliveryTime, true);
        }
        /**
         * Code to check if quote item is valid to add in the quote
         */
        if ($quote->getItems()) {
            foreach ($quote->getItems() as $quoteItem) {
                $this->validateQuoteItem($quoteItem, $deliveryType, $deliveryTime, false);
            }
        }
    }

    /**
     * @param $quoteItem
     * @param $deliveryType
     * @param $deliveryTime
     * @param $isLastItem
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function validateQuoteItem(
        $quoteItem,
        $deliveryType,
        $deliveryTime,
        $isLastItem
    )
    {
        $currentDateTime = new \DateTime('now', new \DateTimezone("Asia/Kolkata"));
        $deliveryDateTime = new \DateTime(date('Y-m-d H:i:s', strtotime($deliveryTime)));
        /**
         * Past time validation
         */
        if ($deliveryType == '') {
            throw new LocalizedException(__('Delivery type not specified.'));
        } elseif ($deliveryTime == '') {
            throw new LocalizedException(__('Delivery/Pickup time not specified.'));
        } elseif ($currentDateTime->format('Y-m-d H:i:s') > $deliveryDateTime->format('Y-m-d H:i:s')) {
            throw new LocalizedException(__('You can not order for past time.'));
        }

        $sku = $quoteItem->getSku();
        try {
            $product = $this->productRepository->get($sku);
        } catch (\Exception $e) {
            throw new NotFoundException(__('Requested item not found in catalog'));
        }

        $sellerId = $this->getSellerIdBySku($sku);

        if ($sellerId === false) {
            throw new LocalizedException(__('Product is not associated with any seller'));
        }

        $isLiveReady = $this->marketplaceHelper->getIsLiveReady($sellerId);
        if ($isLiveReady === false) {
            $this->deleteLastItem($quoteItem, $isLastItem);
            throw new LocalizedException(__('Seller is not live ready'));
        }

        $seller = $this->marketplaceHelper->getSellerDataBySellerId($sellerId)->getFirstItem();

        /**
         * Permanently closed validation
         */
        $permanentlyClosed = $seller->getData('userdata_shop_operatational_status');
        if ($permanentlyClosed != 0) {
            $this->deleteLastItem($quoteItem, $isLastItem);
            throw new LocalizedException(__('Bakery is permanently closed.'));
        }

        /**
         * Between Contract validation
         */
        $inContract = $this->marketplaceHelper->getIsInContract($sellerId);
        if ($inContract === false) {
            $this->deleteLastItem($quoteItem, $isLastItem);
            throw new LocalizedException(__('Seller contract is not started'));
        }

        /**
         * Weekly off validation
         */
        $deliveryDate = date('Y-m-d H:i:s', strtotime($deliveryTime));
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        $day = date('l', strtotime($deliveryDate));
        $dayKey = array_search ($day, $days);
        $operationalDays = $seller->getData('userdata_operational_days');
        $operationalArray = unserialize($operationalDays);
        if (is_array($operationalArray) && $operationalArray[$dayKey] != 1) {
            $this->deleteLastItem($quoteItem, $isLastItem);
            throw new LocalizedException(__('Bakery is closed on selected date.'));
        }

        if (!is_array($operationalArray)) {
            $this->deleteLastItem($quoteItem, $isLastItem);
            throw new LocalizedException(__('Bakery does not defined any operational days.'));
        }

        /**
         * vacation validation
         */
        if (
            $seller->getData('userdata_shop_temporarily_u_from') != null &&
            $seller->getData('userdata_shop_temporarily_u_to') != null
        ) {
            $temporarilyFrom = date('Y-m-d', strtotime($seller->getData('userdata_shop_temporarily_u_from')));
            $temporarilyTo = date('Y-m-d', strtotime($seller->getData('userdata_shop_temporarily_u_to')));
            $deliveryDate = date('Y-m-d', strtotime($deliveryTime));
            if (($temporarilyFrom < $deliveryDate) && ($deliveryDate < $temporarilyTo)) {
                $this->deleteLastItem($quoteItem, $isLastItem);
                throw new LocalizedException(__('Bakery temporary unavailable on selected date.'));
            }
        }

        /**
         * Advance Order Intimation Time Validation
         */
        if ($product->getCustomAttribute('advance_order_intimation')) {
            $advanceOrderIntimation = $product->getCustomAttribute('advance_order_intimation')->getValue();
            if (isset($advanceOrderIntimation) && $advanceOrderIntimation > 0) {
                $cDate = new \DateTime($currentDateTime->format('Y-m-d H:i:s'));
                $dDate = new \DateTime($deliveryDateTime->format('Y-m-d H:i:s'));
                $interval= $dDate->diff($cDate);
                $hrDiff = ($interval->days * 24) + $interval->h;
                if ($advanceOrderIntimation > $hrDiff) {
                    $this->deleteLastItem($quoteItem, $isLastItem);
                    throw new LocalizedException(__('Advance order intimation time for this product is : '.$advanceOrderIntimation." Hrs"));
                }
            }
        }

        /**
         * Pickup Delivery validation
         */
        $isPickup = $seller->getData('is_pickup');
        $isDelivery = $seller->getData('delivery');
        if ($deliveryType == 'pickup') {
            if ($isPickup == 0) {
                $this->deleteLastItem($quoteItem, $isLastItem);
                throw new LocalizedException(__('Bakery does not provide pickup.'));
            }
        } elseif ($deliveryType == 'home') {
            if ($isDelivery == 0) {
                $this->deleteLastItem($quoteItem, $isLastItem);
                throw new LocalizedException(__('Bakery does not provide delivery.'));
            } else {
                $rangeCollection = $this->deliveryRangeCollection
                    ->addFieldToFilter('seller_id', $sellerId)
                    ->addFieldToFilter('is_active', 1)
                    ->addFieldToFilter('delivery_deleted', 0);
                if ($rangeCollection->count() <= 0) {
                    $this->deleteLastItem($quoteItem, $isLastItem);
                    throw new LocalizedException(__('Bakery does not have any delivery range defined.'));
                }
            }
        } else {
            $this->deleteLastItem($quoteItem, $isLastItem);
            throw new LocalizedException(__('Invalid delivery type.'));
        }

        /**
         * Shop is closed validation
         */
        if ($deliveryDateTime->format('Y-m-d') == $currentDateTime->format('Y-m-d')) {
            $shopOpenTime = $seller->getShopOpenTiming();
            $shopOpenAmPm = $seller->getData('shop_open_AMPM');
            if ($shopOpenAmPm == 1) {
                $shopOpenAmPm = "AM";
            } elseif ($shopOpenAmPm == 2) {
                $shopOpenAmPm = "PM";
            }
            $shopCloseTime = $seller->getShopCloseTiming();
            $shopCloseAmPm = $seller->getData('shop_close_AMPM');
            if ($shopCloseAmPm == 1) {
                $shopCloseAmPm = "AM";
            } elseif ($shopCloseAmPm == 2) {
                $shopCloseAmPm = "PM";
            }
            $current = new \Datetime('now', new \DateTimezone("Asia/Kolkata"));
            $current = strtotime('+1 hour', strtotime($current->format("h A")));
            $deliveryTimeAmPm = strtotime('now', strtotime($deliveryDateTime->format("h A")));
            $shopOpen = strtotime($shopOpenTime . " " . $shopOpenAmPm);
            $shopClose = strtotime($shopCloseTime . " " . $shopCloseAmPm);
            if (
                ($shopOpen <= $current && $current <= $shopClose) &&
                ($shopOpen <= $deliveryTimeAmPm && $deliveryTimeAmPm <= $shopClose)
            ) {
            } else {
                $this->deleteLastItem($quoteItem, $isLastItem);
                throw new LocalizedException(__('Bakery is closed for the day. Or does not provide order on selected time'));
            }
        }
    }

    /**
     * @param $quoteItem
     * @param $isLastItem
     * @return void
     */
    public function deleteLastItem($quoteItem, $isLastItem) {
        if ($isLastItem === true) {
            $quoteItem->delete();
        }
        return;
    }
}