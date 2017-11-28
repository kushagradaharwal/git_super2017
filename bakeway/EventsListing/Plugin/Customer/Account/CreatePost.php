<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bakeway\EventsListing\Plugin\Customer\Account;

use Magento\Framework\Registry;

class CreatePost
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    public function __construct(
        \Magento\Framework\UrlInterface $url
    )
    {
        $this->url = $url;
    }

    public function afterExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        $resultRedirect
    )
    {
        $resultRedirect->setUrl($this->url->getUrl('customer/account'));
        return $resultRedirect;
    }
}