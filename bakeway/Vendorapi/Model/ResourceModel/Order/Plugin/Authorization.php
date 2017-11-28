<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Model\ResourceModel\Order\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Webkul\Marketplace\Helper\Data as SellerHelper;
use Bakeway\Vendorapi\Model\OrderStatus as BakewayOrderStatuses;

class Authorization
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $sellerHelper;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        SellerHelper $sellerHelper
    ) {
        $this->userContext = $userContext;
        $this->sellerHelper = $sellerHelper;
    }

    /**
     * Checks if order is allowed
     *
     * @param \Magento\Sales\Model\ResourceModel\Order $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $order
     * @param mixed $value
     * @param null|string $field
     * @return \Magento\Sales\Model\Order
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        \Magento\Sales\Model\ResourceModel\Order $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $order,
        $value,
        $field = null
    ) {
        $result = $proceed($order, $value, $field);
        if (!$this->isAllowed($order)) {
            throw NoSuchEntityException::singleField('orderId', $order->getId());
        }
        return $result;
    }

    /**
     * Checks if order is allowed for current customer
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isAllowed(\Magento\Sales\Model\Order $order)
    {
        $customerId = $this->userContext->getUserId();
        $seller = $this->sellerHelper->getSellerDataBySellerId($customerId)->load();
        $sellerCount = $seller->count();
        $isApproved = $seller->getFirstItem()->getIsSeller();


        /* This new condition is added for seller */
        if ($isApproved == 1 && $sellerCount > 0) {
            return true;
        } else {
            /* enabling for guest users */
            if (
                $order->getCustomerIsGuest() &&
                $order->getData('order_tracking_token') != null &&
                $order->getStatus() != BakewayOrderStatuses::STATUS_PARTNER_REJECTED &&
                $order->getStatus() != 'canceled'
            ) {
                return true;
            }
            return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
            ? $order->getCustomerId() == $this->userContext->getUserId()
            : true;
        }
    }
}
