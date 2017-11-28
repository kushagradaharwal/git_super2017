<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */
namespace Bakeway\Vendorapi\Model;


class OrderStatus
{
    /**
     * Bakeway Custom order statuses
     */
    const STATUS_PARTNER_ACCEPTED = 'bakeway_partner_accepted';

    const STATUS_PARTNER_REJECTED = 'bakeway_partner_rejected';

    const STATUS_ORDER_READY = 'bakeway_order_ready';

    const STATUS_ORDER_OUT_FOR_DELIVERY = 'bakeway_order_out_for_delivery';

    const STATUS_ORDER_COMPLETE = 'complete';

    /**
     * Get statuses for order which require vendor actions
     * @return array
     */
    public function actionStatusArray()
    {
        return ['bakeway_order_ready','bakeway_order_out_for_delivery','complete',
            'pending','canceled','bakeway_partner_accepted','bakeway_partner_rejected'];
    }
}