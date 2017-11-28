<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\CustomAttributes\Observer;

use Magento\Framework\Event\ObserverInterface;

class ControllerActionPredispatch implements ObserverInterface {

    const ROUTES_ARR = ['checkout'];
    const FULL_ACTION_NAME = [''];

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * ControllerActionPredispatch constructor.
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param  \Magento\Customer\Model\Session $session
     */
    public function __construct(
    \Magento\Framework\App\ResponseFactory $responseFactory, \Magento\Framework\UrlInterface $url, \Magento\Customer\Model\Session $session
    ) {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->customerSession = $session;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $redirect = false;
        $routeName = $observer->getRequest()->getRouteName();
        $actionName = $observer->getRequest()->getFullActionName();

        if ($actionName == "customer_account_edit" && $observer->getRequest()->getParam('changepass') !== "1") {
            $redirectUrl = $this->_url->getUrl('customer/account/edit/changepass/1/');
            $this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit();
        }

        if ($actionName == "marketplace_index_index" && $routeName == "marketplace") {
            if ($this->customerSession->isLoggedIn()) {
                $redirectUrl = $this->_url->getUrl('partner/account/dashboard');
            } else {
                $redirectUrl = $this->_url->getUrl('/');
            }
            $this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit();
        }

        if (in_array($routeName, self::ROUTES_ARR)) {
            $redirect = true;
        } elseif (in_array($actionName, self::FULL_ACTION_NAME)) {
            $redirect = true;
        }

        if ($redirect) {
            if ($this->customerSession->isLoggedIn()) {
                $redirectUrl = $this->_url->getUrl('partner/account/dashboard');
            } else {
                $redirectUrl = $this->_url->getUrl('/');
            }
            $this->_responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            exit();
        }
    }

}
