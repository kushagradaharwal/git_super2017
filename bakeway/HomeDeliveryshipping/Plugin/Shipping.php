<?php

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bakeway\HomeDeliveryshipping\Plugin;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\FreeShippingInterface;

class Shipping {

    CONST DELIVERYMETHOD = "bakewayhomedelivery_bakewayhomedelivery";

    protected $request;
    protected $ship
    ;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var FreeShippingInterface
     */
    protected $freeShipping;

    /**
     * @var Bakeway\HomeDeliveryshipping\Helper\Data
     */
    protected $_homedeliveryhelper;

    /**
     * @var \Bakeway\Deliveryrangeprice\Model\RangepriceFactory
     */
    protected $rangepriceFactory;

    public function __construct(
    \Magento\Framework\App\Request\Http $request, \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, \Magento\Quote\Model\Quote\Address\Total $total, PriceCurrencyInterface $priceCurrency, FreeShippingInterface $freeShipping, \Bakeway\HomeDeliveryshipping\Helper\Data $datahelper, \Bakeway\Deliveryrangeprice\Model\RangepriceFactory $rangepriceFactory
    ) {
        $this->request = $request;
        $this->shippingAssignment = $shippingAssignment;
        $this->total = $total;
        $this->priceCurrency = $priceCurrency;
        $this->freeShipping = $freeShipping;
        $this->_homedeliveryhelper = $datahelper;
        $this->rangepriceFactory = $rangepriceFactory;
    }

    /**
     * @return \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public function aroundCollect(
    \Magento\Quote\Model\Quote\Address\Total\Shipping $subject, \Closure $proceed, \Magento\Quote\Model\Quote $quote, \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, \Magento\Quote\Model\Quote\Address\Total $total) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();
        $_QuoteDeliveryType = $quote->getDeliveryType();
        /*
         * * extension attribute
         * * calculation fee
         */
        $extensionAttributes = $address->getExtensionAttributes();
        $_setLatitude = $_setLongtitude = $_setProductsku = "";

        if (!empty($address->getExtensionAttributes())) {
            $extensionAttributes = $address->getExtensionAttributes();
            $_setLatitude = $extensionAttributes->getLatitude();
            $_setLongtitude = $extensionAttributes->getLongtitude();
            $_setProductsku = $extensionAttributes->getSku();
            $_sellerid = $this->_homedeliveryhelper->getSelleridFSku($_setProductsku);
            $_DeliveryStatus = $this->getSelleredeliveryStatus($_sellerid);


            if ($_setLongtitude && $_setLatitude && !empty($_DeliveryStatus)) {
                $key = $this->getMapKey();
                $_sellerid = $this->_homedeliveryhelper->getSelleridFSku($_setProductsku);
                $_selleraddress = $this->getSelleraddressData($_sellerid);
                $latitudeCust = $_setLatitude; //visitor lat
                $longitudeCust = $_setLongtitude; //visitor long
                $latitudeSeller = $_selleraddress['lat']; // seller lat
                $longitudeSeller = $_selleraddress['long']; //seller long
                $_AddressDistance = $this->_homedeliveryhelper->getDistance($latitudeCust, $longitudeCust, $latitudeSeller, $longitudeSeller, $key);
                $this->getSellerRange($_sellerid, $_AddressDistance);

                $_collection = $this->rangepriceFactory->create()->getCollection()
                        ->addFieldToFilter('is_active', 1)
                        ->addFieldToFilter('seller_id', $_sellerid);
                //echo "<pre>";print_r( $_collection->getData());    


                /* end */
                if ($method) {
                    if (!empty($_selleraddress['lat'] && !empty($_selleraddress['long']))) {

                        //foreach ($address->getAllShippingRates() as $rate) {
                        //if ($rate->getCode() == 'bakewayhomedelivery_bakewayhomedelivery') {
                        $store = $quote->getStore();
                        $amountPrice = $_AddressDistance;

                        $_Custshipping = $this->getSellerRange($_sellerid, $_AddressDistance);
                        if (!empty($_Custshipping)):
                            // $total->setTotalAmount('bakewayhomedelivery', $amountPrice);
                            //$total->setBaseTotalAmount('bakewayhomedelivery', $amountPrice);
                            $shippingDescription = self::DELIVERYMETHOD;
                            $address->setShippingDescription(trim($shippingDescription, ' -'));
                            $total->setBaseShippingAmount($_Custshipping);
                            $total->setShippingAmount($_Custshipping);
                            $total->setShippingDescription($address->getShippingDescription());
                        else:
                            $_ShippError = ["error" => "Shiiping is not available for this area"];
                            echo json_encode($_ShippError);
                            exit;
                        endif;
                        // break;
                        //}
                        //}
                    } else {
                        foreach ($address->getAllShippingRates() as $rate) {
                            if ($rate->getCode() == $method) {
                                $store = $quote->getStore();
                                $amountPrice = $this->priceCurrency->convert(
                                        $rate->getPrice(), $store
                                );
                                $total->setTotalAmount('bakewayhomedelivery', $amountPrice);
                                $total->setBaseTotalAmount('bakewayhomedelivery', $amountPrice);
                                $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                                $address->setShippingDescription(trim($shippingDescription, ' -'));
                                $total->setBaseShippingAmount($amountPrice);
                                $total->setShippingAmount($amountPrice);
                                $total->setShippingDescription($address->getShippingDescription());

                                break;
                            }
                        }
                    }
                    return $this;
                }
            }
        } else {


            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode() == $method) {
                    $store = $quote->getStore();
                    /* set shipping charge zero when only pickup option is avaliable */
                    if ($_QuoteDeliveryType == 'home'):
                        $_price = $rate->getPrice();
                    else:
                        $_price = 0;
                    endif;
                    $amountPrice = $this->priceCurrency->convert(
                            $_price, $store
                    );
                    /* $total->setTotalAmount('bakewayhomedelivery', $amountPrice);
                      $total->setBaseTotalAmount('bakewayhomedelivery', $amountPrice);
                      $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                      $address->setShippingDescription(trim($shippingDescription, ' -'));
                      $total->setBaseShippingAmount($amountPrice);
                      $total->setShippingAmount($amountPrice);
                      $total->setShippingDescription($address->getShippingDescription());


                     */

                    $total->setTotalAmount($rate->getCode(), $amountPrice);
                    $total->setBaseTotalAmount($rate->getCode(), $rate->getPrice());
                    $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                    $address->setShippingDescription(trim($shippingDescription, ' -'));
                    $total->setBaseShippingAmount($rate->getPrice());
                    $total->setShippingAmount($amountPrice);
                    $total->setShippingDescription($address->getShippingDescription());


                    break;
                }
            }

            return $proceed;
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
                ->addFieldToFilter('seller_id', $id);
        foreach ($_collection as $_rangedata) {
            if ($_distance > $_rangedata['from_kms'] && $_distance <= $_rangedata['to_kms']) {
                return $_finalFess = $_rangedata['delivery_price'];

                break;
            }
            continue;
        }
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

    /*
     * check delivery is on or off for seller
     */

    public function getSelleredeliveryStatus($id) {
        return $this->_homedeliveryhelper->getSelleredelivery($id);
    }

}
