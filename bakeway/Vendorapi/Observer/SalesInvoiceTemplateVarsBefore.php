<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use \Magento\Framework\ObjectManagerInterface;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Bakeway\Cities\Helper\Data as CitiesHelper;

class SalesInvoiceTemplateVarsBefore implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CitiesHelper
     */
    protected $citiesHelper;

    /**
     * SalesInvoiceTemplateVarsBefore constructor.
     * @param ObjectManagerInterface $objectManager
     * @param MarketplaceHelper $marketplaceHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param CitiesHelper $citiesHelper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        MarketplaceHelper $marketplaceHelper,
        ScopeConfigInterface $scopeConfig,
        CitiesHelper $citiesHelper
    ) {
        $this->objectManager = $objectManager;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->scopeConfig = $scopeConfig;
        $this->citiesHelper = $citiesHelper;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $bakewayPan = $this->scopeConfig->getValue('payouts_calculation/bakeway_account_details/bakeway_pan',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $bakewayGstin = $this->scopeConfig->getValue('payouts_calculation/bakeway_account_details/bakeway_gstin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $sellerId = null;
        $businessName = '';
        /** @var \Magento\Framework\App\Action\Action $controller */
        $transportObject = $observer->getData('transportObject');
        $order = $transportObject->getData('order');
        $collection = $this->objectManager->create('Webkul\Marketplace\Model\Orders')
                        ->getCollection()
                        ->addFieldToFilter('order_id', $order->getId());
        foreach ($collection as $mpOrder) {
            $sellerId = $mpOrder->getData('seller_id');
            $sellerColl = $this->marketplaceHelper->getSellerDataBySellerId($sellerId);
            if ($sellerColl->count() > 0) {
                $seller = $sellerColl->getFirstItem();
                $businessName = $seller->getData('business_name');
                $sellerAddress = $seller->getData('store_locality_area');
                $cityId = $seller->getData('store_city');
                $cityName = $this->citiesHelper->getCityNameById($cityId);
                $sellerAddress = $sellerAddress.', '.$cityName;
            }
        }
        $transportObject->setData('businessName', $businessName);
        $transportObject->setData('sellerAddress', $sellerAddress);
        $transportObject->setData('bakewayPan', $bakewayPan);
        $transportObject->setData('bakewayGstin', $bakewayGstin);
    }
}