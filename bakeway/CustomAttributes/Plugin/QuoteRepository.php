<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */
namespace Bakeway\CustomAttributes\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\Quote\Item\CartItemPersister;
use Bakeway\CustomAttributes\Helper\Data as CustomAttributesHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;
use Bakeway\Vendorcontract\Model\ResourceModel\Vendorcontract\Collection as ContractCollection;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Bakeway\Deliveryrangeprice\Model\ResourceModel\Rangeprice\Collection as DeliveryRangeCollection;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class QuoteRepository
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CartItemPersister
     */
    protected $cartItemPersister;

    /**
     * @var CustomAttributesHelper
     */
    protected $helper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

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
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * QuoteRepository constructor.
     * @param RequestInterface $request
     * @param CartItemPersister $cartItemPersister
     * @param CustomAttributesHelper $helper
     * @param ProductRepositoryInterface $productRepository
     * @param ContractCollection $contractCollection
     * @param MarketplaceHelper $marketplaceHelper
     * @param DeliveryRangeCollection $deliveryRangeCollection
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        RequestInterface $request,
        CartItemPersister $cartItemPersister,
        CustomAttributesHelper $helper,
        ProductRepositoryInterface $productRepository,
        ContractCollection $contractCollection,
        MarketplaceHelper $marketplaceHelper,
        DeliveryRangeCollection $deliveryRangeCollection,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->request = $request;
        $this->cartItemPersister = $cartItemPersister;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->contractCollection = $contractCollection;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->deliveryRangeCollection = $deliveryRangeCollection;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Magento\Quote\Model\QuoteRepository $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return mixed
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSave(
        \Magento\Quote\Model\QuoteRepository $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\CartInterface $quote
    )
    {
        $extensionAttr = $quote->getExtensionAttributes();
        $deliveryTime = '';
        $deliveryType = '';
        if ($quote->getId() && $extensionAttr !== null) {
            $deliveryType = $extensionAttr->getDeliveryType();
            $deliveryTime = $extensionAttr->getDeliveryTime();
            $customerNotes = $extensionAttr->getCustomerNotes();
            $storeUniqueName = $extensionAttr->getStoreUniqueName();
            if ($deliveryType !== null) {
                $quote->setData('delivery_type', $deliveryType);
            }

            if ($deliveryTime !== null) {
                $deliveryTime = date('Y-m-d H:i:s', strtotime($deliveryTime));
                $quote->setData('delivery_time', $deliveryTime);
            }

            if ($customerNotes !== null) {
                $quote->setData('customer_notes', $customerNotes);
            }

            if ($storeUniqueName !== null) {
                $quote->setData('store_unique_name', $storeUniqueName);
            }

            $quote->save();
        }
        $result = $proceed($quote);
        /**
         * Code to save the quote item custom extension attributes created by bakeway
         */
        $lastItem = $quote->getLastAddedItem();
        $customMessage = '';
        $lastItemId = '';
        if ($quote->getItems() && $lastItem) {
            foreach ($quote->getItems() as $quoteItem) {
                $itemExtAttr = $quoteItem->getExtensionAttributes();
                if ($itemExtAttr !== null && $itemExtAttr->getCustomMessage() !== null) {
                    $customMessage = $itemExtAttr->getCustomMessage();
                    $quoteItemId = $quoteItem->getItemId();
                    if (!isset($quoteItemId)) {
                        $lastItemId = $lastItem->getId();
                    } else {
                        $lastItemId = $quoteItemId;
                    }
                }
            }
            if ($lastItem !== null & $customMessage !== '' && $lastItemId == $lastItem->getId()) {
                $lastItem->setData('custom_message', $customMessage);
                $lastItem->save();
            }
        }
        /** custom extension attribute code ends */
        if ($lastItem !== null) {
            $this->helper->applyQuoteValidations($quote, $deliveryType, $deliveryTime, $lastItem);
        } else {
            $this->helper->applyQuoteValidations($quote, $deliveryType, $deliveryTime);
        }


        $lastSellerId = 0;
        $sellerArray = [];
        if ($quote->getItems()) {
            foreach ($quote->getItems() as $quoteItem) {
                $sku = $quoteItem->getSku();
                $sellerId = $this->helper->getSellerIdBySku($sku);
                /**
                 * Single Seller validation
                 */
                if ($lastSellerId != 0) {
                    if ($sellerId != $lastSellerId) {
                        if (!in_array($sellerId, $sellerArray)) {
                            $sellerArray[] = $sellerId;
                        }
                    }
                } else {
                    $sellerArray[] = $sellerId;
                }
                $lastSellerId = $sellerId;
            }
            if (!empty($sellerArray) && count($sellerArray) > 1) {
                $lastEl = array_values(array_slice($sellerArray, -1))[0];
                foreach ($quote->getItems() as $quoteItem) {
                    $sku = $quoteItem->getSku();
                    $sellerId = $this->helper->getSellerIdBySku($sku);
                    $quoteItemId = $quoteItem->getItemId();
                    if ($sellerId != $lastEl) {
                        if (isset($quoteItemId)) {
                            $quoteItem->delete();
                        }
                    }
                    try {
                        $product = $this->productRepository->get($sku);
                        if ($product->getId()) {
                            $categoryIds = $product->getCategoryIds();
                            foreach ($categoryIds as $id) {
                                $category = $this->categoryRepository->get($id);
                                $name = $category->getName();
                                if (isset($name)) {
                                    $name = str_replace(' ', '', $name);
                                    $name =  preg_replace('/[^A-Za-z0-9]/', '', $name);
                                    if (strpos(strtolower($name), 'addon') !== false) {
                                        if (isset($quoteItemId)) {
                                            $quoteItem->delete();
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        return $result;
    }
}