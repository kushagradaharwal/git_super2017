<?php
/**
 * Copyright © 2015 Bakeway . All rights reserved.
 */
namespace Bakeway\Import\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param  \Magento\Customer\Model\CustomerFactory
     */
    protected $_sellerFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        parent::__construct($context);
        $this->_sellerFactory = $customerFactory;
    }

    public function getidFromEmail($email)
    {
        $_Email = $_EntityId = [];

        $_Collection = $this->_sellerFactory->create()->getCollection()
            ->addAttributeToFilter('email', array("like" => '%' . $email . '%'));
        if (count($_Collection) > 0) {
            foreach ($_Collection as $_Collection1) {
                $_EntityId[] = $_Collection1['entity_id'];
            }

            return $_EntityId[0];
        } else {
            return "";
        }
    }

}