<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomerWebapi
 * @author    Bakeway
 */

namespace Bakeway\CustomerWebapi\Api;

/**
 * Customer Order interface.
 */

interface CustomerOrderRepositoryInterface
{
    /**
     * Get Customer Order.
     *
     * @param int $customerId
     * @param int $orderId The order ID.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrder($customerId, $orderId);

    /**
     * Get Customer Order List.
     *
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getCustomerOrderList($customerId, \Magento\Framework\Api\SearchCriteria $searchCriteria = null);

    /**
     * Get valid dates for order
     * @return array
     */
    public function getValidOrderDateList();

    /**
     * Get valid time for order
     * @param string $sku
     * @return array
     */
    public function getValidOrderTimeList($sku);
}
