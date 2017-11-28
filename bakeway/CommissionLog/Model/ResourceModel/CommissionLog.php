<?php
/**
 * Copyright © 2015 Bakeway. All rights reserved.
 */
namespace Bakeway\CommissionLog\Model\ResourceModel;

/**
 * Commison resource
 */
class CommissionLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('bakeway_commissionlog', 'id');
    }

  
}
