<?php

/**
 * Copyright © 2015 Bakeway. All rights reserved.
 */

namespace Bakeway\HomeDeliveryshipping\Model;

use Magento\Framework\Exception\CommisonException;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Simplexml\Element;
use Magento\Ups\Helper\Config;
use Magento\Framework\Xml\Security;

class Carrier extends AbstractCarrierOnline implements CarrierInterface {

    const CODE = 'bakewayhomedelivery';
    protected $_code = self::CODE;
    protected $_request;
    protected $_result;
    protected $_baseCurrencyRate;
    protected $_xmlAccessRequest;
    protected $_localeFormat;
    protected $_logger;
    protected $configHelper;
    protected $_errors = [];
    protected $_isFixed = true;
    protected $registry;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory, \Psr\Log\LoggerInterface $logger, Security $xmlSecurity, \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory, \Magento\Shipping\Model\Rate\ResultFactory $rateFactory, \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory, \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory, \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory, \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory, \Magento\Directory\Model\RegionFactory $regionFactory, \Magento\Directory\Model\CountryFactory $countryFactory, \Magento\Directory\Model\CurrencyFactory $currencyFactory, \Magento\Directory\Helper\Data $directoryData, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Magento\Framework\Locale\FormatInterface $localeFormat, Config $configHelper, \Magento\Framework\Registry $registry, array $data = []
    ) {
        $this->_localeFormat = $localeFormat;
        $this->configHelper = $configHelper;
        $this->scopeConfig = $scopeConfig;
        $this->_registry = $registry;
        parent::__construct(
                $scopeConfig, $rateErrorFactory, $logger, $xmlSecurity, $xmlElFactory, $rateFactory, $rateMethodFactory, $trackFactory, $trackErrorFactory, $trackStatusFactory, $regionFactory, $countryFactory, $currencyFactory, $directoryData, $stockRegistry, $data
        );
    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request) {
        
    }

    public function getAllowedMethods() {
        
    }

    public function collectRates(RateRequest $request) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $addressInformation = $objectManager->create('Magento\Quote\Api\Data\AddressInterface');
        //$extAttributes = $addressInformation->getExtensionAttributes();
        //$selectedShipping = $extAttributes->getLatitude(); //get custom attribute data.
        $result = $this->_rateFactory->create();
        /* store shipping in session */
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('name'));
        /* Use method name */
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setCost(120);
        //$method->setPrice(10);
        $result->append($method);
        return $result;
    }

    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request) {
        return true;
    }

}
