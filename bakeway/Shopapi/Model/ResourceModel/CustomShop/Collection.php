<?php

namespace Bakeway\Shopapi\Model\ResourceModel\CustomShop;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 *
 * Shopapi Resource Model Collection\AbstractCollection
 * @author Bakeway
 */
class Collection extends AbstractCollection {

    /**
     * Initialize Resource Collection
     *
     * @return void
     */
    public function _construct() {
        $this->_init('Bakeway\Shopapi\Model\CustomShop', 'Bakeway\Shopapi\Model\ResourceModel\CustomShop');
    }

}
