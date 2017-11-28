<?php
/**
 * Copyright © 2015 Bakeway. All rights reserved.
 */
namespace Bakeway\Deliveryrangeprice\Model\ResourceModel;

/**
 * Rangeprice resource
 */
class Rangeprice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('bakeway_delivery_rangeprice', 'id');
    }


}
