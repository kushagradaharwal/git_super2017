<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomFee
 * @author    Bakeway
 */
namespace Bakeway\CustomFee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Custom fee config path
     */
    const CONFIG_CUSTOM_IS_ENABLED = 'customfee/customfee/status';
    const CONFIG_CUSTOM_FEE = 'customfee/customfee/customfee_amount';
    const CONFIG_CUSTOM_GST_PERCENT = 'customfee/customfee/customfee_gst_amount';
    const CONFIG_FEE_LABEL = 'customfee/customfee/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'customfee/customfee/minimum_order_amount';

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope);
        return $isEnabled;
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getCustomFee()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $gstPercent = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_GST_PERCENT, $storeScope);
        $fee = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, $storeScope);
        $fee = $fee + ($fee * ($gstPercent / 100));
        return round($fee,0);
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getFeeLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $feeLabel = $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL, $storeScope);
        return $feeLabel;
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmount()
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $MinimumOrderAmount = $this->scopeConfig->getValue(self::CONFIG_MINIMUM_ORDER_AMOUNT, $storeScope);
        return $MinimumOrderAmount;
    }

    /**
     * @return mixed
     */
    public function getCustomFeePercentage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $gstPercent = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_GST_PERCENT, $storeScope);
        return $gstPercent;
    }

    /**
     * Get custom fee
     *
     * @return mixed
     */
    public function getCustomFeeExclTax()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, $storeScope);
        return $fee;
    }
}
