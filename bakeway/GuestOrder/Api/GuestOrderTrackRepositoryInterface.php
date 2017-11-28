<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_GuestOrder
 * @author    Bakeway
 */

namespace Bakeway\GuestOrder\Api;

interface GuestOrderTrackRepositoryInterface
{
    /**
     * Get Guest Customer Order.
     *
     * @param string $trackingToken
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function trackOrder($trackingToken);
}