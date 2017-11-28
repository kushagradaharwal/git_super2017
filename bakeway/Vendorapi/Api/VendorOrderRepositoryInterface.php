<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Api;

/**
 * Seller Order interface.
 */

interface VendorOrderRepositoryInterface
{
    /**
     * Get Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrder($vendorId, $orderId);

    /**
     * Accept Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function acceptOrder($vendorId, $orderId);

    /**
     * Reject Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @param string|null $reason Order rejection reason
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function rejectOrder($vendorId, $orderId, $reason = null);

    /**
     * Get order statuses list for vendor.
     *
     * @param int|null $vendorId
     * @return array
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function getStatusList($vendorId=null);

    /**
     * Set Order status.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @param string $status_code The order status code
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setOrderStatus($vendorId, $orderId, $status_code);

    /**
     * Get Vendor Order List.
     *
     * @param int $vendorId
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getVendorOrderList($vendorId, \Magento\Framework\Api\SearchCriteria $searchCriteria = null);

    /**
     * Get Vendor Unpaid Orders List.
     *
     * @param int $vendorId
     * @return array
     */
    public function getVendorUnpaidOrderList($vendorId);

    /**
     * Get order rejected reasons list for vendor.
     *
     * @param int $vendorId
     * @return array
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function getRejectedReasonsList($vendorId);
}
