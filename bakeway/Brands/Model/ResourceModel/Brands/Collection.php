<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bakeway\Brands\Model\ResourceModel\Brands;

/**
 * Commisons Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Bakeway\Brands\Model\Brands', 'Bakeway\Brands\Model\ResourceModel\Brands');
    }
}
