<?php

/**
 * Copyright © 2015 Bakeway. All rights reserved.
 */

namespace Bakeway\HomeDeliveryshipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Exception\LocalizedException;

class Homedelivery implements ObserverInterface {

    CONST DELIVERYMETHOD = "bakewayhomedelivery_bakewayhomedelivery";

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var registry
     */
    protected $_registry;

    /**
     * @var \Magento\Quote\Model\Quote\Address\TotalFactory
     */
    protected $totalFactory;

    /**
     * @param   \Bakeway\HomeDeliveryshipping\Model\Carrier
     */
    protected $carrier;

    /**
     * @param   \Magento\Quote\Model\Quote\Address\RateResult
     */
    protected $MethodFactory;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Bakeway\HomeDeliveryshipping\Helper\Data
     */
    protected $_homedeliveryhelper;

    /**
     * @var \Bakeway\Deliveryrangeprice\Model\RangepriceFactory
     */
    protected $rangepriceFactory;

    public function __construct(
    \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Registry $registry, \Magento\Quote\Model\Quote\Address\TotalFactory $totalFactory, \Bakeway\HomeDeliveryshipping\Model\Carrier $carrier, \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $method, PriceCurrencyInterface $priceCurrency, \Bakeway\HomeDeliveryshipping\Helper\Data $datahelper, \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->totalFactory = $totalFactory;
        $this->carrier = $carrier;
        $this->MethodFactory = $method;
        $this->priceCurrency = $priceCurrency;
        $this->_homedeliveryhelper = $datahelper;
        $this->rangepriceFactory = $rangepriceFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $shippingAssignment = $observer->getShippingAssignment();
        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();
        $quote = $observer->getQuote();
        $address = $shippingAssignment->getShipping()->getAddress();
        $errorMessage = [];
        $_QuoteDeliveryType = $quote->getDeliveryType();
        if ($_QuoteDeliveryType == 'home') {
            /* calling extension attributes start */
            $extensionAttributes = $address->getExtensionAttributes();
            $_setLatitude = $_setLongtitude = $_setProductsku = "";
            if (!empty($address->getExtensionAttributes())) {
                $extensionAttributes = $address->getExtensionAttributes();
                $_setLatitude = $extensionAttributes->getLatitude();
                $_setLongtitude = $extensionAttributes->getLongtitude();
                $_setProductsku = $extensionAttributes->getSku();
            }
            /* calling extension attributes end */

            /** @varw \Magento\Quote\Model\Quote\Address $address */
            foreach ($observer->getTotal() as $totel) {
                $addressTotal = $this->collectAddressTotals($quote, $address);
            }

            $address->collectShippingRates();

            $key = $this->getMapKey();
            $_sellerid = $this->_homedeliveryhelper->getSelleridFSku($_setProductsku);
            $_selleraddress = $this->getSelleraddressData($_sellerid);
            $_DeliveryStatus = $this->getSelleredeliveryStatus($_sellerid);
            if (!empty($_selleraddress['lat'] && !empty($_selleraddress['long']) && !empty($_DeliveryStatus))) {
                //tmp code
                $latitudeCust = $_setLatitude; //visitor lat
                $longitudeCust = $_setLongtitude; //visitor long
                $latitudeSeller = $_selleraddress['lat']; // seller lat
                $longitudeSeller = $_selleraddress['long']; //seller long
                //tmp code end
                $_AddressDistance = $this->_homedeliveryhelper->getDistance($latitudeCust, $longitudeCust, $latitudeSeller, $longitudeSeller, $key);
                $_checkMaxFees = $this->checkMaxSellerRange($_sellerid, $_AddressDistance);


                if (!empty($_checkMaxFees)) {
                    $_Custshipping = $this->getSellerRange($_sellerid, $_AddressDistance);
                    if (!empty($_Custshipping)):
                        foreach ($address->getAllShippingRates() as $rate) {
                            if ($rate->getCode() == self::DELIVERYMETHOD) {
                                $store = $quote->getStore();
                                /* function to return delivery fees calculation */
                                $_checkProduct = $this->_homedeliveryhelper->getSelleridFSku($_setProductsku);
                                if (!empty($_checkProduct)) {
                                    $rate->setPrice($_Custshipping);
                                }
                                $address->setPrice(10);
                                $observer->getTotal()->setTotalAmount($rate->getCode(), $_Custshipping);
                                $observer->getTotal()->setBaseTotalAmount($rate->getCode(), $_Custshipping);
                                $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                                $address->setShippingDescription(trim($shippingDescription, ' -'));
                                $observer->getTotal()->setBaseShippingAmount($_Custshipping);
                                $observer->getTotal()->setShippingAmount($_Custshipping);
                                $observer->getTotal()->setShippingDescription($address->getShippingDescription());
                                /*
                                 * set grand total and base grand total
                                 */
                                $_getSubTotal = $observer->getTotal()->getGrandTotal();
                                $observer->getTotal()->setGrandTotal($_getSubTotal + $_Custshipping);
                                $observer->getTotal()->setBaseGrandTotal($_getSubTotal + $_Custshipping);
                                /* function to return delivery fees calculation */
                                break;
                            }
                        }
                    else:
                        /*
                         * applying zero shipping when user select pick up option
                         */
                        foreach ($address->getAllShippingRates() as $rate) {
                            if ($rate->getCode() == $method) {
                                $store = $quote->getStore();
                                /* function to return delivery fees calculation */
                                $_price = 0;
                                $amountPrice = $this->priceCurrency->convert(
                                        $_price, $store
                                );
                                $observer->getTotal()->setTotalAmount($rate->getCode(), $amountPrice);
                                $observer->getTotal()->setBaseTotalAmount($rate->getCode(), $amountPrice);
                                $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                                $address->setShippingDescription(trim($shippingDescription, ' -'));
                                $observer->getTotal()->setBaseShippingAmount($amountPrice);
                                $observer->getTotal()->setShippingAmount($amountPrice);
                                $observer->getTotal()->setShippingDescription($address->getShippingDescription());
                                /* function to return delivery fees calculation */
                                break;
                            }
                        }


                    endif;
                } else {
                    header('HTTP/1.1 ' . '400' . 'Bad Request');
                    header('Content-type: application/json');
                    $response = array(
                        'message' => 'Delivery is not available for this area.'
                    );
                    echo json_encode($response);
                    exit;
                }
            } else {
                // throw new LocalizedException(__('Shiiping is not available for this area'));
            }
        } else {
            /*
             * applying zero shipping when user select pick up option
             */
            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode() == $method) {
                    $store = $quote->getStore();
                    /* function to return delivery fees calculation */
                    $_price = 0;
                    $amountPrice = $this->priceCurrency->convert(
                            $_price, $store
                    );
                    $observer->getTotal()->setTotalAmount($rate->getCode(), $amountPrice);
                    $observer->getTotal()->setBaseTotalAmount($rate->getCode(), $amountPrice);
                    $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                    $address->setShippingDescription(trim($shippingDescription, ' -'));
                    $observer->getTotal()->setBaseShippingAmount($amountPrice);
                    $observer->getTotal()->setShippingAmount($amountPrice);
                    $observer->getTotal()->setShippingDescription($address->getShippingDescription());
                    /* function to return delivery fees calculation */
                    break;
                }
            }
        }
        return;
    }

    /*
     * get matrix key
     */

    public function getMapKey() {
        return $this->_homedeliveryhelper->getKey('delivery_fee/delivery_fee/google_api_key');
    }

    /*
     * return seller lat and long value
     */

    public function getSelleraddressData($id) {
        $customerAddress = array();

        $customerObj = $this->_homedeliveryhelper->getSelleraddress($id);
        if (!empty($customerObj->getStoreLatitude())) {
            return array("lat" => $customerObj->getStoreLatitude(), "long" => $customerObj->getStoreLongitude());
        }
    }

    /**
     * @return $_finalFess
     *
     */
    public function getSellerRange($id, $_distance) {
        $_finalFess = "";
        $_collection = $this->rangepriceFactory->create()->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('delivery_deleted', 0)
                ->addFieldToFilter('seller_id', $id);
        foreach ($_collection as $_rangedata) {

            if ($_distance > $_rangedata['from_kms'] && $_distance <= $_rangedata['to_kms']) {
                return $_finalFess = $_rangedata['delivery_price'];

                break;
            }
            continue;
        }
    }

    /**
     * @check distance
     * return true when seller max delivery range is below than customer address range
     */
    public function checkMaxSellerRange($id, $_distance) {
        $_Maxdistance = array();
        $_collection = $this->rangepriceFactory->create()->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('delivery_deleted', 0)
                ->addFieldToFilter('seller_id', $id);

        foreach ($_collection as $_rangedata) {
            $_Maxdistance[] = $_rangedata['to_kms'];
        }

        if (!empty($_Maxdistance)):
            if (max($_Maxdistance) >= $_distance) {
                return true;
            } else {
                return false;
            }
        else:
            return false;
        endif;
    }

    /*
     * check delivery is on or off for seller
     */

    public function getSelleredeliveryStatus($id) {
        return $this->_homedeliveryhelper->getSelleredelivery($id);
    }

}
