<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\AuthenticationException;
use Webkul\Marketplace\Helper\Data as SellerHelper;
use Magento\Integration\Model\CustomerTokenService;
use Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata\CollectionFactory as DeviceCollection;

class VendorTokenCreateBeforePlugin
{
    /**
     * Customer Account Service
     *
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $sellerHelper;

    /**
     * @var CustomerTokenService
     */
    protected $customerTokenService;

    /**
     * @var DeviceCollection
     */
    protected $deviceCollection;

    /**
     * Initialize service
     *
     * @param AccountManagementInterface $accountManagement
     * @param \Webkul\Marketplace\Helper\Data $sellerHelper
     * @param \Magento\Integration\Model\CustomerTokenService $customerTokenService
     * @param DeviceCollection $deviceCollection
     */
    public function __construct(
        AccountManagementInterface $accountManagement,
        SellerHelper $sellerHelper,
        CustomerTokenService $customerTokenService,
        DeviceCollection $deviceCollection
    ) {
        $this->accountManagement = $accountManagement;
        $this->sellerHelper = $sellerHelper;
        $this->customerTokenService = $customerTokenService;
        $this->deviceCollection = $deviceCollection;
    }

    /**
     * @param CustomerTokenService\Interceptor $interceptor
     * @param $username
     * @param $password
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeCreateCustomerAccessToken(
        \Magento\Integration\Model\CustomerTokenService\Interceptor $interceptor,
        $username,
        $password)
    {
        $customerDataObject = $this->accountManagement->authenticate($username, $password);

        if ($customerDataObject->getId()) {
            $seller = $this->sellerHelper->getSellerDataBySellerId($customerDataObject->getId())->load();
            $sellerCount = $seller->count();
            $isApproved = $seller->getFirstItem()->getIsSeller();

            if ($isApproved != 1 && $sellerCount > 0) {
                throw new AuthenticationException(__("Unauthorized user"));
            }

            if ($sellerCount > 0) {
                $this->customerTokenService->revokeCustomerAccessToken($customerDataObject->getId());
            }
        }
    }

    /**
     * @param CustomerTokenService\Interceptor $interceptor
     * @param $customerId
     */
    public function beforeRevokeCustomerAccessToken(
        \Magento\Integration\Model\CustomerTokenService\Interceptor $interceptor,
        $customerId)
    {
        $collection = $this->deviceCollection->create()
            ->addFieldToFilter('seller_id', $customerId);

        foreach ($collection as $deviceData) {
            $deviceData->delete();
        }
    }
}