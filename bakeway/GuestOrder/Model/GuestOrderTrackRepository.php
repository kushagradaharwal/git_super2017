<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_GuestOrder
 * @author    Bakeway
 */

namespace Bakeway\GuestOrder\Model;

use Bakeway\GuestOrder\Api\GuestOrderTrackRepositoryInterface as GuestOrderTrackInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Bakeway\Vendorapi\Model\OrderStatus as BakewayOrderStatuses;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Authorization\Model\UserContextInterface;

class GuestOrderTrackRepository implements GuestOrderTrackInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderCollection
     */
    protected $orderCollection;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * GuestOrderTrackRepository constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param OrderCollection $orderCollection
     * @param ScopeConfigInterface $scopeConfig
     * @param UserContextInterface $userContext
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        OrderCollection $orderCollection,
        ScopeConfigInterface $scopeConfig,
        UserContextInterface $userContext
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderCollection = $orderCollection;
        $this->scopeConfig = $scopeConfig;
        $this->userContext = $userContext;
    }

    /**
     * Get Guest Customer Order.
     *
     * @param string $trackingToken
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function trackOrder($trackingToken)
    {
        $customerId = $this->userContext->getUserId();
        $collection = $this->orderCollection->create()
                        ->addFieldToFilter('order_tracking_token', $trackingToken);

        $orderCount = $collection->count();

        if ($orderCount > 0 && $customerId == 0) {
            $orderId = $collection->getFirstItem()->getEntityId();
            $orderDetails = $this->orderRepository->get($orderId);
            $today = date("Y-m-d H:i:s");
            $orderStatus = $orderDetails->getStatus();
            $updatedAt = $orderDetails->getUpdatedAt();
            $numberOfDays = $this->scopeConfig->getValue('guest_order_settings/guest_order_general/guset_order_token_days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($numberOfDays == 0 || $numberOfDays == '') {
                $numberOfDays = 3;
            }
            $updatedAtPlusTwo = date('Y-m-d H:i:s', strtotime($updatedAt .' +'.$numberOfDays.' day'));
            
            if (
                $orderStatus != BakewayOrderStatuses::STATUS_PARTNER_REJECTED &&
                $orderStatus != 'complete' &&
                $orderStatus != 'canceled'
            ) {
                return $orderDetails;
            }

            if ($orderStatus == 'complete') {
                if ($today <= $updatedAtPlusTwo) {
                    return $orderDetails;
                } else {
                    throw NoSuchEntityException::singleField('trackingToken', $trackingToken);
                }
            }

        } else {
            throw NoSuchEntityException::singleField('trackingToken', $trackingToken);
        }
    }
}