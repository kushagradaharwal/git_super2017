<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_GuestOrder
 * @author    Bakeway
 */

namespace Bakeway\GuestOrder\Model\Rewrite;


class Order extends \Magento\Sales\Model\Order {

    /**
     * Returns Order Token Number
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
     public function getTokenNumber()
    {
        return $this->getData('order_tracking_token');
    }

    
}
