<?php

namespace Bakeway\Razorpay\Model;

use Razorpay\Api\Api;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Payment\Model\InfoInterface;
use Razorpay\Magento\Model\Config;
use Magento\Catalog\Model\Session;

/**
 * Class PaymentMethod
 * @package Razorpay\Magento\Model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod {

    const CHANNEL_NAME = 'Magento';
    const METHOD_CODE = 'razorpay';
    const CONFIG_MASKED_FIELDS = 'masked_fields';
    const CURRENCY = 'INR';
    
    const AWATING_CONFIRMATION = 'pending';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = false;

}
