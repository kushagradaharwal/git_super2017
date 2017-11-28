<?php

namespace Bakeway\Razorpay\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class Config {

    const KEY_ACTIVE = 'active';
    const KEY_PUBLIC_KEY = 'payment/razorpay/key_id';
    const KEY_SECRET_KEY = 'payment/razorpay/key_secret';
    const KEY_MERCHANT_NAME = 'payment/razorpay/merchant_name';
    const METHOD_CODE = 'razorpay';
    const CURRENCY = 'INR';
    const PENDING_PAYMENT = 'pending_payment';
    const AWATING_CONFIRMATION = 'pending';
    const ERROR_CODE = '402';
    const ERROR_SERVER_MESSAGE = 'Payment Required';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param  \Magento\Store\Model\StoreManagerInterf‌​ace
     */
    protected $storeManagerInterface;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
    ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * @return string
     */
    public function getMerchantName() {
        return $this->scopeConfig->getValue(self::KEY_MERCHANT_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return Razor pay key id
     */

    public function getKeyId() {
        return $this->scopeConfig->getValue(self::KEY_PUBLIC_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /*
     * @return Razor pay key id
     */

    public function getKeySecret() {
        return $this->scopeConfig->getValue(self::KEY_SECRET_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return current store id
     */
    public function getStoreId() {
        return $this->storeManagerInterface->getStore()->getId();
    }

    /**
     * @return bool
     */
    public function isActive() {
        return (bool) (int) $this->getConfigData(self::KEY_ACTIVE, $this->getStoreId);
    }

}
