<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PayoutsCalculation
 * @author    Bakeway
 */

namespace Bakeway\PayoutsCalculation\Model\ResourceModel\Bakewaytransaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;

/**
 * ResourceModel Bakeway payouts transactions collection
 */
class Collection extends DbAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bakeway\PayoutsCalculation\Model\Bakewaytransaction',
                    'Bakeway\PayoutsCalculation\Model\ResourceModel\Bakewaytransaction');
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
