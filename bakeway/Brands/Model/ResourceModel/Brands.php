<?php
/**
 * Copyright © 2015 Bakeway. All rights reserved.
 */
namespace Bakeway\Brands\Model\ResourceModel;

/**
 * Commison resource
 */
class Brands extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('bakeway_brands', 'entity_id');
    }

  
}
