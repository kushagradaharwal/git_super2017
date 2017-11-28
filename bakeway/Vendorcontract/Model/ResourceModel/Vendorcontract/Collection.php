<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcontract
 * @author    Bakeway
 */

namespace Bakeway\Vendorcontract\Model\ResourceModel\Vendorcontract;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;

/**
 * ResourceModel Vendorcontract data collection
 */
class Collection extends DbAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Bakeway\Vendorcontract\Model\Vendorcontract',
            'Bakeway\Vendorcontract\Model\ResourceModel\Vendorcontract');
    }


    /**
     * Add field filter to collection.
     *
     * @param array|string          $field
     * @param string|int|array|null $condition
     *
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }
}
