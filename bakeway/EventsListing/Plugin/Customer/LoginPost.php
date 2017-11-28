<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bakeway\EventsListing\Plugin\Customer;

class LoginPost
{
    /**
     * @var $marketplacehelper
     */
    private $marketplacehelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Webkul\Marketplace\Helper\Data $marketplacehelper
    )
    {
        $this->_request = $context->getRequest();
        $this->_response = $context->getResponse();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultFactory = $context->getResultFactory();
        $this->marketplacehelper = $marketplacehelper;
    }

    public function afterExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $result)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($customerSession->isLoggedIn()) {
            $_CustId = $customerSession->getId();
            $_isexist = $this->marketplacehelper->isSellerCustomerExist($_CustId);
            if ($_isexist):
                $resultRedirect->setPath('marketplace/account/dashboard');
            else:
                $resultRedirect->setPath('customer/account');
            endif;
            return $resultRedirect;
        } else {
            $resultRedirect->setPath('marketplace/account/dashboard');
            return $resultRedirect;
        }
    }
}