<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomFee
 * @author    Bakeway
 */

namespace Bakeway\CustomFee\Plugin\Checkout\Model;

class ShippingInformationManagement {

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Bakeway\CustomFee\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Bakeway\CustomFee\Helper\Data $dataHelper
     */
    public function __construct(
    \Magento\Quote\Model\QuoteRepository $quoteRepository, \Bakeway\CustomFee\Helper\Data $dataHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
    \Magento\Checkout\Model\ShippingInformationManagement $subject, $cartId, \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        //$customFee = $addressInformation->getExtensionAttributes()->getFee();
        $quote = $this->quoteRepository->getActive($cartId);
        //if ($customFee) {
        $fee = $this->dataHelper->getCustomFee();
        $quote->setFee($fee);
        //} else {
        //$quote->setFee(NULL);
        //}
    }

}
