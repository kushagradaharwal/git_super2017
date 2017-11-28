<?php

/**
 *
 * Copyright © 2015 Bakewaycommerce. All rights reserved.
 */

namespace Bakeway\ShopAvailability\Controller\Availability;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action {
    /*
     * \Bakeway\CommissionLog\Model\CommissionLogFactory
     */

    protected $commissionLogCollection;

    /*
     * \Magento\Customer\Model\Session
     */
    protected $customerSesssion;/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $productRepository;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Bakeway\CommissionLog\Model\CommissionLogFactory $commissionLogFactory, \Magento\Customer\Model\Session $customerSesssion
    , \Magento\Catalog\Api\ProductRepositoryInterface $productRepository) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->commissionLogCollection = $commissionLogFactory;
        $this->customerSesssion = $customerSesssion;
        $this->productRepository = $productRepository;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request) {
        $loginUrl = $this->_objectManager->get(
                        'Magento\Customer\Model\Url'
                )->getLoginUrl();

        if (!$this->customerSesssion->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Flush cache storage
     *
     */
    public function execute() {
        $this->resultPage = $this->resultPageFactory->create();
        return $this->resultPage;
    }

}
